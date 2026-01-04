<?php

namespace Haida\FilamentCommerceExperience\Http\Controllers\Api\V1;

use Haida\FilamentCommerceExperience\Http\Resources\ExperienceReviewResource;
use Haida\FilamentCommerceExperience\Models\ExperienceReview;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewController
{
    public function index(): AnonymousResourceCollection
    {
        $reviews = ExperienceReview::query()->latest()->paginate();

        return ExperienceReviewResource::collection($reviews);
    }
}
