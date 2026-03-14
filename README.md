# News Aggregator API for Innoscripta Case Study

A RESTful API built with Laravel for aggregating news from multiple sources (NewsAPI, The New York Times, OpenNews) and providing a unified, searchable, and filterable interface for clients.

## Features

- **Multi-Source Aggregation**: Fetches articles from various external news APIs.
- **Unified Article Format**: standardizes diverse structured data into a consistent internal model (`ArticleDTO`).
- **Advanced Filtering**: Enables searching by keyword, and filtering by category, source, date, and user preferences.
- **Automated Fetching**: Uses Laravel Queues and Jobs (`FetchAndStoreNewsJob`) to periodically pull and upsert articles into the database.
- **Extensible Architecture**: Utilizes Strategy and Mapper design patterns to easily add more news sources in the future.
- **Comprehensive Testing**: Fully tested using the Pest PHP testing framework.

## Requirements

- PHP 8.2+
- Composer
- SQLite / MySQL / PostgreSQL (SQLite is used by default)
- Ext-curl or similar HTTP client extension for PHP

## Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd innoscripta
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Configure the environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Set up Database (SQLite by default):**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

5. **Configure API Keys:**
   Add your API keys to the `.env` file for the supported news sources:
   ```env
   NEWSAPI_API_KEY="your_newsapi_key_here"
   NYTIMES_API_KEY="your_nytimes_key_here"
   ```

## Usage

### Running the Application

To start the local development server:
```bash
php artisan serve
```

To run the queue worker (required to process the background jobs fetching the news):
```bash
php artisan queue:listen
```

### Fetching News Manually
To dispatch jobs to fetch news immediately:
```bash
php artisan news:aggregate
```

### Automated Fetching
The application is pre-configured to fetch news once every day. This is managed in `bootstrap/app.php` using Laravel's task scheduler. To run the scheduler locally:
```bash
php artisan schedule:work
```
In a production environment, you should add a single cron entry to your server:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## API Endpoints

### `GET /api/articles`

Returns a paginated list of articles.

**Query Parameters (Optional):**

- `search` (string): Search by title, description or content.
- `date` (string, `Y-m-d` format): Filter articles published on a specific date.
- `category` (string): Filter by category (e.g., `technology`, `business`, `sports`).
- `source` (string): Filter by source (e.g., `news_api`, `ny_times`, `open_news`).
- `preferences[sources][]` (array): Array of preferred sources.
- `preferences[categories][]` (array): Array of preferred categories.
- `preferences[authors][]` (array): Array of preferred authors.

**Example Request:**
```
GET /api/articles?category=technology&search=AI
```

## Testing

The application uses [Pest](https://pestphp.com/) for testing. 

To run the test suite:
```bash
php artisan test
```

Tests include coverage for:
- API endpoints (`ArticleControllerTest`)
- API fetch strategies & mappers
- Data Transfer Objects (`ArticleDTOTest`)
- Background jobs (`FetchAndStoreNewsJobTest`)

## Architecture Description

The application fetches data from multiple APIs natively via robust abstractions:

*   **Strategies**: Implement `NewsFetcherInterface` allowing uniform interaction with vastly different external structures (e.g. `NewsApiStrategy`, `NYTimesStrategy`, `OpenNewsStrategy`).
*   **Mappers**: Conform different category naming logic per API source into a unified system enum (`ArticleCategory`).
*   **DTOs**: The `ArticleDTO` translates diverse external schemas to a cohesive internal model.
*   **Database Upserts**: Safe periodic operations, avoiding duplicated entries leveraging URL identification.
