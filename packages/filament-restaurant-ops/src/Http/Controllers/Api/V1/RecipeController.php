<?php

namespace Haida\FilamentRestaurantOps\Http\Controllers\Api\V1;

use Haida\FilamentRestaurantOps\Http\Requests\StoreRecipeRequest;
use Haida\FilamentRestaurantOps\Http\Requests\UpdateRecipeRequest;
use Haida\FilamentRestaurantOps\Http\Resources\RecipeResource;
use Haida\FilamentRestaurantOps\Models\RestaurantRecipe;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class RecipeController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(RestaurantRecipe::class, 'recipe');
    }

    public function index(): AnonymousResourceCollection
    {
        $recipes = RestaurantRecipe::query()
            ->with('lines')
            ->latest('created_at')
            ->paginate();

        return RecipeResource::collection($recipes);
    }

    public function show(RestaurantRecipe $recipe): RecipeResource
    {
        $recipe->load('lines');

        return new RecipeResource($recipe);
    }

    public function store(StoreRecipeRequest $request): RecipeResource
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? [];
        unset($data['lines']);

        $recipe = DB::transaction(function () use ($data, $lines): RestaurantRecipe {
            $recipe = RestaurantRecipe::query()->create($data);
            $this->syncLines($recipe, $lines);

            return $recipe->refresh();
        });

        return new RecipeResource($recipe->load('lines'));
    }

    public function update(UpdateRecipeRequest $request, RestaurantRecipe $recipe): RecipeResource
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? null;
        unset($data['lines']);

        $updated = DB::transaction(function () use ($recipe, $data, $lines): RestaurantRecipe {
            $recipe->update($data);
            if (is_array($lines)) {
                $this->syncLines($recipe, $lines);
            }

            return $recipe->refresh();
        });

        return new RecipeResource($updated->load('lines'));
    }

    public function destroy(RestaurantRecipe $recipe): array
    {
        $recipe->delete();

        return ['status' => 'ok'];
    }

    protected function syncLines(RestaurantRecipe $recipe, array $lines): void
    {
        $recipe->lines()->delete();

        foreach ($lines as $line) {
            $recipe->lines()->create([
                'item_id' => $line['item_id'] ?? null,
                'uom_id' => $line['uom_id'] ?? null,
                'quantity' => $line['quantity'] ?? 0,
                'waste_percent' => $line['waste_percent'] ?? 0,
                'is_optional' => $line['is_optional'] ?? false,
            ]);
        }
    }
}
