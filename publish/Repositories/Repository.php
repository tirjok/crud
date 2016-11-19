<?php

namespace App\Repositories;

use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Class Repository
 */
abstract class Repository
{
    /**
     * @var int
     */
    protected $recordPerPage = 20;

    /**
     * @var array
     */
    public static $unwantedDataKeys = [
        '_token',
        '_method'
    ];

    /**
     * @var App
     */
    private $app;

    /**
     * @var
     */
    protected $model;

    /**
     * @var
     */
    protected $newModel;

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    abstract public  function model();

    /**
     * Filter data based on user input
     *
     * @param array $filter
     * @param $query
     */
    abstract public function filterData(array $filter, $query);

    /**
     * Get paginated filtered data.
     *
     * @param array $filter
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getFilterWithPaginatedData(array $filter)
    {
        $query = $this->getQuery();

        if (!empty($filter)) {
            $this->filterData($filter, $query);
        }

        return $query->orderBy('id', 'DESC')->paginate($this->recordPerPage);
    }


    /**
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     * @param        $id
     * @param string $attribute
     *
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $this->removeUnwantedData($data);
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = array('*'))
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * @param       $attribute
     * @param       $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = array('*'))
    {
        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findByAll($attribute, $value, $columns = array('*'))
    {
        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws NotFoundResourceException
     */
    public function makeModel()
    {
        return $this->setModel($this->model());
    }

    /**
     * Set Eloquent Model to instantiate
     *
     * @param $eloquentModel
     *
     * @return Model
     * @throws NotFoundResourceException
     */
    public function setModel($eloquentModel)
    {
        $this->newModel = $this->app->make($eloquentModel);

        if (!$this->newModel instanceof Model)
            throw new NotFoundResourceException("Class {$this->newModel} must be an instance of Illuminate\\Database\\Eloquent\\Model");

        return $this->model = $this->newModel;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getQuery()
    {
        return $this->model->newQuery();
    }

    /**
     * @return mixed
     */
    public function getFillable()
    {
        return $this->model->getFillable();
    }

    /**
     * @param $data
     */
    protected function removeUnwantedData(&$data)
    {
        foreach ($data as $key=>$value) {
            if (in_array($key, self::$unwantedDataKeys)) {
                unset($data[$key]);
            }
        }
    }
}
