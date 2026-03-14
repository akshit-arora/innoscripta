<?php

namespace App\Enums;

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
