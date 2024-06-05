<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Obra;
use App\Models\Budget;
use App\Models\Ipc;
use App\Models\Cac;

class DashboardController extends Controller
{
    /** 
     * Display the dashboard graph
     *
     * @return Response
     */
    public function index(): Response
    {
        $overdueObras = Obra::where('end_date', '<', now())->count();
        $upcomingObras = Obra::where('start_date', '>', now())->count();
        $onDayObras = Obra::where('start_date', '<', now())
            ->where('end_date', '>', now())
            ->count();

        $inProgressObras = Obra::where('status', 'IN_PROGRESS')->count();

        $pendingBudgets = Budget::where('status', 'PENDING')->count();
        $revisionBudgets = Budget::where('status', 'REVISION')->count();

        // Get the IPC data
        $IPCs = Ipc::orderByDesc('period')
            ->take(13)
            ->get(['period', 'value'])
            ->sortBy('period');
        $uniqueDates = $IPCs->pluck('period');
        $ipcData = [
            'labels' => $uniqueDates->values()->all(),
            'data' => []
        ];
        foreach ($uniqueDates as $date) {
            $ipc = $IPCs->where('period', $date)->first();
            $ipcData['data'][] = $ipc ? $ipc->value : null;
        }

        // Get the CAC data
        $CACs = Cac::orderByDesc('period')
            ->take(13)
            ->get(['period', 'general', 'materials', 'labour'])
            ->sortBy('period');
        $cacData = [
            'labels' => $CACs->pluck('period')->all(),
            'data' => [
                'general' => $CACs->pluck('general')->all(),
                'materials' => $CACs->pluck('materials')->all(),
                'labor' => $CACs->pluck('labour')->all(),
            ]
        ];

        $data = [
            'obras' => [
                'upcoming' => $upcomingObras,
                'overdue' => $overdueObras,
                'on_day' => $onDayObras,
                'in_progress' => $inProgressObras,
            ],
            'budgets' => [
                'pending' => $pendingBudgets,
                'revision' => $revisionBudgets,
            ],
            'graphs' => [
                'ipc' => $ipcData,
                'cac' => $cacData
            ]
        ];

        return response($data, 200);
    }
}
