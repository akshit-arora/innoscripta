<?php

namespace App\Http\Requests;

use App\Enums\ArticleCategory;
use App\Enums\NewsSource;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ArticleIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Filters
            'search' => ['nullable', 'string', 'max:100'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'category' => ['nullable', 'in:' . implode(',', array_map(fn($case) => $case->value, ArticleCategory::cases()))],
            'source' => ['nullable', 'in:' . implode(',', array_map(fn($case) => $case->value, NewsSource::cases()))],

            // Stateless User Preferences (to be passed via frontend load)
            'preferences' => ['nullable', 'array'],
            'preferences.sources' => ['nullable', 'array'],
            'preferences.categories' => ['nullable', 'array'],
            'preferences.authors' => ['nullable', 'array'],
            'preferences.sources.*' => ['in:' . implode(',', array_map(fn($case) => $case->value, NewsSource::cases()))],
            'preferences.categories.*' => ['in:' . implode(',', array_map(fn($case) => $case->value, ArticleCategory::cases()))],
            'preferences.authors.*' => ['string'],
        ];
    }
}
