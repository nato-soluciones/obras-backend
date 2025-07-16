<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalendarEventRequest;
use App\Http\Requests\UpdateCalendarEventRequest;
use App\Http\Resources\CalendarEventResource;
use App\Models\CalendarEvent;
use App\Models\CalendarEventParticipant;
use App\Services\CalendarEventService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CalendarEventController extends Controller
{
    protected $calendarEventService;

    public function __construct(CalendarEventService $calendarEventService)
    {
        $this->calendarEventService = $calendarEventService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of calendar events
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = CalendarEvent::with(['category', 'participants.user'])
            ->forUser($user->id);

        // Filtros de fecha
        if ($request->has('start') && $request->has('end')) {
            $startDate = Carbon::parse($request->start);
            $endDate = Carbon::parse($request->end);         

            $query->where(function ($q) use ($startDate, $endDate) {
                $q->where('start_datetime', '<=', $endDate)
                    ->where('end_datetime', '>=', $startDate);
            });
        }

        // Filtro por estado
        if ($request->has('status')) {
            $query->status($request->status);
        }

        $events = $query->orderBy('start_datetime')->get();

        Log::info(
            vsprintf(
                str_replace('?', "'%s'", $query->toSql()),
                collect($query->getBindings())->map(function ($binding) {
                    // Si el binding es una fecha o string, lo rodea de comillas
                    return is_numeric($binding) ? $binding : addslashes($binding);
                })->toArray()
            )
        );

        return response()->json([
            'success' => true,
            'data' => CalendarEventResource::collection($events)
        ]);
    }

    /**
     * Store a newly created calendar event
     */
    public function store(StoreCalendarEventRequest $request): JsonResponse
    {
        $result = $this->calendarEventService->handleEventTransaction(function() use ($request) {
            $eventData = $request->validated();
            $eventData['user_id'] = $request->user()->id;

            // Remover participantes de los datos del evento
            $participants = $eventData['participants'] ?? [];
            unset($eventData['participants']);

            $event = CalendarEvent::create($eventData);

            // Agregar participantes
            $this->calendarEventService->createParticipants($event, $participants);

            // Verificar conflictos de horario
            $hasConflict = $this->calendarEventService->checkEventConflicts($event);

            return $this->calendarEventService->formatSuccessResponse(
                $event, 
                'Evento creado exitosamente.', 
                $hasConflict, 
                201
            );
        }, 'Error al crear el evento.');

        return response()->json($result['response'], $result['status_code']);
    }

    /**
     * Display the specified calendar event
     */
    public function show(CalendarEvent $event): JsonResponse
    {
        $user = request()->user();
        
        // Verificar que el usuario tenga acceso al evento
        if ($event->user_id !== $user->id && 
            !$event->participants()->where('user_id', $user->id)->exists() &&
            $event->visibility !== 'public') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este evento.'
            ], 403);
        }

        $event->load(['category', 'participants.user', 'user']);

        return response()->json([
            'success' => true,
            'data' => new CalendarEventResource($event)
        ]);
    }

    /**
     * Update the specified calendar event
     */
    public function update(UpdateCalendarEventRequest $request, CalendarEvent $event): JsonResponse
    {
        $user = $request->user();

        // Solo el organizador puede editar el evento
        if ($event->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Solo el organizador puede editar este evento.'
            ], 403);
        }

        $result = $this->calendarEventService->handleEventTransaction(function() use ($request, $event) {
            $eventData = $request->validated();
            $participants = $eventData['participants'] ?? null;
            unset($eventData['participants']);

            $event->update($eventData);

            // Actualizar participantes si se proporcionaron
            if ($participants !== null) {
                $this->calendarEventService->syncParticipants($event, $participants);
            }

            // Verificar conflictos de horario si se actualizaron las fechas
            $hasConflict = $this->calendarEventService->checkEventConflictsOnUpdate($event, $eventData);

            return $this->calendarEventService->formatSuccessResponse(
                $event, 
                'Evento actualizado exitosamente.', 
                $hasConflict
            );
        }, 'Error al actualizar el evento.');

        return response()->json($result['response'], $result['status_code']);
    }

    /**
     * Remove the specified calendar event
     */
    public function destroy(CalendarEvent $event): JsonResponse
    {
        $user = request()->user();

        // Solo el organizador puede eliminar el evento
        if ($event->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Solo el organizador puede eliminar este evento.'
            ], 403);
        }

        try {
            $event->delete();

            return response()->json([
                'success' => true,
                'message' => 'Evento eliminado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el evento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update participant status for a calendar event
     */
    public function updateParticipantStatus(Request $request, CalendarEvent $event, CalendarEventParticipant $participant): JsonResponse
    {
        $user = $request->user();

        // Verificar que el participante pertenece al evento
        if ($participant->calendar_event_id !== $event->id) {
            return response()->json([
                'success' => false,
                'message' => 'El participante no pertenece a este evento.'
            ], 404);
        }

        // Solo el propio participante puede cambiar su status
        if ($participant->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes cambiar tu propio estado de participación.'
            ], 403);
        }

        // Validar el status
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected'
        ]);

        try {
            $participant->update([
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado de participación actualizado exitosamente.',
                'data' => [
                    'id' => $participant->id,
                    'status' => $participant->status,
                    'updated_at' => $participant->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado de participación.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search calendar events by title with pagination
     */
    public function search(Request $request): JsonResponse
    {
        // Validar parámetros de búsqueda
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:50'
        ]);

        $query = $request->get('q');
        $userId = $request->user()->id;
        $perPage = $request->get('per_page', 15); // Default 15 elementos por página

        try {
            $events = CalendarEvent::with(['category', 'participants.user', 'user'])
                ->where('title', 'LIKE', '%' . $query . '%')
                ->where(function($q) use ($userId) {
                    $q->where('user_id', $userId) // Es organizador
                      ->orWhere('visibility', 'public') // Es evento público
                      ->orWhereHas('participants', function($p) use ($userId) {
                          $p->where('user_id', $userId); // Es participante
                      });
                })
                ->orderBy('start_datetime', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => CalendarEventResource::collection($events->items()),
                'meta' => [
                    'query' => $query,
                    'current_page' => $events->currentPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                    'last_page' => $events->lastPage(),
                    'from' => $events->firstItem(),
                    'to' => $events->lastItem(),
                    'has_more_pages' => $events->hasMorePages()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar eventos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
