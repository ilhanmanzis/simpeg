<?php

namespace App\Services\Kmeans;

class NormalisasiService
{
    public function minMax(array $data, string $key)
    {
        $values = array_column($data, $key);
        $min = min($values);
        $max = max($values);

        return array_map(function ($item) use ($key, $min, $max) {
            $item[$key . '_norm'] = ($max - $min) == 0
                ? 0
                : ($item[$key] - $min) / ($max - $min);
            return $item;
        }, $data);
    }
}
