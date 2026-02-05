<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Attempt activ (pentru "Continuă testul")
        $activeAttempt = Attempt::query()
            ->where('user_id', $user->id)
            ->whereNull('finished_at')
            ->latest('created_at')
            ->with('category')
            ->first();

        // KPI-uri
        $totalAttempts = Attempt::where('user_id', $user->id)->count();

        $finishedAttempts = Attempt::where('user_id', $user->id)
            ->whereNotNull('finished_at')
            ->count();

        $bestPercent = Attempt::where('user_id', $user->id)
            ->whereNotNull('finished_at')
            ->select(DB::raw('MAX((correct_count / total_questions) * 100) as max_percent'))
            ->value('max_percent');

        $avgPercent = Attempt::where('user_id', $user->id)
            ->whereNotNull('finished_at')
            ->select(DB::raw('AVG((correct_count / total_questions) * 100) as avg_percent'))
            ->value('avg_percent');

        $bestPercent = $bestPercent ? round($bestPercent, 1) : 0;
        $avgPercent  = $avgPercent ? round($avgPercent, 1) : 0;

        // Ultimele teste finalizate
        $recentAttempts = Attempt::query()
            ->where('user_id', $user->id)
            ->whereNotNull('finished_at')
            ->with('category')
            ->latest('finished_at')
            ->limit(5)
            ->get();

        // Progres pe categorii
        $categoryProgress = Category::query()
            ->withCount([
                'attempts as finished_count' => function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->whereNotNull('finished_at');
                }
            ])
            ->get();

        return view('dashboard', compact(
            'activeAttempt',
            'totalAttempts',
            'finishedAttempts',
            'bestPercent',
            'avgPercent',
            'recentAttempts',
            'categoryProgress'
        ));
    }
}
