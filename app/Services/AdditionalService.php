<?php

namespace App\Services;

use App\Models\Additional;
use App\Models\AdditionalCategory;
use App\Models\AdditionalCategoryActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdditionalService
{
  public function createAdditionalWithCategories($data)
  {
    $categories = $data['categories'];
    unset($data['categories']);
    $data['user_id'] = Auth::user()->id;

    $additional = Additional::create($data);
    $additionalData = $additional->toArray();

    foreach ($categories as $categoryData) {
      $activities = isset($categoryData['activities']) ? $categoryData['activities'] : [];
      unset($categoryData['activities']);
      $categoryData['additional_id'] = $additional->id;

      $category = AdditionalCategory::create($categoryData);
      $categoryData = $category->toArray();

      if (count($activities) > 0) {
        $categoryData['activities'] = [];
        foreach ($activities as $activityData) {
          $activityData['additional_category_id'] = $category->id;
          $activity = AdditionalCategoryActivity::create($activityData);
          $categoryData['activities'][] = $activity->toArray();
        }
      }
      $additionalData['categories'][] = $categoryData;
    }
    return $additionalData;
  }

  public function updateAdditional($additionalId, $data)
  {
    try {
      $additional = Additional::findOrFail($additionalId);
      $additional->update($data);

      if (isset($data['categories'])) {
        $categoriesData = $data['categories'];

        foreach ($categoriesData as $i => $categoryData) {
          $categoryData['additional_id'] = $additional->id;

          $category = $additional->categories()->updateOrCreate(
            ['id' => $categoryData['id'] ?? null],
            $categoryData
          );

          $categoriesData[$i]['id'] = $category->id;

          if (isset($categoryData['activities'])) {
            $activitiesData = $categoryData['activities'];

            foreach ($activitiesData as $i => $activityData) {
              $activityData['additional_category_id'] = $category->id;

              $activity = $category->activities()->updateOrCreate(
                ['id' => $activityData['id'] ?? null],
                $activityData
              );

              $activitiesData[$i]['id'] = $activity->id;
            }
          }

          // Elimina las actividades que ya no se utilizan
          $category->activities()->whereNotIn('id', array_column($activitiesData, 'id'))->delete();
        }
      }

      // Elimina las categorías que ya no se utilizan
      $additional->categories()->whereNotIn('id', array_column($categoriesData, 'id'))->delete();

      return ['status' => 200, 'data' => $additional];
    } catch (ModelNotFoundException $e) {
      return ['status' => 422, 'message' => 'Adicional no encontrado'];
    }
  }

  public function getAdditionalCostsByProvider($additionalData)
  {
    $result = [];

    foreach ($additionalData['categories'] as $category) {
      foreach ($category['activities'] as $activity) {
        $providerId = $activity['provider_id'];
        $additionalCost = $activity['unit_cost'] * $activity['quantity'];

        if (!isset($result[$providerId])) {
          $result[$providerId] = 0;
        }

        $result[$providerId] += $additionalCost;
      }
    }

    $output = [];

    foreach ($result as $providerId => $additionalCost) {
      $output[] = [
        'contractor_id' => $providerId,
        'additional_cost' => round($additionalCost, 2),
      ];
    }

    return $output;
  }
}
