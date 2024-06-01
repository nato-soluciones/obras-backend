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
        $ondayObras = Obra::where('start_date', '<', now())
            ->where('end_date', '>', now())
            ->count();

        $inProgressObras = Obra::where('status', 'IN_PROGRESS')->count();

        $pendingBudgets = Budget::where('status', 'PENDING')->count();
        $revisionBudgets = Budget::where('status', 'REVISION')->count();

        // Get the IPC data
        $ipcs = Ipc::orderBy('period', 'asc')->get(['period', 'value']);
        $uniqueDates = $ipcs->pluck('period')->unique()->sort();
        $ipcData = [
            'labels' => $uniqueDates->values()->all(),
            'data' => []
        ];
        foreach ($uniqueDates as $date) {
            $ipc = $ipcs->where('period', $date)->first();
            $ipcData['data'][] = $ipc ? $ipc->value : null;
        }

        // Get the CAC data
        $cacs = Cac::orderBy('period', 'asc')->get(['period', 'general', 'materials', 'labour']);
        $cacData = [
            'labels' => $cacs->pluck('period')->all(),
            'data' => [
                'general' => $cacs->pluck('general')->all(),
                'materials' => $cacs->pluck('materials')->all(),
                'labor' => $cacs->pluck('labour')->all(),
            ]
        ];

        $data = [
            'obras' => [
                'upcoming' => $upcomingObras,
                'overdue' => $overdueObras,
                'onday' => $ondayObras,
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
