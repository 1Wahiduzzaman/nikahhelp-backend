<?php

namespace App\Repositories;

use App\Models\TeamListedCandidate;

/**
 * Class ShortListedCandidateRepository
 *
 * @version April 29, 2021, 7:36 am UTC
 */
class TeamListedCandidateRepository extends BaseRepository
{
    protected $modelName = TeamListedCandidate::class;

    /**
     * UserRepository constructor.
     */
    public function __construct(TeamListedCandidate $model)
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
        return TeamListedCandidate::class;
    }

    public function deletedCandidate()
    {
        return $onlySoftDeleted = TeamListedCandidate::onlyTrashed()->get();
    }

    public function shortListByCandidate($userId, $shortlistedBy)
    {
        return $result = TeamListedCandidate::where('user_id', '=', $userId)->first();
    }
}
