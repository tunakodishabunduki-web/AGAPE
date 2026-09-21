<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    // POST /api/analytics — unauthenticated, called by every visitor's browser
    // on every pageview (site-data.js already does this). Rate-limited via
    // routes/api.php so this can't be trivially used to bloat the database.
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string|max:60',
            'page' => 'nullable|string|max:255',
            'label' => 'nullable|string|max:255',
        ]);
        AnalyticsEvent::create([...$data, 'created_at' => now()]);
        return response()->json(['ok' => true], 201);
    }

    // GET /api/analytics/summary?days=30 — admin-only. Daily visitor counts
    // for the dashboard graph, counting only 'pageview' events (the other
    // event types are actions, not visits, and would skew a "traffic" chart).
    public function summary(Request $request)
    {
        $days = min(90, max(1, (int) $request->query('days', 30)));
        $since = now()->subDays($days - 1)->startOfDay();

        $rows = AnalyticsEvent::where('type', 'pageview')
            ->where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        // Zero-fill every day in range so the chart doesn't show gaps as
        // "no data point" — a day with genuinely zero visits should read as 0.
        $series = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $since->copy()->addDays($i)->toDateString();
            $series[] = ['date' => $date, 'count' => (int) ($rows[$date] ?? 0)];
        }

        return response()->json([
            'days' => $days,
            'total' => array_sum(array_column($series, 'count')),
            'series' => $series,
        ]);
    }
}
