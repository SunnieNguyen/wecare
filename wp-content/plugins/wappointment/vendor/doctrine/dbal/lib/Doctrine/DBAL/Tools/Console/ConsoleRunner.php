<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */
namespace WappoVendor\Doctrine\DBAL\Tools\Console;

use WappoVendor\Doctrine\DBAL\Connection;
use WappoVendor\Doctrine\DBAL\Tools\Console\Command\ImportCommand;
use WappoVendor\Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand;
use WappoVendor\Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use WappoVendor\Symfony\Component\Console\Helper\HelperSet;
use WappoVendor\Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use WappoVendor\Symfony\Component\Console\Application;
use WappoVendor\Doctrine\DBAL\Version;
/**
 * Handles running the Console Tools inside Symfony Console context.
 */
class ConsoleRunner
{
    /**
     * Create a Symfony Console HelperSet
     *
     * @param Connection $connection
     *
     * @return HelperSet
     */
    public static function createHelperSet(\WappoVendor\Doctrine\DBAL\Connection $connection)
    {
        return new \WappoVendor\Symfony\Component\Console\Helper\HelperSet(array('db' => new \WappoVendor\Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($connection)));
    }
    /**
     * Runs console with the given helperset.
     *
     * @param \Symfony\Component\Console\Helper\HelperSet  $helperSet
     * @param \Symfony\Component\Console\Command\Command[] $commands
     *
     * @return void
     */
    public static function run(\WappoVendor\Symfony\Component\Console\Helper\HelperSet $helperSet, $commands = array())
    {
        $cli = new \WappoVendor\Symfony\Component\Console\Application('Doctrine Command Line Interface', \WappoVendor\Doctrine\DBAL\Version::VERSION);
        $cli->setCatchExceptions(true);
        $cli->setHelperSet($helperSet);
        self::addCommands($cli);
        $cli->addCommands($commands);
        $cli->run();
    }
    /**
     * @param Application $cli
     *
     * @return void
     */
    public static function addCommands(\WappoVendor\Symfony\Component\Console\Application $cli)
    {
        $cli->addCommands(array(new \WappoVendor\Doctrine\DBAL\Tools\Console\Command\RunSqlCommand(), new \WappoVendor\Doctrine\DBAL\Tools\Console\Command\ImportCommand(), new \WappoVendor\Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand()));
    }
    /**
     * Prints the instructions to create a configuration file
     */
    public static function printCliConfigTemplate()
    {
        echo <<<'HELP'
You are missing a "cli-config.php" or "config/cli-config.php" file in your
project, which is required to get the Doctrine-DBAL Console working. You can use the
following sample as a template:

<?php
use Doctrine\DBAL\Tools\Console\ConsoleRunner;

// replace with the mechanism to retrieve DBAL connection in your app
$connection = getDBALConnection();

// You can append new commands to $commands array, if needed

return ConsoleRunner::createHelperSet($connection);

HELP;
    }
}
