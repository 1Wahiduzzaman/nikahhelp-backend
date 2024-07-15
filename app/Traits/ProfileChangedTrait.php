<?php

namespace App\Traits;

trait ProfileChangedTrait
{
    public function isEditingTextField(array $prop, array $requestParam): bool
    {
        $parameters = collect($requestParam);

        return collect($prop)->reduce(function ($property, $key) use ($parameters) {
            return $property->get($key) !== null && strcmp($property->get($key), $parameters->get($key)) !== 0;
        });
    }
}
