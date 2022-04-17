<?php

namespace WappoVendor\Illuminate\Database\Console\Migrations;

use WappoVendor\Illuminate\Support\Collection;
use WappoVendor\Illuminate\Database\Migrations\Migrator;
use WappoVendor\Symfony\Component\Console\Input\InputOption;
class StatusCommand extends \WappoVendor\Illuminate\Database\Console\Migrations\BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migrate:status';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the status of each migration';
    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;
    /**
     * Create a new migration rollback command instance.
     *
     * @param  \Illuminate\Database\Migrations\Migrator $migrator
     * @return void
     */
    public function __construct(\WappoVendor\Illuminate\Database\Migrations\Migrator $migrator)
    {
        parent::__construct();
        $this->migrator = $migrator;
    }
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->migrator->setConnection($this->option('database'));
        if (!$this->migrator->repositoryExists()) {
            return $this->error('No migrations found.');
        }
        $ran = $this->migrator->getRepository()->getRan();
        if (\count($migrations = $this->getStatusFor($ran)) > 0) {
            $this->table(['Ran?', 'Migration'], $migrations);
        } else {
            $this->error('No migrations found');
        }
    }
    /**
     * Get the status for the given ran migrations.
     *
     * @param  array  $ran
     * @return \Illuminate\Support\Collection
     */
    protected function getStatusFor(array $ran)
    {
        return \WappoVendor\Illuminate\Support\Collection::make($this->getAllMigrationFiles())->map(function ($migration) use($ran) {
            $migrationName = $this->migrator->getMigrationName($migration);
            return \in_array($migrationName, $ran) ? ['<info>Y</info>', $migrationName] : ['<fg=red>N</fg=red>', $migrationName];
        });
    }
    /**
     * Get an array of all of the migration files.
     *
     * @return array
     */
    protected function getAllMigrationFiles()
    {
        return $this->migrator->getMigrationFiles($this->getMigrationPaths());
    }
    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [['database', null, \WappoVendor\Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'The database connection to use.'], ['path', null, \WappoVendor\Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'The path of migrations files to use.']];
    }
}
