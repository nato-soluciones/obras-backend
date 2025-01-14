<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function indexLatest()
    {
        $reminders = [
            [
                "id" => 1,
                "text" => 'Revisar reporte trimestral',
                "date"=> '13/03/2024 19:32',
            ],
            [
                "id" => 2,
                "text" => 'Reunión de equipo',
                "date"=> '08/01/2025 09:00',
            ],
        ];
        return response($reminders, 200);
    }
}
