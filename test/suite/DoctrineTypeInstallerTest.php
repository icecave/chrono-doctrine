<?php
namespace Icecave\Chrono\Doctrine;

use Doctrine\DBAL\Types\Type;
use Eloquent\Liberator\Liberator;
use PHPUnit\Framework\TestCase;

class DoctrineTypeInstallerTest extends TestCase
{
    public function setUp(): void
    {
        $this->installer = new DoctrineTypeInstaller();
    }

    public function testInstallTypes()
    {
        $this->installer->installTypes();

        $this->assertTrue(Type::hasType('chrono_date'));
        $this->assertTrue(Type::hasType('chrono_time'));
        $this->assertTrue(Type::hasType('chrono_datetime'));
        $this->assertTrue(Type::hasType('chrono_timezone'));
        $this->assertTrue(Type::hasType('chrono_period'));
        $this->assertTrue(Type::hasType('chrono_duration'));
    }
}
