<?php
namespace LazyRecord\Command\SchemaCommand;
use LazyRecord\Command\BaseCommand;
use LazyRecord\Schema\SchemaGenerator;
use LazyRecord\Schema\SchemaUtils;

/**
 * $ lazy build-schema path/to/Schema path/to/SchemaDir
 *
 */
class BuildCommand extends BaseCommand
{

    public function usage()
    {
        return 'schema build [paths|classes]';
    }

    public function brief()
    {
        return 'build schema files.';
    }

    public function arguments($args)
    {
        $args->add('file')
            ->isa('file')
            ;
    }

    public function options($opts) 
    {
        $opts->add('f|force','force generate all schema files.');
        parent::options($opts);
    }

    public function execute()
    {
        $logger = $this->getLogger();


        $config = $this->getConfigLoader();

        $this->logger->debug('Finding schemas...');
        $classes = $this->findSchemasByArguments(func_get_args());

        $this->logger->debug("Initializing schema generator...");

        $generator = new SchemaGenerator($config, $this->logger);

        if ($this->options->force) {
            $generator->setForceUpdate(true);
        }


        // for generated class source code.
        $this->logger->debug("Setting up error handler...");
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            printf( "ERROR %s:%s  [%s] %s\n" , $errfile, $errline, $errno, $errstr );
        }, E_ERROR );
        $classMap = $generator->generate($classes, $this->options->force);

        $this->logger->debug("Restoring error handler...");
        restore_error_handler();
    }
}

