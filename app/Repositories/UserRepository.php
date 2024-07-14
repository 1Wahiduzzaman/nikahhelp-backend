<?php

namespace App\Repositories;

use App\Models\User;

/**
 * Class UserRepository
 */
class UserRepository extends BaseRepository
{
    protected $modelName = User::class;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'email',
        'is_verified',
        'status',
        'locked_at',
        'locked_end',
        'account_type',
    ];

    /**
     * UserRepository constructor.
     */
    public function __construct(User $model)
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
