<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()->orderBy('name')->get();
        return view('tests.index', compact('categories'));
    }

    public function start(Request $request)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $user = $request->user();

        // dacă există attempt activ, îl reluăm
        $activeAttempt = Attempt::query()
            ->where('user_id', $user->id)
            ->whereNull('finished_at')
            ->latest('created_at')
            ->first();

        if ($activeAttempt) {
            return redirect()->route('tests.show', $activeAttempt);
        }

        $categoryId = (int) $request->input('category_id');

        $questionIds = Question::query()
            ->where('category_id', $categoryId)
            ->inRandomOrder()
            ->limit(20)
            ->pluck('id');

        if ($questionIds->isEmpty()) {
            return back()->with('error', 'Categoria nu are încă întrebări.');
        }

        $attempt = null;

        DB::transaction(function () use ($user, $categoryId, $questionIds, &$attempt) {
            $attempt = Attempt::create([
                'user_id' => $user->id,
                'category_id' => $categoryId,
                'started_at' => now(),
                'finished_at' => null,
                'total_questions' => $questionIds->count(),
                'correct_count' => 0,
            ]);

            foreach ($questionIds as $qid) {
                AttemptAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $qid,
                    'ai_question_id' => null,
                    'selected' => null,
                    'is_correct' => false,
                ]);
            }
        });

        return redirect()->route('tests.show', $attempt);
    }

    public function show(Attempt $attempt, Request $request)
    {
        $this->authorizeAttempt($attempt, $request);

        if ($attempt->finished_at) {
            return redirect()->route('tests.result', $attempt);
        }

        $answers = $attempt->answers()
            ->with(['question', 'aiQuestion'])
            ->orderBy('id')
            ->get();

        $totalQuestions = $answers->count();
        $answeredCount = $answers->whereNotNull('selected')->count();

        // prima întrebare ne-răspunsă
        $current = $answers->firstWhere('selected', null);

        // dacă nu mai există, finalizează și mergi la rezultat (GET ok)
        if (!$current) {
            $attempt->update(['finished_at' => now()]);
            return redirect()->route('tests.result', $attempt);
        }

        $question = $current->question ?? $current->aiQuestion;
        if (!$question) abort(404);

        $currentIndex = $answeredCount + 1;
        $isLast = ($currentIndex >= max(1, $totalQuestions));

        // feedback overlay din sesiune (trimis de answer())
        $feedback = session('test_feedback');

        return view('tests.take', compact(
            'attempt',
            'question',
            'currentIndex',
            'totalQuestions',
            'isLast',
            'feedback'
        ));
    }

    /**
     * PREMIUM: feedback overlay pe aceeași pagină.
     * Salvăm feedback în session și redirect la tests.show (care arată următoarea întrebare).
     */
    public function answer(Attempt $attempt, Request $request)
    {
        $this->authorizeAttempt($attempt, $request);

        if ($attempt->finished_at) {
            return redirect()->route('tests.result', $attempt);
        }

        $data = $request->validate([
            'selected' => ['required', 'in:a,b,c,d'],
            'question_id' => ['required', 'integer'],
        ]);

        $selected = strtolower($data['selected']);
        $qid = (int) $data['question_id'];

        $answer = $attempt->answers()
            ->where(function ($q) use ($qid) {
                $q->where('question_id', $qid)
                  ->orWhere('ai_question_id', $qid);
            })
            ->with(['question', 'aiQuestion'])
            ->first();

        if (!$answer) abort(404);

        // nu permitem schimbare după ce a răspuns
        if (!is_null($answer->selected)) {
            return redirect()->route('tests.show', $attempt);
        }

        $q = $answer->question ?? $answer->aiQuestion;
        if (!$q) abort(404);

        $correctOpt = strtolower((string) ($q->correct ?? ''));
        $isCorrect = ($selected === $correctOpt);

        DB::transaction(function () use ($attempt, $answer, $selected, $isCorrect) {
            $answer->update([
                'selected' => $selected,
                'is_correct' => $isCorrect,
            ]);

            if ($isCorrect) {
                $attempt->increment('correct_count');
            }
        });

        // feedback pentru overlay (arată întrebarea anterioară, highlight corect/greșit)
        $feedback = [
            'prompt' => $q->prompt ?? '',
            'a' => $q->a ?? null,
            'b' => $q->b ?? null,
            'c' => $q->c ?? null,
            'd' => $q->d ?? null,
            'selected' => $selected,
            'correct' => $correctOpt,
            'isCorrect' => $isCorrect,
        ];

        // dacă s-a terminat testul, finalizăm acum, ca să fie consistent
        $remaining = $attempt->answers()->whereNull('selected')->count();
        if ($remaining === 0 && is_null($attempt->finished_at)) {
            $attempt->update(['finished_at' => now()]);
            // trimitem feedback și mergem la rezultat după overlay
            return redirect()
                ->route('tests.result', $attempt)
                ->with('test_feedback', $feedback)
                ->with('show_overlay_on_result', true);
        }

        return redirect()
            ->route('tests.show', $attempt)
            ->with('test_feedback', $feedback);
    }

    public function submit(Attempt $attempt, Request $request)
    {
        $this->authorizeAttempt($attempt, $request);

        if (!$attempt->finished_at) {
            $attempt->update(['finished_at' => now()]);
        }

        return redirect()->route('tests.result', $attempt);
    }

    public function result(Attempt $attempt, Request $request)
    {
        $this->authorizeAttempt($attempt, $request);

        $answers = $attempt->answers()
            ->with(['question', 'aiQuestion'])
            ->orderBy('id')
            ->get();

        $total = max(1, (int) ($attempt->total_questions ?? $answers->count()));
        $correct = (int) ($attempt->correct_count ?? 0);
        $percent = round(($correct / $total) * 100, 1);

        $feedback = session('test_feedback');
        $showOverlay = session('show_overlay_on_result', false);

        return view('tests.result', compact('attempt', 'answers', 'total', 'correct', 'percent', 'feedback', 'showOverlay'));
    }

    public function aiGenerate(Attempt $attempt, Request $request)
    {
        $this->authorizeAttempt($attempt, $request);

        // aici rămâne “mock” dacă nu ai încă logica AI reală
        return back()->with('status', 'AI: întrebare generată (mock).');
    }

    public function history(Request $request)
    {
        $categories = Category::query()->orderBy('name')->get();

        $status = $request->input('status', 'all');
        $categoryId = $request->input('category_id');

        $query = Attempt::query()
            ->where('user_id', $request->user()->id)
            ->with('category')
            ->latest('created_at');

        if ($categoryId) $query->where('category_id', $categoryId);

        if ($status === 'finished') $query->whereNotNull('finished_at');
        if ($status === 'active') $query->whereNull('finished_at');

        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->input('from'));
        if ($request->filled('to')) $query->whereDate('created_at', '<=', $request->input('to'));

        $attempts = $query->paginate(15)->withQueryString();

        return view('tests.history', compact('attempts', 'categories', 'status'));
    }

    private function authorizeAttempt(Attempt $attempt, Request $request): void
    {
        if ($attempt->user_id !== $request->user()->id) {
            abort(403);
        }
    }
}
