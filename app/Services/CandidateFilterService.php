<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class CandidateFilterService
{
    public function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            if (method_exists($this, $method = 'filterBy' . ucfirst($key))) {
                $this->{$method}($query, $value);
            }
        }

        return $query;
    }

    protected function filterByStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }

    protected function filterByTags(Builder $query, array $tags): void
    {
        $query->whereJsonContains('candidate_tags', $tags);
    }

    protected function filterByDateRange(Builder $query, array $dateRange): void
    {
        $query->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
    }
}