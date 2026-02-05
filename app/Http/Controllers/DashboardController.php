<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $base = Attempt::query()->where('user_id', $user->id);

        // totals
        $totalAttempts = (clone $base)->count();
        $finishedAttempts = (clone $base)->whereNotNull('finished_at')->count();

        // doar cele valide (fără total_questions=0)
        $validFinished = (clone $base)
            ->whereNotNull('finished_at')
            ->where('total_questions', '>', 0);

        // SAFE best/avg (float division)
        $bestPercent = (clone $validFinished)
            ->selectRaw('MAX(correct_count * 100.0 / total_questions) AS best_percent')
            ->value('best_percent') ?? 0;

        $avgPercent = (clone $validFinished)
            ->selectRaw('AVG(correct_count * 100.0 / total_questions) AS avg_percent')
            ->value('avg_percent') ?? 0;

        // test activ
        $activeAttempt = (clone $base)
            ->whereNull('finished_at')
            ->with('category')
            ->latest()
            ->first();

        // evoluție scor (ultimele 10 finalizate valide)
        $recentFinishedNewestFirst = (clone $validFinished)
            ->with('category')
            ->latest('finished_at')
            ->limit(10)
            ->get();

        $recentFinished = $recentFinishedNewestFirst->reverse()->values();

        // streak (>=70%) de la cele mai noi spre vechi
        $streak = 0;
        foreach ($recentFinishedNewestFirst as $a) {
            $pct = (int) round(($a->correct_count * 100) / max(1, (int)$a->total_questions));
            if ($pct >= 70) $streak++;
            else break;
        }

        // progres pe categorii: ultimul attempt valid per categorie
        $categories = Category::withCount('questions')->get();

        $lastByCategory = (clone $validFinished)
            ->latest('finished_at')
            ->get()
            ->groupBy('category_id')
            ->map(fn($items) => $items->first());

        $categoryProgress = $categories->map(function ($c) use ($lastByCategory) {
            $last = $lastByCategory->get($c->id);

            $pct = $last
                ? (int) round(($last->correct_count * 100) / max(1, (int)$last->total_questions))
                : null;

            return (object)[
                'category' => $c,
                'last_attempt' => $last,
                'last_percent' => $pct,
            ];
        });

        // “Ultimele teste” listă (și valide și invalide, dar cu procent calculat safe)
        $recentAttempts = (clone $base)
            ->with('category')
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('dashboard-custom', compact(
            'totalAttempts',
            'finishedAttempts',
            'bestPercent',
            'avgPercent',
            'streak',
            'activeAttempt',
            'recentFinished',
            'categoryProgress',
            'recentAttempts',
        ));
    }
}
