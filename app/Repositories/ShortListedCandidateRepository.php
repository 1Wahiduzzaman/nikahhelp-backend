<?php

namespace App\Repositories;

use App\Models\ShortListedCandidate;

/**
 * Class ShortListedCandidateRepository
 *
 * @version April 29, 2021, 7:36 am UTC
 */
class ShortListedCandidateRepository extends BaseRepository
{
    protected $modelName = ShortListedCandidate::class;

    /**
     * UserRepository constructor.
     */
    public function __construct(ShortListedCandidate $model)
    {
        $this->model = $model;
    }

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
     * Configure the Model
     **/
    public function model()
    {
        return ShortListedCandidate::class;
    }

    public function deletedCandidate()
    {
        return $onlySoftDeleted = ShortListedCandidate::onlyTrashed()->get();
    }

    public function shortListByCandidate($userId, $shortlistedBy)
    {
        return $result = ShortListedCandidate::where('user_id', '=', $userId)->first();
    }
}
