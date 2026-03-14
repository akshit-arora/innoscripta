<?php
namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Innoscripta News API",
    description: "API documentation for the News Aggregation service",
    contact: new OA\Contact(email: "support@innoscripta.com")
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Main API Server"
)]
abstract class Controller
{
    //
}
