<?php

namespace Haida\FilamentRestaurantOps\Policies;

use Haida\FilamentRestaurantOps\Models\RestaurantRecipe;
use Haida\FilamentRestaurantOps\Policies\Concerns\HandlesRestaurantPermissions;

class RestaurantRecipePolicy
{
    use HandlesRestaurantPermissions;

    public function viewAny(): bool
    {
        return $this->allow('restaurant.recipe.view');
    }

    public function view(RestaurantRecipe $recipe): bool
    {
        return $this->allow('restaurant.recipe.view', $recipe);
    }

    public function create(): bool
    {
        return $this->allow('restaurant.recipe.manage');
    }

    public function update(RestaurantRecipe $recipe): bool
    {
        return $this->allow('restaurant.recipe.manage', $recipe);
    }

    public function delete(RestaurantRecipe $recipe): bool
    {
        return $this->allow('restaurant.recipe.manage', $recipe);
    }

    public function restore(RestaurantRecipe $recipe): bool
    {
        return $this->allow('restaurant.recipe.manage', $recipe);
    }

    public function forceDelete(RestaurantRecipe $recipe): bool
    {
        return $this->allow('restaurant.recipe.manage', $recipe);
    }
}
