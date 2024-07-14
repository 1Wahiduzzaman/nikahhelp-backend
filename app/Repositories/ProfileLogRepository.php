<?php

namespace App\Repositories;

use App\Models\ProfileLog;

/**
 * Class UserRepository
 */
class ProfileLogRepository extends BaseRepository
{
    protected $modelName = ProfileLog::class;

    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];

    /**
     * UserRepository constructor.
     */
    public function __construct(ProfileLog $model)
    {
        $this->model = $model;
    }

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }
}
