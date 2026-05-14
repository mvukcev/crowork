<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    protected bool $useMeilisearch;

    public function __construct()
    {
        $this->useMeilisearch = config('services.search.use_meilisearch', false);
    }

    public function search(Builder $query, string $term, array $searchableFields, ?array $sortFields = null): Builder
    {
        if ($this->useMeilisearch) {
            // Placeholder for Meilisearch integration
            // Example: return $this->searchWithMeilisearch($term, $searchableFields, $sortFields);
        }

        return $this->searchWithDatabase($query, $term, $searchableFields, $sortFields);
    }

    protected function searchWithDatabase(Builder $query, string $term, array $searchableFields, ?array $sortFields = null): Builder
    {
        $columns = implode(',', $searchableFields);
        $query->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", [$term]);

        if ($sortFields) {
            foreach ($sortFields as $field => $direction) {
                $query->orderBy($field, $direction);
            }
        }

        return $query;
    }

    // Placeholder for Meilisearch integration
    // protected function searchWithMeilisearch(string $term, array $searchableFields, ?array $sortFields = null): Builder
    // {
    //     // Implement Meilisearch logic here
    // }
}