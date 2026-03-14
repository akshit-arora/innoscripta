<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleIndexRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleController extends Controller
{
    /**
     * Display a listing of the articles.
     *
     * @param ArticleIndexRequest $request
     * @return ResourceCollection
     */
    public function index(ArticleIndexRequest $request): ResourceCollection
    {
        $filters = $request->validated();

        $articles = Article::filter($filters)
            ->withPreferences($filters['preferences'] ?? [])
            ->latest()
            ->paginate(10);

        return ArticleResource::collection($articles);
    }
}
