<?php

namespace App\Services;

use App\Models\Additional;
use App\Models\AdditionalCategory;
use App\Models\AdditionalCategoryActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

class AdditionalService
{
  public function createAdditionalWithCategories($data)
  {
    $categories = $data['categories'];
    unset($data['categories']);
    $data['user_id'] = Auth::user()->id;

    $additional = Additional::create($data);

    foreach ($categories as $categoryData) {
      $activities = isset($categoryData['activities']) ? $categoryData['activities'] : [];
      unset($categoryData['activities']);
      $categoryData['additional_id'] = $additional->id;

      $category = AdditionalCategory::create($categoryData);

      if (count($activities) > 0) {
        foreach ($activities as $activityData) {
          $activityData['additional_category_id'] = $category->id;
          AdditionalCategoryActivity::create($activityData);
          $category['activities'][] = $activityData;
        }
      }
      $additional['categories'][] = $category;
    }
    return $additional;
  }

  public function updateAdditional($additionalId, $data)
  {
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

    return $additional;
  }
}
