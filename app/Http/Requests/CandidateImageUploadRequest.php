<?php

namespace App\Http\Requests;

use App\Models\CandidateImage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CandidateImageUploadRequest extends APIRequest
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
        $rules = [
            CandidateImage::IMAGE_AVATAR => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            CandidateImage::IMAGE_MAIN => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'image.*.image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'image.*.type' => 'sometimes|numeric|between:1,8',
            'image.*.visibility' => 'sometimes|numeric|between:1,4',
//            CandidateImage::IMAGE  => 'required|image|mimes:jpeg,png,jpg|max:3072',
//            CandidateImage::IMAGE_TYPE => [ 'required','numeric','between:1,8'],
//            CandidateImage::IMAGE_VISIBILITY => [ 'sometimes','numeric','between:1,4'],
        ];

//        if($this->candidate_image){
//            $rules[CandidateImage::USER_ID] = ['required', Rule::exists('candidate_information','user_id')];
//        }else{
//            $rules[CandidateImage::USER_ID] = [
//                'required',
//                Rule::exists('candidate_information','user_id'),
//                Rule::unique('candidate_images')
//                    ->where(function ($query) use($request) {
//                        return $query->where('image_type', $request['image_type'])
//                            ->where('user_id', $request['user_id']);
//                    })];
//        }
        return $rules;

    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
//            'user_id.unique' => 'The image has already taken.'
            'image.*.image.max' => 'The gallery image must not be greater than 2MB',
            'image.*.image.mimes' => 'The gallery image must not be type ( jpeg,png,jpg )'
        ];

    }
}
