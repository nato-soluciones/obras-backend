<?php

namespace App\Services\Material;

use App\Models\Material;
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
}
