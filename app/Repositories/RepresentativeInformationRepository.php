<?php

namespace App\Repositories;

use App\Models\RepresentativeInformation;

/**
 * Class RepresentativeInformationRepository
 *
 * @version May 3, 2021, 10:52 am UTC
 */
class RepresentativeInformationRepository extends BaseRepository
{
    protected $modelName = RepresentativeInformation::class;

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
        return RepresentativeInformation::class;
    }
}
