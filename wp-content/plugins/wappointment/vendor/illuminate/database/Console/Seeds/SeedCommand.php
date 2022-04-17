<?php

namespace WappoVendor\Illuminate\Database\Console\Seeds;

use WappoVendor\Illuminate\Console\Command;
use WappoVendor\Illuminate\Database\Eloquent\Model;
use WappoVendor\Illuminate\Console\ConfirmableTrait;
use WappoVendor\Symfony\Component\Console\Input\InputOption;
use WappoVendor\Illuminate\Database\ConnectionResolverInterface as Resolver;
class SeedCommand extends \WappoVendor\Illuminate\Console\Command
{
    use ConfirmableTrait;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:seed';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with records';
    /**
     * The connection resolver instance.
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $resolver;
    /**
     * Create a new database seed command instance.
     *
     * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
     * @return void
     */
    public function __construct(\WappoVendor\Illuminate\Database\ConnectionResolverInterface $resolver)
    {
        parent::__construct();
        $this->resolver = $resolver;
    }
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return;
        }
        $this->resolver->setDefaultConnection($this->getDatabase());
        \WappoVendor\Illuminate\Database\Eloquent\Model::unguarded(function () {
            $this->getSeeder()->__invoke();
        });
    }
    /**
     * Get a seeder instance from the container.
     *
     * @return \Illuminate\Database\Seeder
     */
    protected function getSeeder()
    {
        $class = $this->laravel->make($this->input->getOption('class'));
        return $class->setContainer($this->laravel)->setCommand($this);
    }
    /**
     * Get the name of the database connection to use.
     *
     * @return string
     */
    protected function getDatabase()
    {
        $database = $this->input->getOption('database');
        return $database ?: $this->laravel['config']['database.default'];
    }
    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [['class', null, \WappoVendor\Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'The class name of the root seeder', 'DatabaseSeeder'], ['database', null, \WappoVendor\Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'The database connection to seed'], ['force', null, \WappoVendor\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Force the operation to run when in production.']];
    }
}
