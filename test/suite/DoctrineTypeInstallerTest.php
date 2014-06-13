<?php
namespace Icecave\Chrono\Doctrine;

use Doctrine\DBAL\Types\Type;
use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

class DoctrineTypeInstallerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->installer = new DoctrineTypeInstaller;

        Liberator::liberateClass('Doctrine\DBAL\Types\Type')->_typesMap = array();
    }

    public function testInstallTypes()
    {
        $this->installer->installTypes();

        $this->assertSame(
            array(
                'chrono_date' => __NAMESPACE__ . '\DateType',
                'chrono_datetime' => __NAMESPACE__ . '\DateTimeType',
            ),
            Type::getTypesMap()
        );
    }
}
