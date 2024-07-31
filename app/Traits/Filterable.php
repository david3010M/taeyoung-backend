<?php

namespace App\Traits;

use App\Utils\Constants;

trait Filterable
{
    protected function applyFilters($query, $request, $filters)
    {
        foreach ($filters as $filter) {
            if ($value = $request->query($filter)) {
                $query->where($filter, 'like', '%' . $value . '%');
            }
        }
        return $query;
    }

    protected function getFilteredResults($model, $request, $filters, $resource)
    {
        $query = $this->applyFilters($model::query(), $request, $filters);

        $all = $request->query('all', false) === 'true';
        $results = $all ? $query->get() : $query->paginate($request->query('per_page', Constants::DEFAULT_PER_PAGE));

        return $all ? response()->json($resource::collection($results)) : $resource::collection($results);
    }
}
