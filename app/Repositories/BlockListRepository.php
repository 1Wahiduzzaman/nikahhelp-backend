<?php

namespace App\Repositories;

use App\Models\BlockList;

/**
 * Class block_listRepository
 *
 * @version May 20, 2021, 12:35 pm UTC
 */
class BlockListRepository extends BaseRepository
{
    protected $modelName = BlockList::class;

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
    public function __construct(BlockList $model)
    {
        $this->model = $model;
    }
}
