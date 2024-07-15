<?php

namespace App\Repositories;

use App\Models\ShortListedRepresentative;

/**
 * Class ShortListedRepresentativeRepository
 *
 * @version April 29, 2021, 7:36 am UTC
 */
class ShortListedRepresentativeRepository extends BaseRepository
{
    protected $modelName = ShortListedRepresentative::class;

    /**
     * UserRepository constructor.
     */
    public function __construct(ShortListedRepresentative $model)
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
        return ShortListedRepresentative::class;
    }

    public function deletedCandidate()
    {
        return $onlySoftDeleted = ShortListedRepresentative::onlyTrashed()->get();
    }

    public function shortListByCandidate($userId, $shortlistedBy)
    {
        return $result = ShortListedRepresentative::where('user_id', '=', $userId)->first();
    }
}
