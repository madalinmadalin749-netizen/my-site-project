<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;

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
        $categoryId = (int) $request->input('category_id');

        // Dacă ai deja un attempt activ (nefinalizat), îl reluăm
        $activeAttempt = Attempt::query()
            ->where('user_id', $user->id)
            ->whereNull('finished_at')
            ->latest('created_at')
            ->first();

        if ($activeAttempt) {
            return redirect()->route('tests.show', $activeAttempt);
        }

        // Selectăm întrebări din categorie (poți schimba limita)
        $questions = Question::query()
            ->where('category_id', $categoryId)
            ->inRandomOrder()
            ->limit(20)
            ->get(['id']);

        if ($questions->isEmpty()) {
            return back()->with('error', 'Categoria nu are încă întrebări.');
        }

        $attempt = Attempt::create([
            'user_id' => $user->id,
            'category_id' => $categoryId,
            'started_at' => now(),
            'total_questions' => $questions->count(),
            'correct_count' => 0,
            'finished_at' => null,
        ]);

        // Pre-creăm answer rows (ca să avem ordine stabilă)
        foreach ($questions as $q) {
            Answer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $q->id,
                'choice' => null,
                'is_correct' => false,
            ]);
        }

        return redirect()->route('tests.show', $attempt);
    }

    public function show(Attempt $attempt, Request $request)
    {
        $this->authorizeAttempt($attempt, $request);

        if ($attempt->finished_at) {
            return redirect()->route('tests.result', $attempt);
        }

        // Ia prima întrebare ne-răspunsă
        $answers = $attempt->answers()
            ->with('question')
            ->orderBy('id')
            ->get();

        $totalQuestions = $answers->count();
        $answeredCount = $answers->whereNotNull('choice')->count();

        $current = $answers->firstWhere('choice', null);
        if (!$current) {
            // nimic ne-răspuns -> submit automat
            return redirect()->route('tests.submit', $attempt);
        }

        $question = $current->question;

        $currentIndex = $answeredCount + 1;
        $isLast = ($currentIndex >= $totalQuestions);

        return view('tests.take', compact(
            'attempt',
            'question',
            'currentIndex',
            'totalQuestions',
            'isLast'
        ));
    }

    public function answer(Attempt $attempt, Request $request)
    {
        $this->authorizeAttempt($attempt, $request);

        if ($attempt->finished_at) {
            return redirect()->route('tests.result', $attempt);
        }

        $data = $request->validate([
            'choice' => ['required', 'in:A,B,C'],
            'question_id' => ['required', 'exists:questions,id'],
        ]);

        $question = Question::findOrFail($data['question_id']);

        // Găsim answer row corespunzător
        $answer = Answer::query()
            ->where('attempt_id', $attempt->id)
            ->where('question_id', $question->id)
            ->firstOrFail();

        // dacă era deja răspuns -> nu îl schimbăm (poți schimba dacă vrei edit)
        if ($answer->choice !== null) {
            return redirect()->route('tests.show', $attempt);
        }

        $isCorrect = strtoupper($data['choice']) === strtoupper($question->correct);

        $answer->update([
            'choice' => $data['choice'],
            'is_correct' => $isCorrect,
        ]);

        if ($isCorrect) {
            $attempt->increment('correct_count');
        }

        return redirect()->route('tests.show', $attempt);
    }

    public function submit(Attempt $attempt, Request $request)
    {
        $this->authorizeAttempt($attempt, $request);

        if ($attempt->finished_at) {
            return redirect()->route('tests.result', $attempt);
        }

        // finalizează doar dacă toate au choice sau dacă vrei forced submit
        $unanswered = $attempt->answers()->whereNull('choice')->count();

        // dacă vrei să permiți submit chiar cu ne-răspunse, scoate if-ul ăsta
        if ($unanswered > 0) {
            return redirect()->route('tests.show', $attempt)
                ->with('error', 'Mai ai întrebări nerăspunse.');
        }

        $attempt->update([
            'finished_at' => now(),
        ]);

        return redirect()->route('tests.result', $attempt);
    }

    public function result(Attempt $attempt, Request $request)
    {
        $this->authorizeAttempt($attempt, $request);

        $answers = $attempt->answers()->with('question')->orderBy('id')->get();

        $total = max(1, (int) $attempt->total_questions);
        $correct = (int) $attempt->correct_count;
        $percent = round(($correct / $total) * 100, 1);

        return view('tests.result', compact('attempt', 'answers', 'total', 'correct', 'percent'));
    }

    public function history(Request $request)
    {
        $user = $request->user();

        $attempts = Attempt::query()
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->with('category')
            ->paginate(15);

        return view('tests.history', compact('attempts'));
    }

    public function aiGenerate(Attempt $attempt, Request $request)
    {
        // placeholder – îl facem “pe bune” după ce UI + core flow e perfect
        $this->authorizeAttempt($attempt, $request);

        return back()->with('status', 'AI Generate (placeholder).');
    }

    private function authorizeAttempt(Attempt $attempt, Request $request): void
    {
        if ($attempt->user_id !== $request->user()->id) {
            abort(403);
        }
    }
}
