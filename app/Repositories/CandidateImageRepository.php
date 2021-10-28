<?php

namespace App\Repositories;

use App\Http\Requests\UserNotificationRequest;
use App\Models\CandidateImage;
use App\Models\CandidateInformation;
use App\Http\Requests\DeviceTokenRequest;
use Illuminate\Http\Request;

/**
 * Class CandidateImage
 *
 * @package App\Repositories
 */
class CandidateImageRepository  extends BaseRepository
{
    protected $modelName = CandidateImage::class;

    /**
     * CandidateImage constructor.
     *
     * @param CandidateImage $model
     */
    public function __construct(CandidateImage $model)
    {
        $this->model = $model;
    }



}
