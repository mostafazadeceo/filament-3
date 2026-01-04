<?php

namespace Haida\FilamentCommerceExperience\Http\Controllers\Api\V1;

use Haida\FilamentCommerceExperience\Http\Resources\ExperienceQuestionResource;
use Haida\FilamentCommerceExperience\Models\ExperienceQuestion;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuestionController
{
    public function index(): AnonymousResourceCollection
    {
        $questions = ExperienceQuestion::query()->latest()->paginate();

        return ExperienceQuestionResource::collection($questions);
    }
}
