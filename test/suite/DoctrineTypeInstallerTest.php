<?php
namespace Icecave\Chrono\Doctrine;

use Doctrine\DBAL\Types\Type;
use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

class DoctrineTypeInstallerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->installer = new DoctrineTypeInstaller();

        Liberator::liberateClass('Doctrine\DBAL\Types\Type')->_typesMap = array();
    }

    public function testInstallTypes()
    {
        $this->installer->installTypes();

        $this->assertSame(
            array(
                'chrono_date' => __NAMESPACE__ . '\DateType',
                'chrono_time' => __NAMESPACE__ . '\TimeType',
                'chrono_datetime' => __NAMESPACE__ . '\DateTimeType',
                'chrono_timezone' => __NAMESPACE__ . '\TimeZoneType',
                'chrono_period' => __NAMESPACE__ . '\PeriodType',
                'chrono_duration' => __NAMESPACE__ . '\DurationType',
            ),
            Type::getTypesMap()
        );
    }
}
