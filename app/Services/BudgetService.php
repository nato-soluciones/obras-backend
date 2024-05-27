<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\BudgetCategoryActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BudgetService
{
  public function createBudgetWithCategories($data)
  {
    $categories = $data['categories'];
    unset($data['categories']);
    $data['created_by_id'] = Auth::user()->id;

    $budget = Budget::create($data);

    foreach ($categories as $categoryData) {
      $activities = isset($categoryData['activities']) ? $categoryData['activities'] : [];
      unset($categoryData['activities']);
      $categoryData['budget_id'] = $budget->id;

      $category = BudgetCategory::create($categoryData);

      if (count($activities) > 0) {
        foreach ($activities as $activityData) {
          $activityData['budget_category_id'] = $category->id;
          BudgetCategoryActivity::create($activityData);
          $category['activities'][] = $activityData;
        }
      }
      $budget['categories'][] = $category;
    }
    return $budget;
  }

  public function updateBudget($budgetId, $data)
  {
    $budget = Budget::findOrFail($budgetId);
    $budget->update($data);

    if (isset($data['categories'])) {
      $categoriesData = $data['categories'];

      foreach ($categoriesData as $i => $categoryData) {
        $categoryData['budget_id'] = $budget->id;

        $category = $budget->categories()->updateOrCreate(
          ['id' => $categoryData['id'] ?? null],
          $categoryData
        );

        $categoriesData[$i]['id'] = $category->id;

        if (isset($categoryData['activities'])) {
          $activitiesData = $categoryData['activities'];

          foreach ($activitiesData as $i => $activityData) {
            $activityData['budget_category_id'] = $category->id;

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
    $budget->categories()->whereNotIn('id', array_column($categoriesData, 'id'))->delete();

    return $budget;
  }
}
