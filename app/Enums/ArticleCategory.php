<?php

namespace App\Enums;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ArticleCategory",
    title: "Article Category",
    description: "Article category enum",
    type: "string",
)]
enum ArticleCategory: string
{
    case BUSINESS = 'business';
    case ENTERTAINMENT = 'entertainment';
    case HEALTH = 'health';
    case SCIENCE = 'science';
    case SPORTS = 'sports';
    case TECHNOLOGY = 'technology';
    case GENERAL = 'general';
}
