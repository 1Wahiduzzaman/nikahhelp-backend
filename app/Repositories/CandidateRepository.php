<?php

namespace App\Repositories;

use App\Http\Requests\UserNotificationRequest;
use App\Models\CandidateInformation;
use App\Http\Requests\DeviceTokenRequest;
use Illuminate\Http\Request;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class CandidateRepository extends BaseRepository
{
    protected $modelName = CandidateInformation::class;

    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(CandidateInformation $model)
    {
        $this->model = $model;
    }

    public function getCandidateSuggestions($id)
    {
         $result = $this->model->newQuery();
//        $result->where('is_publish','=',1);
//        $result->where('data_input_status','=',1);
        return $result->get();

    }

}
