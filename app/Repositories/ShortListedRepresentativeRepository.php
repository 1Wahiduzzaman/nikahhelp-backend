<?php

namespace App\Repositories;

use App\Models\ShortListedCandidate;
use App\Models\ShortListedRepresentative;
use App\Repositories\BaseRepository;

/**
 * Class ShortListedRepresentativeRepository
 * @package App\Repositories
 * @version April 29, 2021, 7:36 am UTC
 */
class ShortListedRepresentativeRepository extends BaseRepository
{

    protected $modelName = ShortListedRepresentative::class;


    /**
     * UserRepository constructor.
     *
     * @param ShortListedRepresentative $model
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
