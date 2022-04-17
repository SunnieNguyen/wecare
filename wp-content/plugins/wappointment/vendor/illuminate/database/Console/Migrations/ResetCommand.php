<?php

namespace WappoVendor\Illuminate\Database\Console\Migrations;

use WappoVendor\Illuminate\Console\ConfirmableTrait;
use WappoVendor\Illuminate\Database\Migrations\Migrator;
use WappoVendor\Symfony\Component\Console\Input\InputOption;
class ResetCommand extends \WappoVendor\Illuminate\Database\Console\Migrations\BaseCommand
{
    use ConfirmableTrait;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migrate:reset';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback all database migrations';
    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;
    /**
     * Create a new migration rollback command instance.
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
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
        if (!$this->confirmToProceed()) {
            return;
        }
        $this->migrator->setConnection($this->option('database'));
        // First, we'll make sure that the migration table actually exists before we
        // start trying to rollback and re-run all of the migrations. If it's not
        // present we'll just bail out with an info message for the developers.
        if (!$this->migrator->repositoryExists()) {
            return $this->comment('Migration table not found.');
        }
        $this->migrator->reset($this->getMigrationPaths(), $this->option('pretend'));
        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }
    }
    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [['database', null, \WappoVendor\Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'The database connection to use.'], ['force', null, \WappoVendor\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Force the operation to run when in production.'], ['path', null, \WappoVendor\Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL | \WappoVendor\Symfony\Component\Console\Input\InputOption::VALUE_IS_ARRAY, 'The path(s) of migrations files to be executed.'], ['pretend', null, \WappoVendor\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.']];
    }
}
