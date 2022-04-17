<?php

namespace WappoVendor\Illuminate\Database\Console\Factories;

use WappoVendor\Illuminate\Console\GeneratorCommand;
use WappoVendor\Symfony\Component\Console\Input\InputOption;
class FactoryMakeCommand extends \WappoVendor\Illuminate\Console\GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:factory';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model factory';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Factory';
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/factory.stub';
    }
    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $model = $this->option('model') ? $this->qualifyClass($this->option('model')) : 'Model';
        return \str_replace('DummyModel', $model, parent::buildClass($name));
    }
    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = \str_replace(['\\', '/'], '', $this->argument('name'));
        return $this->laravel->databasePath() . "/factories/{$name}.php";
    }
    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [['model', 'm', \WappoVendor\Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'The name of the model']];
    }
}
