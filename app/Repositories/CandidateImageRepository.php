<?php

namespace App\Repositories;

use App\Models\CandidateImage;

/**
 * Class CandidateImage
 */
class CandidateImageRepository extends BaseRepository
{
    protected $modelName = CandidateImage::class;

    /**
     * CandidateImage constructor.
     */
    public function __construct(CandidateImage $model)
    {
        $this->model = $model;
    }
}
