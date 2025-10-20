<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Obra;
use App\Models\Budget;
use App\Models\Ipc;
use App\Models\Cac;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /** 
     * Display the dashboard graph
     *
     * @return Response
     */
    public function index(): Response
    {
        $now = now();

        $obraStats = Obra::selectRaw("
                COUNT(CASE WHEN end_date < ? THEN 1 END) as overdue_obras,
                COUNT(CASE WHEN start_date > ? THEN 1 END) as upcoming_obras,
                COUNT(CASE WHEN start_date < ? AND end_date > ? THEN 1 END) as on_day_obras,
                COUNT(CASE WHEN status = 'IN_PROGRESS' THEN 1 END) as in_progress_obras
            ", [$now, $now, $now, $now])
            ->first();

        $budgetStats = Budget::selectRaw("
            COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as pending_budgets,
            COUNT(CASE WHEN status = 'FINISHED' THEN 1 END) as finished_budgets")
            ->first();

        $IPCs = Ipc::orderByDesc('period')
            ->take(13)
            ->get(['period', 'value'])
            ->sortBy('period');

        $IPC_labels = $IPCs->pluck('period')->map(function ($period) {
            $date = Carbon::createFromFormat('Y-m', $period);
            return $date->format('m/y');
        })->values()->all();

        $ipcData = [
            'labels' => $IPC_labels,
            'data' => $IPCs->pluck('value')->values()->all()
        ];

        // Get the CAC data
        $CACs = Cac::orderByDesc('period')
            ->take(13)
            ->get(['period', 'general', 'materials', 'labour'])
            ->sortBy('period');

        $CAC_labels = $CACs->pluck('period')->map(function ($period) {
            return Carbon::createFromFormat('Y-m', $period)->format('m/y');
        })->values()->all();

        $cacData = [
            'labels' => $CAC_labels,
            'data' => [
                'general' => $CACs->pluck('general')->values()->all(),
                'materials' => $CACs->pluck('materials')->values()->all(),
                'labor' => $CACs->pluck('labour')->values()->all(),
            ]
        ];

        $data = [
            'obras' => [
                'upcoming' => $obraStats->upcoming_obras,
                'overdue' => $obraStats->overdue_obras,
                'on_day' => $obraStats->on_day_obras,
                'in_progress' => $obraStats->in_progress_obras,
            ],
            'budgets' => [
                'pending' => $budgetStats->pending_budgets,
                'finished' => $budgetStats->finished_budgets,
            ],
            'graphs' => [
                'ipc' => $ipcData,
                'cac' => $cacData
            ]
        ];

        return response($data, 200);
    }

    /**
     * Get obras statistics for dashboard
     *
     * @return Response
     */
    public function obrasStats(): Response
    {
        $stats = Obra::selectRaw("
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'IN_PROGRESS' THEN 1 END) as in_progress,
                COUNT(CASE WHEN status = 'FINALIZED' THEN 1 END) as finalized
            ")
            ->first();

        return response([
            'total' => $stats->total,
            'in_progress' => $stats->in_progress,
            'finalized' => $stats->finalized,
        ], 200);
    }
}
