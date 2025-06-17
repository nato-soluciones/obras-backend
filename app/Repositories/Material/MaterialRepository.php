<?php

namespace App\Repositories\Material;

use App\Models\Material;

class MaterialRepository
{
    public function list($categoryId, $color, $search, $orderBy, $direction, $perPage = 16)
    {
        $materials = Material::query()
            ->with([
                'measurementUnit',
                'category'
            ])
            ->when($search, function ($query) use ($search) {
                $search = strtolower($search);

                return $query->whereRaw("LOWER(CONCAT(code, ' ', name, ' ', color)) LIKE ?", ["%{$search}%"]);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($color, function ($query) use ($color) {
                $color = strtolower($color);
                $query->whereRaw("LOWER(color) LIKE ?", ["%{$color}%"]);
            })
            ->when($orderBy, function ($query) use ($orderBy, $direction) {
                    $query->orderBy($orderBy, $direction);
                },
                function ($query) {
                    return $query->orderBy('id', 'asc');
                }
            )
            ->paginate($perPage);

        return $materials;
    }
}
