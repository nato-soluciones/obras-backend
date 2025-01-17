<?php

namespace App\Http\Controllers;

use App\Http\Resources\Reminders\ReminderResourceCollection;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = Reminder::where('user_id', auth()->user()->id)
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
            ->select('id', 'text', 'datetime', 'priority')
            ->orderBy('datetime', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        return response($reminders, 200);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();
            $data['created_by'] = Auth::id();
            Reminder::create($data);
            return response(['message' => 'Recordatorio creado correctamente'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al crear el recordatorio'], 500);
        }
    }

    public function update(Request $request, int $reminderId)
    {
        $data = $request->all();
        try {
            $reminder = Reminder::findOrFail($reminderId);
            $reminder->update($data);
            return response(['message' => 'Recordatorio actualizado correctamente'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al actualizar el recordatorio'], 500);
        }
    }

    public function destroy(int $reminderId)
    {
        try {
            $reminder = Reminder::findOrFail($reminderId);
            $reminder->delete();
            return response()->json(['message' => 'Recordatorio eliminado correctamente'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al eliminar el recordatorio'], 500);
        }
    }

    public function toggleResolved(int $reminderId)
    {
        try {
            $reminder = Reminder::findOrFail($reminderId);
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
}
