<?php

namespace App\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Input;

abstract class BaseService
{
    /**
     * @return \App\Repositories\Repository
     */
    abstract public function getRepository();

    /**
     * Create new item
     *
     * @param array $input
     *
     * @return mixed
     */
    public function create(array $input)
    {
        return $this->getRepository()->create($input);
    }

    /**
     * Return all
     *
     * @return mixed
     */
    public function all()
    {
        $filter = Input::get('filters', []);
        return $this->getRepository()->getFilterWithPaginatedData($filter);
    }

    /**
     * Item updated
     *
     * @param array $input
     * @param       $id
     *
     * @return mixed
     */
    public function update(array $input, $id)
    {

        $item = $this->find($id);

        if ($item) {
            return $this->getRepository()->update($input, $id);
        }

        return false;
    }

    /**
     * Item delete
     *
     * @param $id
     *
     * @return bool
     */
    public function delete($id)
    {
        $item = $this->find($id);

        if ($item) {
            return $this->getRepository()->delete($id);
        }

        return false;
    }

    /**
     * Find item
     *
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        try {
            return $this->getRepository()->find($id);
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}