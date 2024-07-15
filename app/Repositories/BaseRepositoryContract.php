<?php

namespace App\Repositories;

/**
 * Interface BaseRepositoryContract
 */
interface BaseRepositoryContract
{
    /**
     * Find a resource by id
     *
     * @return Model|null
     */
    public function findOne($id, $relation);

    /**
     * Find a resource by criteria
     *
     * @return Model|null
     */
    public function findOneBy(array $criteria, $relation);

    /**
     * Search All resources by criteria
     *
     * @param  null  $relation
     * @return Collection
     */
    public function findBy(array $searchCriteria = [], $relation = null, ?array $orderBy = null);

    /**
     * Search All resources by any values of a key
     *
     * @param  string  $key
     * @param  null  $relation
     * @return Collection
     */
    public function findIn($key, array $values, $relation = null, ?array $orderBy = null);

    /**
     * @param  null  $perPage
     * @param  null  $relation
     * @return Collection
     */
    public function findAll($perPage = null, $relation = null, ?array $orderBy = null);

    /**
     * @param  null  $relation
     * @return mixed
     */
    public function findOrFail($id, $relation = null, ?array $orderBy = null);

    /**
     * @param  array  $fields  Which fields to select
     * @return \Illuminate\Support\Collection|null|static
     */
    public function findByProperties(array $params, array $fields = ['*']);

    /**
     * Find resource
     *
     * @param  array  $fields  Which fields to select
     * @return Model|null|static
     */
    public function findOneByProperties(array $params, array $fields = ['*']);

    /**
     * Find resources by ids
     *
     * @param  array  $ids
     * @return \Illuminate\Support\Collection|null|static
     */
    public function findByIds($ids);

    /**
     * Retrieve all resources
     *
     * @return \Illuminate\Support\Collection|null|static
     */
    public function getAll();

    /**
     * Save a resource
     *
     * @return Model
     */
    public function save(array $data);

    /**
     * Save resources
     *
     * @param  array|Collection  $resources
     * @return \Illuminate\Support\Collection|null|static
     */
    public function saveMany($resources);

    /**
     * @return \Illuminate\Support\Collection|null|static
     */
    public function update($resource, $data = []);

    /**
     * Delete resources
     *
     * @return \Illuminate\Support\Collection|null|static
     */
    public function delete($resource);

    /**
     * Return model
     *
     * @return Model
     */
    public function getModel();

    /**
     * Creates a new model from properties
     *
     * @return mixed
     */
    public function create(array $properties);
}
