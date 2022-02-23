<?php
/**
 * Add ORDER BY statement to a query builder
 * @return object $builder
 * @param object $builder
 * @param array $orderFields
 * @param string $order = asc, desc
 */
function addQueryOrder($builder, $orderFields, $order)
{
    // bulding order query
    $col = 0;
    $dir = "";
    if (!empty($order)) {
        $col = $order[0]['column'];
        $dir = $order[0]['dir'];
    }
    if ($dir != "asc" && $dir != "desc") $dir = "asc";
    if (isset($orderFields[$col])) $builder->orderBy($orderFields[$col], $dir); // add order query to builder
    return $builder;
}

/**
 * Add AND (LIKE '%...%' OR ...) statement to a query builder
 * @return object $builder
 * @param object $builder
 * @param array $searchFields columns to be search
 * @param string $keyword is what to search
 */
function addQuerySearch($builder, $searchFields, $keyword)
{
    // bulding search query
    if (!empty($keyword)) {
        $search = $keyword;
        $search_array = array();
        foreach ($searchFields as $key) $search_array[$key] = $search;
        // add search query to builder
        $builder
            ->groupStart()
            ->orLike($search_array)
            ->groupEnd();
    }
    return $builder;
}
