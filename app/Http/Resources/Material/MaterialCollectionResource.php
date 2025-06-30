<?php

namespace App\Http\Resources\Material;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\StoreMovementMaterial;

class MaterialCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $stock = $this->storeMaterials->sum('quantity');

        $lastMovement = StoreMovementMaterial::where('material_id', $this->id)
            ->latest('created_at')
            ->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'category' => $this->category,
            'dimensions' => $this->dimensions,
            'quantity_per_package' => $this->quantity_per_package,
            'color' => $this->color,
            'description' => $this->description,
            'unit' => $this->measurementUnit->name,
            'unit_abbreviation' => $this->measurementUnit->abbreviation,
            'stock' => $stock,
            'lastMovement' => $lastMovement ? $lastMovement->created_at->format('d/m/Y') : null,
        ];
    }
}
