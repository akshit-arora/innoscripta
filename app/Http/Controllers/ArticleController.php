<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleIndexRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Resources\Json\ResourceCollection;

use OpenApi\Attributes as OA;

class ArticleController extends Controller
{
    #[OA\Get(
        path: "/api/articles",
        summary: "Get list of articles",
        description: "Returns list of articles with optional filtering and user preferences",
        operationId: "getArticlesList",
        tags: ["Articles"],
        parameters: [
            new OA\Parameter(name: "search", in: "query", description: "Search by title, description or content", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "date", in: "query", description: "Filter by date (Y-m-d)", required: false, schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "category", in: "query", description: "Filter by category", required: false, schema: new OA\Schema(ref: "#/components/schemas/ArticleCategory")),
            new OA\Parameter(name: "source", in: "query", description: "Filter by source", required: false, schema: new OA\Schema(ref: "#/components/schemas/NewsSource")),
            new OA\Parameter(name: "preferences[sources][]", in: "query", description: "Preferred sources", required: false, schema: new OA\Schema(type: "array", items: new OA\Items(ref: "#/components/schemas/NewsSource"))),
            new OA\Parameter(name: "preferences[categories][]", in: "query", description: "Preferred categories", required: false, schema: new OA\Schema(type: "array", items: new OA\Items(ref: "#/components/schemas/ArticleCategory"))),
            new OA\Parameter(name: "preferences[authors][]", in: "query", description: "Preferred authors", required: false, schema: new OA\Schema(type: "array", items: new OA\Items(type: "string"))),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/Article")),
                        new OA\Property(property: "links", type: "object", properties: [
                            new OA\Property(property: "first", type: "string"),
                            new OA\Property(property: "last", type: "string"),
                            new OA\Property(property: "prev", type: "string"),
                            new OA\Property(property: "next", type: "string"),
                        ]),
                        new OA\Property(property: "meta", type: "object", properties: [
                            new OA\Property(property: "current_page", type: "integer"),
                            new OA\Property(property: "from", type: "integer"),
                            new OA\Property(property: "last_page", type: "integer"),
                            new OA\Property(property: "path", type: "string"),
                            new OA\Property(property: "per_page", type: "integer"),
                            new OA\Property(property: "to", type: "integer"),
                            new OA\Property(property: "total", type: "integer"),
                        ]),
                    ]
                )
            )
        ]
    )]
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
