<?php

namespace App\Services\Material;

use App\Repositories\Material\MaterialRepository;

class MaterialService
{
    private $allowedOrderColumns = ['name', 'code', 'category_id', 'color'];
    public function __construct(
        protected MaterialRepository $materialRepository,
    ) {}
    
    public function getListMaterials($filters)
    {
        $orderBy = $filters['orderBy'] ?? null;
        $direction = ($filters['direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $search = $filters['search'] ?? null;
        $categoryId = $filters['categoryId'] ?? null;
        $color = $filters['color'] ?? null;

        $orderBy = isset($filters['orderBy']) && in_array($filters['orderBy'], $this->allowedOrderColumns)
            ? $filters['orderBy']
            : 'name';

        $materials = $this->materialRepository->list(
            $categoryId,
            $color,
            $search,
            $orderBy,
            $direction
        );

        return $materials;
    }


    public function getComboMaterials()
    {
        $materials = $this->materialRepository->all(['id', 'name', 'code', 'color', 'measurement_unit_id']);

        return $materials->map(function ($material) {
            return [
                'id' => $material->id,
                'name' => $material->name,
                'code' => $material->code,
                'color' => $material->color,
                'unit_abbreviation' => $material->measurementUnit?->abbreviation
            ];
        });
    }
}
