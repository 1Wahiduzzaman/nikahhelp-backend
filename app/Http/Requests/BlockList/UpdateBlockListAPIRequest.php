<?php

namespace App\Http\Requests\BlockList;

use App\Models\BlockList;
use InfyOm\Generator\Request\APIRequest;

class UpdateBlockListAPIRequest extends APIRequest
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
        $rules = BlockList::$rules;

        return $rules;
    }
}
