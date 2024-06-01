<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Budget;
use App\Models\Ipc;

class DashboardController extends Controller
{
    /** 
     * Display the dashboard graph
     *
     * @return Response
     */
    public function index(): Response
    {
        $pendingBudgets = Budget::where('status', 'PENDING')->count();
        $revisionBudgets = Budget::where('status', 'REVISION')->count();

        // Get the IPC data
        $ipcs = Ipc::all(['period', 'value']);
        $ipcData = $ipcs->map(function ($ipc) {
            return [
                'date' => $ipc->period,
                'value' => $ipc->value,
            ];
        });

        $data = [
            'obras' => [
                'upcoming' => 10,
                'overdue' => 5,
                'onday' => 5,
                'in_progress' => 5,
            ],
            'budgets' => [
                'pending' => $pendingBudgets,
                'revision' => $revisionBudgets,
            ],
            'graphs' => [
                'ipc' => $ipcData,
                'cac' => [

                ]
            ]
        ];

        return response($data, 200);
    }
}
