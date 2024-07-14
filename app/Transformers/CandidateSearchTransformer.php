<?php

namespace App\Transformers;

use App\Models\CandidateImage;
use App\Models\CandidateInformation;
use App\Models\Religion;
use App\Models\User;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

/**
 * Class CandidateSearchTransformer
 * @package App\Transformers
 */
class CandidateSearchTransformer extends CandidateTransformer
{
	
	public function contact (CandidateInformation $item) 
	{
		return [
            'per_current_residence_country' => $item->per_current_residence_country,
            'per_current_residence_country_name' => $item->getCurrentResidenceCountry()->exists() ? $item->getCurrentResidenceCountry->name : null,
            'per_current_residence_city' => $item->per_current_residence_city,
            'per_permanent_country' => $item->per_permanent_country,
            'per_permanent_country_name' => $item->getPermanentCountry()->exists() ? $item->getPermanentCountry->name :null,
            'per_permanent_city' => $item->per_permanent_city,
            'per_county' => $item->per_county,
        ];
	}

	public function  personal (CandidateInformation $item) 
	{
		 return [
            'per_gender' => (int)$item->per_gender,
            'per_height' => (int)$item->per_height,
            'per_employment_status' => $item->per_employment_status,
            'per_education_level_id' => (int)$item->per_education_level_id,
            'per_education_level' => $item->candidateEducationLevel()->exists() ? $item->candidateEducationLevel->name : null,
            'per_religion_id' => (int)$item->per_religion_id,
            'per_ethnicity' => $item->per_ethnicity,
            'per_mother_tongue' => $item->per_mother_tongue,
            'per_nationality' => (int)$item->per_nationality,
            'per_country_of_birth_id' => (int)$item->per_country_of_birth,
            'per_country_of_birth' => $item->getCountryOFBirth()->exists() ? $item->getCountryOFBirth->name : null,
            'per_current_residence_id' => (int)$item->per_current_residence_country,
            'per_current_residence' => $item->getCurrentResidenceCountry()->exists() ? $item->getCurrentResidenceCountry->name : null,
            'per_marital_status' => $item->per_marital_status,
            'per_have_children' => boolval($item->per_have_children),
            'per_children' => $item->per_children,
            'per_currently_living_with' => $item->per_currently_living_with,
            'per_willing_to_relocate' => (int)$item->per_willing_to_relocate,
            'per_smoker' => boolval($item->per_smoker),
            'per_language_speak' => $item->per_language_speak,
            'per_hobbies_interests' => $item->per_hobbies_interests,
            'per_food_cuisine_like' => $item->per_food_cuisine_like,
            'per_things_enjoy' => $item->per_things_enjoy,
            'per_thankfull_for' => $item->per_thankfull_for,
            'per_about' => $item->per_about,
            'per_image_url' => CandidateImage::getCandidateMainImage($item->user_id),
            'anybody_can_see' => $item->anybody_can_see,
            'only_team_can_see' => $item->only_team_can_see,
            'team_connection_can_see' => $item->team_connection_can_see,
        ];
	}
}