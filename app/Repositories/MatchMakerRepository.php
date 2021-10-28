<?php

namespace App\Repositories;

use App\Models\MatchMaker;
use App\Repositories\BaseRepository;

/**
 * Class MatchMakerRepository
 * @package App\Repositories
 * @version June 30, 2021, 11:24 am UTC
*/

class MatchMakerRepository extends BaseRepository
{

    protected $modelName = MatchMaker::class;

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
        return MatchMaker::class;
    }
}
