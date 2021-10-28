<?php


namespace App\Http\Requests\Candidate;

use App\Http\Requests\APIRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;


class CandidatePersonalAboutMoreRequest extends APIRequest
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
            'per_marital_status' => 'required|string',
            'per_have_children' => 'required_if:per_marital_status,divorced_with_children| boolean',
            'per_children' => [
                'required_if:per_have_children,1',
                'array',
                function ($attribute, $values, $fail) {
                    if (!empty($values)):
                        foreach ($values as $key => $value):
                            if (!Arr::exists($value, 'type')) {
                                $fail('The ' . $attribute . ' is invalid.');
                            }
                            if (!Arr::exists($value, 'count')) {
                                $fail('The ' . $attribute . ' is invalid.');
                            }
                            if (!Arr::exists($value, 'age')) {
                                $fail('The ' . $attribute . ' is invalid.');
                            }
                        endforeach;
                    endif;

                }],
            'per_currently_living_with' => 'nullable|string',
            'per_willing_to_relocate' => 'required',
            'per_smoker' => 'required|string',
            'per_language_speak' => 'required|string',
            'per_hobbies_interests' => 'nullable|string',
            'per_food_cuisine_like' => 'nullable|string',
            'per_things_enjoy' => 'nullable|string',
            'per_thankfull_for' => 'nullable|string',
            'per_about' => 'nullable|string',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'per_current_residence.required' => 'The current residence field is required.',
            'per_willing_to_relocate.required' => 'The willing to relocate field is required.',
            'per_smoker.required' => 'The smoker field is required.',
            'per_language_speak.required' => 'The language speak field is required.',
        ];
    }
}
