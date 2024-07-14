<?php

namespace App\Repositories;

use App\Models\CandidateInformation;

/**
 * Class SearchRepository
 *
 * @version June 8, 2021, 7:37 am UTC
 */
class SearchRepository extends BaseRepository
{
    protected $modelName = CandidateInformation::class;

    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * UserRepository constructor.
     *
     * @param  User  $model
     */
    public function __construct(CandidateInformation $model)
    {
        $this->model = $model;
    }
}
