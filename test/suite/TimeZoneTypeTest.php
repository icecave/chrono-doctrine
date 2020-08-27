<?php
namespace Icecave\Chrono\Doctrine;

use Doctrine\DBAL\Types\Type;
use Icecave\Chrono\TimeZone;
use Phake;
use PHPUnit\Framework\TestCase;

class TimeZoneTypeTest extends TestCase
{
    public function setUp(): void
    {
        $installer = new DoctrineTypeInstaller();
        $installer->installTypes();

        $this->type = Type::getType('chrono_timezone');
        $this->platform = Phake::mock('Doctrine\DBAL\Platforms\AbstractPlatform');
    }

    public function testGetName()
    {
        $this->assertSame('chrono_timezone', $this->type->getName());
    }

    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testToPhp()
    {
        $this->assertEquals(
            TimeZone::fromIsoString('-05:00'),
            $this->type->convertToPHPValue('-05:00', $this->platform)
        );
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testToDatabase()
    {
        $this->assertSame(
            '-05:00',
            $this->type->convertToDatabaseValue(TimeZone::fromIsoString('-05:00'), $this->platform)
        );
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testToDatabaseFailureInvalidType()
    {
        $this->expectException('Doctrine\DBAL\Types\ConversionException');
        $this->type->convertToDatabaseValue('value', $this->platform);
    }
}
