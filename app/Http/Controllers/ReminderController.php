<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReminderRequest;
use App\Http\Requests\UpdateReminderRequest;
use App\Http\Resources\Reminders\ReminderResourceCollection;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $query = Reminder::where('user_id', auth()->user()->id);

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('status')) {
            if ($request->status === 'resolved') {
                $query->resolved();
            } elseif ($request->status === 'pending') {
                $query->pending();
            }
        }

        if ($request->has('overdue') && $request->overdue) {
            $query->overdue();
        }

        if ($request->has('date_from')) {
            $query->where('datetime', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->has('date_to')) {
            $query->where('datetime', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $reminders = $query->with('creator')
            ->orderBy('datetime', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return response()->json([
            'data' => ReminderResourceCollection::collection($reminders)->response()->getData(true)['data'],
            'current_page' => $reminders->currentPage(),
            'last_page' => $reminders->lastPage(),
        ]);
    }

    public function indexToday()
    {
        $reminders = Reminder::where('user_id', auth()->user()->id)
            ->whereBetween('datetime', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
            ->where('is_resolved', false)
            ->with('creator')
            ->orderBy('datetime', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json(
            ReminderResourceCollection::collection($reminders)
        );
    }

    public function store(StoreReminderRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();

            if (!isset($data['user_id'])) {
                $data['user_id'] = Auth::id();
            }

            Reminder::create($data);
            return response(['message' => 'Recordatorio creado correctamente'], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al crear el recordatorio'], 500);
        }
    }

    public function update(UpdateReminderRequest $request, int $reminderId)
    {
        try {
            $reminder = Reminder::findOrFail($reminderId);

            if ($reminder->user_id !== auth()->id() && $reminder->created_by !== auth()->id()) {
                return response()->json(['message' => 'No tienes permisos para modificar este recordatorio'], 403);
            }

            $data = $request->validated();
            $reminder->update($data);
            return response(['message' => 'Recordatorio actualizado correctamente'], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al actualizar el recordatorio'], 500);
        }
    }

    public function destroy(int $reminderId)
    {
        try {
            $reminder = Reminder::findOrFail($reminderId);

            if ($reminder->user_id !== auth()->id() && $reminder->created_by !== auth()->id()) {
                return response()->json(['message' => 'No tienes permisos para eliminar este recordatorio'], 403);
            }

            $reminder->delete();
            return response()->json(['message' => 'Recordatorio eliminado correctamente'], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al eliminar el recordatorio'], 500);
        }
    }

    public function toggleResolved(int $reminderId)
    {
        try {
            $reminder = Reminder::findOrFail($reminderId);

            if ($reminder->user_id !== auth()->id()) {
                return response()->json([
                    'errors' => [
                        'general' => ['No tienes permisos para modificar este recordatorio']
                    ]
                ], 422);
            }

            $is_resolved = !$reminder->is_resolved;

            $dataUpdate = [
                'is_resolved' => $is_resolved,
                'date_resolved' => $is_resolved ? Carbon::now() : null
            ];
            $reminder->update($dataUpdate);
            return response()->json(['message' => 'Recordatorio actualizado correctamente'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al actualizar el recordatorio'], 500);
        }
    }

    public function createdForOthers(Request $request)
    {
        $userId = Auth::id();

        $query = Reminder::createdByMe($userId)
            ->where('user_id', '!=', $userId)
            ->with(['user', 'creator']);

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('status')) {
            if ($request->status === 'resolved') {
                $query->resolved();
            } elseif ($request->status === 'pending') {
                $query->pending();
            }
        }

        $reminders = $query->orderBy('datetime', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return response()->json([
            'data' => ReminderResourceCollection::collection($reminders)->response()->getData(true)['data'],
            'current_page' => $reminders->currentPage(),
            'last_page' => $reminders->lastPage(),
        ]);
    }

    public function overdue()
    {
        $reminders = Reminder::where('user_id', auth()->user()->id)
            ->overdue()
            ->with('creator')
            ->orderBy('datetime', 'desc')
            ->get();

        return response()->json([
            'data' => ReminderResourceCollection::collection($reminders)
        ]);
    }
}
