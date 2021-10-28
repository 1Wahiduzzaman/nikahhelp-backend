<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryCityResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        return [
                    'id'            => $this->id ?? null,
                    'code'          => $this->code ?? null,
                    'name'          => $this->name ?? null,
                    'status'        => $this->status,
                    'cities'        => $this->getCity ?? null
        ];

    }

}
