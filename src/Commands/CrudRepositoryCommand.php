<?php

namespace Tirjok\CrudGenerator\Commands;

use Illuminate\Console\GeneratorCommand;

class CrudRepositoryCommand extends GeneratorCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:repository
                            {name : The name of the repository.}
                            {--model-name= : The name of the Crud.}
                            {--filters= : The fields to add filter.}
                            {--namespace= : The name of the Namespace}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return config('crudgenerator.custom_template')
            ? config('crudgenerator.path') . '/repository.stub'
            : __DIR__ . '/../stubs/repository.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories';
    }

    /**
     * Build the service class with the given name.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $namespace = ($this->option('namespace')) ? $this->option('namespace') : '';
        $modelName = $this->option('model-name');
        $filters = $this->option('filters');
        $className = $modelName.'Repository';
        $modelClass = $namespace.$modelName;

        return $this->replaceNamespace($stub, $name)
            ->replaceModelClass($stub, $modelClass)
            ->replaceModelName($stub, $modelName)
            ->replaceFilterQuery($stub, $filters)
            ->replaceClass($stub, $className);
    }

    /**
     * Replace the modelName for the given stub.
     *
     * @param  string  $stub
     * @param  string  $modelClass
     *
     * @return $this
     */
    protected function replaceModelClass(&$stub, $modelClass)
    {
        $stub = str_replace(
            '{{modelClass}}', $modelClass, $stub
        );

        return $this;
    }

    /**
     * Replace the repositoryName for the given stub.
     *
     * @param $stub
     * @param $modelName
     *
     * @return $this
     */
    protected function replaceModelName(&$stub, $modelName)
    {
        $stub = str_replace(
            '{{modelName}}', $modelName, $stub
        );

        return $this;
    }

    /**
     * Replace the filter query from stub
     *
     * @param $stub
     * @param $filters
     *
     * @return $this
     */
    protected function replaceFilterQuery(&$stub, $filters)
    {
        $filterQuery = $this->buildFilterQuery($filters);

        $stub = str_replace(
            '{{filterQuery}}', $filterQuery, $stub
        );

        return $this;
    }

    /**
     * Build query string
     *
     * @param $filters
     *
     * @return string
     */
    private function buildFilterQuery($filters)
    {
        if (!$filters) {
            return '//';
        }

        $filterArray = explode(';', $filters);
        $queryStr = '';

        foreach ($filterArray as $item) {
            $itemParts = explode('#', $item);

            if (count($itemParts) === 2) { // form of input is table_id#like
                $queryStr .= '
        if (isset($filter[\''. $itemParts[0] .'\']) && $filter[\''. $itemParts[0] .'\']) {
            $query->where(\''.$itemParts[0].'\', \''.$itemParts[1].'\', "%{$filter[\''.$itemParts[0].'\']}%");
        }
                ';
            } elseif (count($itemParts) === 1) { // we assume it is equal
                $queryStr .= '
        if (isset($filter[\''. $itemParts[0] .'\']) && $filter[\''. $itemParts[0] .'\']) {
            $query->where(\''.$itemParts[0].'\', $filter[\''.$itemParts[0].'\']);
        }
                ';
            }

        }

        return $queryStr;
    }
}
