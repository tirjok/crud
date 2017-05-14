<?php

namespace Tirjok\CrudGenerator\Commands;

use Illuminate\Console\GeneratorCommand;

class CrudRequestCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:request
                            {name : The name of the request.}
                            {--crud-name= : The name of the Crud.}
                            {--namespace= : Namespace name of the CRUD.}
                            {--validations= : Validation details for the fields.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new request from form.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return config('crudgenerator.custom_template')
        ? config('crudgenerator.path') . '/request.stub'
        : __DIR__ . '/../stubs/request.stub';
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
        return $rootNamespace . '\Http\Requests';
    }

    /**
     * Build the model class with the given name.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        $validations = rtrim($this->option('validations'), ';');

        $validationRules = $this->buildValidationRulesStr($validations);

        return $this->replaceNamespace($stub, $name)
            ->replaceValidationRules($stub, $validationRules)
            ->replaceClass($stub, $name);
    }

    /**
     * Replace the validationRules for the given stub.
     *
     * @param  string  $stub
     * @param  string  $validationRules
     *
     * @return $this
     */
    protected function replaceValidationRules(&$stub, $validationRules)
    {
        $stub = str_replace(
            '{{validationRules}}', $validationRules, $stub
        );

        return $this;
    }

    /**
     * Replace the pagination placeholder for the given stub
     *
     * @param $stub
     * @param $perPage
     *
     * @return $this
     */
    protected function replacePaginationNumber(&$stub, $perPage)
    {
        $stub = str_replace(
            '{{pagination}}', $perPage, $stub
        );

        return $this;
    }

    /**
     * Replace the file snippet for the given stub
     *
     * @param $stub
     * @param $fileSnippet
     *
     * @return $this
     */
    protected function replaceFileSnippet(&$stub, $fileSnippet)
    {
        $stub = str_replace(
            '{{fileSnippet}}', $fileSnippet, $stub
        );

        return $this;
    }

    /**
     * Replace the service name for the given stub
     *
     * @param $stub
     * @param $serviceName
     *
     * @return $this
     */
    protected function replaceServiceName(&$stub, $serviceName)
    {
        $stub = str_replace(
            '{{serviceName}}', $serviceName, $stub
        );

        return $this;
    }

    /**
     * Build Validation rules string
     *
     * @param $validations
     *
     * @return string
     */
    protected function buildValidationRulesStr($validations)
    {
        $validationRules = '';
        if (trim($validations) != '') {
            $validationRules = "\$this->validate(\$request, [";

            $rules = explode(';', $validations);
            foreach ($rules as $v) {
                if (trim($v) == '') {
                    continue;
                }

                // extract field name and args
                $parts = explode('#', $v);
                $fieldName = trim($parts[0]);
                $rules = trim($parts[1]);
                $validationRules .= "\n\t\t\t'$fieldName' => '$rules',";
            }

            $validationRules = substr($validationRules, 0, -1); // lose the last comma
            $validationRules .= "\n\t\t]);";
        }
        return $validationRules;
    }
}
