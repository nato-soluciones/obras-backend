<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contractor;
use App\Models\CurrentAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CurrentAccountController extends Controller
{
    //? Clients Methods
    public function indexClients(int $clientId)
    {
        $currentAccounts = Client::select('id', 'person_type', 'firstname', 'lastname', 'business_name')
            ->with(['currentAccounts' => function ($q) {
                $q->select('id', 'project_id', 'entity_id', 'currency', 'balance')
                    ->with(['project' => function ($query) {
                        $query->withTrashed()->select('id', 'name');
                    }])
                    ->orderBy('project_id', 'asc')
                    ->orderBy('id', 'asc');
            }])
            ->find($clientId);

        return response($currentAccounts, 200);
    }

    public function showClient(int $clientId, int $projectId, string $currency)
    {
        $ca_movements = CurrentAccount::select('id', 'entity_id', 'project_id', 'currency', 'balance')
            ->with([
                'client' => function ($q) {
                    $q->select('id', 'person_type', 'firstname', 'lastname', 'business_name');
                },
                'project' => function ($query) {
                    $query->withTrashed()->select('id', 'name');
                }
            ])
            ->where('entity_type', 'CLIENT')
            ->where('entity_id', $clientId)
            ->where('project_id', $projectId)
            ->where('currency', $currency)
            ->first();

        return response($ca_movements, 200);
    }

    public function storeClient(Request $request, string $clientId)
    {
        $data = [
            'entity_type' => 'CLIENT',
            'entity_id' => $clientId,
            'project_id' => $request->project_id,
            'currency' => $request->currency,
            'balance' => 0,
        ];
        try {
            $currentAccount = CurrentAccount::where('entity_type', 'CLIENT')
                ->where('entity_id', $clientId)
                ->where('project_id', $request->project_id)
                ->where('currency', $request->currency)
                ->first();

            if ($currentAccount) {
                return response(['status' => 409, 'message'  => 'La cuenta corriente ya existe'], 409); // Código de respuesta 409 Conflict
            }

            $currentAccount = CurrentAccount::create($data);
            return response($currentAccount, 201);
        } catch (\Exception $e) {
            return response(['message' => 'Error al crear la cuenta corriente'], 500);
        }
    }

    //? Providers Methods
    public function indexProviders(int $providerId)
    {
        $currentAccounts = Contractor::select('id', 'person_type', 'first_name', 'last_name', 'business_name')
            ->with(['currentAccounts' => function ($q) {
                $q->select('id', 'project_id', 'entity_id', 'currency', 'balance')
                    ->with(['project' => function ($query) {
                        $query->withTrashed()->select('id', 'name');
                    }])
                    ->orderBy('project_id', 'asc')
                    ->orderBy('id', 'asc');
            }])
            ->find($providerId);

        return response($currentAccounts, 200);
    }

    public function showProvider(int $providerId, int $projectId, string $currency)
    {
        $ca_movements = CurrentAccount::select('id', 'entity_id', 'project_id', 'currency', 'balance')
            ->with([
                'provider' => function ($q) {
                    $q->select('id', 'person_type', 'first_name', 'last_name', 'business_name');
                },
                'project' => function ($query) {
                    $query->withTrashed()->select('id', 'name');
                }
            ])
            ->where('entity_type', 'PROVIDER')
            ->where('entity_id', $providerId)
            ->where('project_id', $projectId)
            ->where('currency', $currency)
            ->first();

        return response($ca_movements, 200);
    }

    public function storeProvider(Request $request, string $providerId)
    {
        $data = [
            'entity_type' => 'PROVIDER',
            'entity_id' => $providerId,
            'project_id' => $request->project_id,
            'currency' => $request->currency,
            'balance' => 0,
        ];
        try {
            $currentAccount = CurrentAccount::where('entity_type', 'PROVIDER')
                ->where('entity_id', $providerId)
                ->where('project_id', $request->project_id)
                ->where('currency', $request->currency)
                ->first();

            if ($currentAccount) {
                return response(['status' => 409, 'message'  => 'La cuenta corriente ya existe'], 409); // Código de respuesta 409 Conflict
            }

            $currentAccount = CurrentAccount::create($data);
            return response($currentAccount, 201);
        } catch (\Exception $e) {
            return response(['message' => 'Error al crear la cuenta corriente'], 500);
        }
    }
}
