<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\AiQuestion;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('questions')->get();
        return view('tests.index', compact('categories'));
    }

    public function start(Request $request)
    {
        $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'count'       => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        // Nu crea alt test dacă există deja unul în lucru
        $active = Attempt::where('user_id', $request->user()->id)
    ->whereNull('finished_at')
    ->latest()
    ->first();

if ($active) {
    // dacă are întrebări, e un test real -> continuă
    if ($active->answers()->count() > 0) {
        return redirect()->route('tests.show', $active);
    }

    // altfel e corupt (0 întrebări) -> îl ștergem ca să nu blocheze userul
    $active->delete();
}


        $count = $request->integer('count', 20);

        $questionIds = Question::where('category_id', $request->category_id)
            ->inRandomOrder()
            ->limit($count)
            ->pluck('id');

        if ($questionIds->isEmpty()) {
            return back()->with('error', 'Nu există întrebări în această categorie.');
        }

        $attempt = Attempt::create([
            'user_id'         => $request->user()->id,
            'category_id'     => $request->category_id,
            'total_questions' => $questionIds->count(),
            'correct_count'   => 0,
            'started_at'      => now(),
            'finished_at'     => null,
        ]);

        foreach ($questionIds as $qid) {
            AttemptAnswer::create([
                'attempt_id'     => $attempt->id,
                'question_id'    => $qid,
                'ai_question_id' => null,
                'selected'       => null,
                'is_correct'     => 0,
            ]);
        }

        return redirect()->route('tests.show', $attempt);
    }

    public function show(Attempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->finished_at !== null) {
            return redirect()->route('tests.result', $attempt);
        }

        $answers = $attempt->answers()
            ->with(['question', 'aiQuestion'])
            ->get();

        return view('tests.take', compact('attempt', 'answers'));
    }

    /**
     * Optional: salvează un singur răspuns “live” (pentru întrebări clasice).
     * Dacă nu îl folosești în UI, poți să-l ignori.
     */
    public function answer(Request $request, Attempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->finished_at !== null) {
            return redirect()->route('tests.result', $attempt);
        }

        $data = $request->validate([
            'question_id' => ['required', 'integer'],
            'selected'    => ['nullable', 'string', 'in:a,b,c,d'],
        ]);

        $row = $attempt->answers()
            ->where('question_id', $data['question_id'])
            ->firstOrFail();

        $selected = $data['selected'] ? strtolower((string)$data['selected']) : null;
        $row->selected = $selected;

        $isCorrect = false;
        if ($selected) {
            $q = Question::find($row->question_id);
            if ($q) {
                $isCorrect = strtolower((string)$q->correct) === $selected;
            }
        }

        $row->is_correct = $isCorrect ? 1 : 0;
        $row->save();

        return back()->with('success', 'Răspuns salvat.');
    }

    public function submit(Request $request, Attempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->finished_at !== null) {
            return redirect()->route('tests.result', $attempt);
        }

        $rows = $attempt->answers()->get();

        if ($rows->isEmpty()) {
            return redirect()->route('tests.show', $attempt)
                ->with('error', 'Testul nu conține întrebări. Te rog reîncearcă.');
        }

        $input = $request->input('answers', []); // ex: [ 'q_12' => 'a', 'ai_7' => 'b' ]

        // Preîncărcăm întrebările clasice și AI implicate
        $questionIds = $rows->whereNotNull('question_id')->pluck('question_id')->unique()->values();
        $aiIds       = $rows->whereNotNull('ai_question_id')->pluck('ai_question_id')->unique()->values();

        $questions   = $questionIds->isEmpty() ? collect() : Question::whereIn('id', $questionIds)->get()->keyBy('id');
        $aiQuestions = $aiIds->isEmpty() ? collect() : AiQuestion::whereIn('id', $aiIds)->get()->keyBy('id');

        $correctCount = 0;

        foreach ($rows as $row) {
            // cheia din form:
            $key = $row->question_id ? "q_{$row->question_id}" : "ai_{$row->ai_question_id}";

            $selected = $input[$key] ?? null;
            $selected = $selected ? strtolower((string)$selected) : null;

            $row->selected = $selected;

            // determinăm corectul (din question sau ai_question)
            $correctLetter = null;

            if ($row->question_id) {
                $q = $questions[$row->question_id] ?? null;
                $correctLetter = $q ? strtolower((string)$q->correct) : null;
            } elseif ($row->ai_question_id) {
                $aq = $aiQuestions[$row->ai_question_id] ?? null;
                $correctLetter = $aq ? strtolower((string)$aq->correct) : null;
            }

            $isCorrect = ($selected && $correctLetter) ? ($selected === $correctLetter) : false;

            $row->is_correct = $isCorrect ? 1 : 0;
            $row->save();

            if ($isCorrect) {
                $correctCount++;
            }
        }

        $attempt->update([
            'correct_count'   => $correctCount,
            'total_questions' => $rows->count(),
            'finished_at'     => now(),
        ]);

        return redirect()->route('tests.result', $attempt);
    }

    public function result(Attempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        $answers = $attempt->answers()
            ->with(['question', 'aiQuestion'])
            ->get();

        return view('tests.result', compact('attempt', 'answers'));
    }

    /**
     * Premium AI: generează un set nou (mock acum) bazat pe greșelile attempt-ului
     */
    public function aiGenerate(Request $request, Attempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if (!$request->user()->is_premium) {
            return redirect()->route('pricing')
                ->with('error', 'Funcția AI este disponibilă doar pentru Premium.');
        }

        if ($attempt->finished_at === null) {
            return redirect()->route('tests.show', $attempt)
                ->with('error', 'Finalizează testul înainte de AI.');
        }

        // Nu crea alt test dacă există deja unul în lucru
        $active = Attempt::where('user_id', $request->user()->id)
            ->whereNull('finished_at')
            ->latest()
            ->first();

        if ($active) {
            return redirect()->route('tests.show', $active)
                ->with('error', 'Ai deja un test în desfășurare. Finalizează-l înainte.');
        }

        $wrongRows = $attempt->answers()
            ->with('question')
            ->where(function ($q) {
                $q->where('is_correct', 0)->orWhereNull('selected');
            })
            ->get();

        if ($wrongRows->isEmpty()) {
            return redirect()->route('tests.result', $attempt)
                ->with('success', 'Nu ai greșeli. AI nu are ce să personalizeze 🎉');
        }

        // Seed text simplu din greșeli (mock)
        $seed = $wrongRows->take(3)
            ->map(fn($r) => $r->question?->prompt)
            ->filter()
            ->values();

        $count = 20;

        $created = collect();
        for ($i = 1; $i <= $count; $i++) {
            $topic = $seed->isNotEmpty() ? $seed->random() : 'Greșelile tale';

            $created->push(AiQuestion::create([
                'user_id'          => $request->user()->id,
                'category_id'      => $attempt->category_id,
                'source_attempt_id'=> $attempt->id,
                'prompt'           => "AI (mock) #{$i}: Întrebare nouă pornind de la: " . str($topic)->limit(90),
                'a'                => 'Varianta A (mock)',
                'b'                => 'Varianta B (mock)',
                'c'                => 'Varianta C (mock)',
                'd'                => 'Varianta D (mock)',
                'correct'          => 'b',
                'explanation'      => 'Explicație (mock): răspunsul corect este B.',
                'difficulty'       => 2,
                'generator'        => 'mock',
            ]));
        }

        $newAttempt = Attempt::create([
            'user_id'         => $request->user()->id,
            'category_id'     => $attempt->category_id,
            'total_questions' => $created->count(),
            'correct_count'   => 0,
            'started_at'      => now(),
            'finished_at'     => null,
        ]);

        foreach ($created as $aq) {
            AttemptAnswer::create([
                'attempt_id'     => $newAttempt->id,
                'question_id'    => null,
                'ai_question_id' => $aq->id,
                'selected'       => null,
                'is_correct'     => 0,
            ]);
        }

        return redirect()->route('tests.show', $newAttempt)
            ->with('success', 'Set AI generat (mock). Când vrei, conectăm AI real.');
    }

    public function history(Request $request)
    {
        $user = $request->user();

        $filters = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status'      => ['nullable', 'in:all,finished,active'],
            'from'        => ['nullable', 'date'],
            'to'          => ['nullable', 'date'],
        ]);

        $q = Attempt::query()
            ->where('user_id', $user->id)
            ->with('category')
            ->orderByDesc('created_at');

        $status = $filters['status'] ?? 'all';
        if ($status === 'finished') {
            $q->whereNotNull('finished_at');
        } elseif ($status === 'active') {
            $q->whereNull('finished_at');
        }

        if (!empty($filters['category_id'])) {
            $q->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['from'])) {
            $q->whereDate('created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $q->whereDate('created_at', '<=', $filters['to']);
        }

        $attempts = $q->paginate(15)->appends($request->query());
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('tests.history', compact('attempts', 'categories', 'status'));
    }

    private function authorizeAttempt(Attempt $attempt): void
    {
        abort_unless(auth()->id() === $attempt->user_id, 403);
    }
}
