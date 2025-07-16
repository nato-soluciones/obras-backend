<?php

namespace App\Services;

use App\Http\Resources\CalendarEventResource;
use App\Models\CalendarEvent;
use App\Models\CalendarEventParticipant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CalendarEventService
{
    /**
     * Procesa y completa los datos de un participante
     */
    public function processParticipantData(array $participantData): array
    {
        // Si es un usuario interno, completar datos faltantes
        if ($participantData['user_id']) {
            $user = User::find($participantData['user_id']);
            if ($user) {
                // Solo completar datos si no fueron proporcionados
                if (empty($participantData['name'])) {
                    $participantData['name'] = $user->name;
                }
                if (empty($participantData['email'])) {
                    $participantData['email'] = $user->email;
                }
                if (empty($participantData['phone'])) {
                    $participantData['phone'] = $user->phone;
                }
            }
        }

        return $participantData;
    }

    /**
     * Crea participantes para un evento nuevo
     */
    public function createParticipants(CalendarEvent $event, array $participants): void
    {
        if (empty($participants)) {
            return;
        }

        foreach ($participants as $participant) {
            $participantData = [
                'calendar_event_id' => $event->id,
                'user_id' => $participant['user_id'] ?? null,
                'name' => $participant['name'] ?? '',
                'email' => $participant['email'] ?? '',
                'phone' => $participant['phone'] ?? null,
                'status' => 'pending'
            ];

            $participantData = $this->processParticipantData($participantData);
            CalendarEventParticipant::create($participantData);
        }
    }

    /**
     * Sincroniza participantes preservando el status existente
     */
    public function syncParticipants(CalendarEvent $event, array $participants): void
    {
        $existingParticipants = $event->participants()->get();
        $newParticipantIds = collect($participants)->pluck('user_id')->filter()->toArray();
        $newExternalParticipants = collect($participants)->whereNull('user_id')->toArray();

        // Eliminar participantes que ya no están en la lista
        foreach ($existingParticipants as $existingParticipant) {
            $shouldKeep = false;
            
            if ($existingParticipant->user_id) {
                // Participante interno
                $shouldKeep = in_array($existingParticipant->user_id, $newParticipantIds);
            } else {
                // Participante externo - verificar por email
                $shouldKeep = collect($newExternalParticipants)->contains('email', $existingParticipant->email);
            }
            
            if (!$shouldKeep) {
                $existingParticipant->delete();
            }
        }

        // Agregar o actualizar participantes
        foreach ($participants as $participant) {
            $participantData = [
                'calendar_event_id' => $event->id,
                'user_id' => $participant['user_id'] ?? null,
                'name' => $participant['name'] ?? '',
                'email' => $participant['email'] ?? '',
                'phone' => $participant['phone'] ?? null,
            ];

            // Buscar participante existente
            $existingParticipant = null;
            if ($participantData['user_id']) {
                $existingParticipant = $existingParticipants->where('user_id', $participantData['user_id'])->first();
            } else {
                $existingParticipant = $existingParticipants->whereNull('user_id')->where('email', $participantData['email'])->first();
            }

            if ($existingParticipant) {
                // Actualizar participante existente (preservar status)
                $updateData = $participantData;
                unset($updateData['calendar_event_id']); // No actualizar este campo
                
                $updateData = $this->processParticipantData($updateData);
                $existingParticipant->update($updateData);
            } else {
                // Crear nuevo participante
                $participantData['status'] = 'pending';
                $participantData = $this->processParticipantData($participantData);
                CalendarEventParticipant::create($participantData);
            }
        }
    }

    /**
     * Verifica conflictos de horario para un evento
     */
    public function checkEventConflicts(CalendarEvent $event): bool
    {
        return $event->hasConflictWith(
            $event->start_datetime,
            $event->end_datetime,
            $event->id
        );
    }

    /**
     * Formatea la respuesta de éxito estándar
     */
    public function formatSuccessResponse(CalendarEvent $event, string $message, bool $hasConflict = false, int $statusCode = 200): array
    {
        $event->load(['category', 'participants.user']);

        $response = [
            'success' => true,
            'message' => $message,
            'data' => new CalendarEventResource($event)
        ];

        if ($hasConflict) {
            $response['warning'] = 'Existe un conflicto de horario con otro evento.';
        }

        return ['response' => $response, 'status_code' => $statusCode];
    }

    /**
     * Maneja transacciones de base de datos con formato de error estándar
     */
    public function handleEventTransaction(callable $callback, string $errorMessage = 'Error en la operación.'): array
    {
        try {
            DB::beginTransaction();
            
            $result = $callback();
            
            DB::commit();
            
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'response' => [
                    'success' => false,
                    'message' => $errorMessage,
                    'error' => $e->getMessage()
                ],
                'status_code' => 500
            ];
        }
    }

    /**
     * Verifica conflictos de horario solo si las fechas fueron actualizadas
     */
    public function checkEventConflictsOnUpdate(CalendarEvent $event, array $eventData): bool
    {
        if (isset($eventData['start_datetime']) || isset($eventData['end_datetime'])) {
            return $this->checkEventConflicts($event);
        }
        
        return false;
    }
}