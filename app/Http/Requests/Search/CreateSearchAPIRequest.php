<?php

namespace App\Http\Requests\Search;

use App\Http\Requests\APIRequest;

class CreateSearchAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "page" => 'numeric',
            "perpage" => 'numeric',
            "min_age" => 'required|numeric|min:18',
            "max_age" => 'required|numeric|max:80',
            "gender" => 'required|boolean',
            "country" => 'string',
            "religion" => 'required|string',
        ];
    }
}
