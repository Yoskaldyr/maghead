<?php
namespace LazyRecord\Testing;
use LazyRecord\ConnectionManager;
use LazyRecord\SqlBuilder;
use LazyRecord\ConfigLoader;
use LazyRecord\Command\CommandUtils;
use LazyRecord\Schema\SchemaGenerator;
use LazyRecord\ClassUtils;
use LazyRecord\Result;
use LazyRecord\SqlBuilder\SqlBuilder;
use LazyRecord\Testing\BaseTestCase;
use PHPUnit_Framework_TestCase;

abstract class ModelTestCase extends BaseTestCase
{
    public $schemaHasBeenBuilt = false;

    public $schemaClasses = array( );

    public function setUp()
    {
        $annnotations = $this->getAnnotations();
        ob_start();

        if ($dsn = $this->getDSN()) {
            $this->config = array('dsn' => $dsn);
            $user = $this->getDatabaseUser();
            $pass = $this->getDatabasePassword();
            $this->config['user'] = $user;
            $this->config['pass'] = $pass;
            ConnectionManager::getInstance()->addDataSource('default', $this->config);
        }
        elseif ( $this->getDatabaseName() ) {
            ConnectionManager::getInstance()->addDataSource('default', array( 
                'driver' => $this->driver,
                'database'  => $this->getDatabaseName(),
                'user' => $this->getDatabaseUser(),
                'pass' => $this->getDatabasePassword(),
            ));
        } else {
            $this->markTestSkipped("{$this->driver} database configuration is missing.");
        }

        try {
            $dbh = ConnectionManager::getInstance()->getConnection('default');
        } catch (PDOException $e) {
            $this->markTestSkipped('Can not connect to database, test skipped: ' . $e->getMessage() );
            return;
        }

        $driver = ConnectionManager::getInstance()->getQueryDriver('default');
        ok($driver,'QueryDriver object OK');


        $rebuild = true;
        if (isset($annnotations['method']['rebuild'][0]) && $annnotations['method']['rebuild'][0] == 'false') {
            $rebuild = false;
        }

        $builder = SqlBuilder::create($driver , array('rebuild' => $rebuild));
        ok($builder, 'SqlBuilder OK');

        $schemas = ClassUtils::schema_classes_to_objects( $this->getModels() );
        foreach( $schemas as $schema ) {
            $sqls = $builder->build($schema);
            ok( $sqls );
            foreach( $sqls as $sql ) {
                $dbh->query( $sql );
            }
        }
        CommandUtils::build_basedata( $schemas );
        ob_end_clean();
    }

    /**
     * Test cases
     */
    public function testClasses()
    {
        foreach ($this->getModels() as $class ) {
            class_ok($class);
        }
    }

}



