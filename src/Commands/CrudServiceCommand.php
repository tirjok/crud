<?php

namespace Tirjok\CrudGenerator\Commands;

use Illuminate\Console\GeneratorCommand;

class CrudServiceCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:service
                            {name : The name of the Service.}
                            {--model-name= : The name of the Model.}
                            {--namespace= : The name of the Namespace}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Service';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return config('crudgenerator.custom_template')
            ? config('crudgenerator.path') . '/service.stub'
            : __DIR__ . '/../stubs/service.stub';
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
        return $rootNamespace . '\Services';
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
        $className = $modelName.'Service';
        $repositoryName = $namespace.$modelName.'Repository';

        return $this->replaceNamespace($stub, $name)
            ->replaceModelName($stub, $modelName)
            ->replaceRepositoryName($stub, $repositoryName)
            ->replaceClass($stub, $className);
    }

    /**
     * Replace the modelName for the given stub.
     *
     * @param  string  $stub
     * @param  string  $modelName
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
     * Replace the repositoryName for the given stub.
     *
     * @param $stub
     * @param $repositoryName
     *
     * @return $this
     */
    protected function replaceRepositoryName(&$stub, $repositoryName)
    {
        $stub = str_replace(
            '{{repositoryName}}', $repositoryName, $stub
        );

        return $this;
    }
}