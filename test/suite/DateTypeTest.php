<?php
namespace Icecave\Chrono\Doctrine;

use Doctrine\DBAL\Types\Type;
use Eloquent\Liberator\Liberator;
use Icecave\Chrono\Clock\SystemClock;
use Icecave\Chrono\Date;
use Icecave\Chrono\TimeZone;
use Phake;
use PHPUnit\Framework\TestCase;

class DateTypeTest extends TestCase
{
    public function setUp(): void
    {
        $installer = new DoctrineTypeInstaller();
        $installer->installTypes();

        $this->type = Type::getType('chrono_date');
        $this->liberatedType = Liberator::liberate($this->type);

        $this->clock = Phake::mock('Icecave\Chrono\Clock\ClockInterface');
        $this->timeZone = TimeZone::fromIsoString('-05:00');
        $this->platform = Phake::mock('Doctrine\DBAL\Platforms\AbstractPlatform');

        $this->liberatedType->clock = $this->clock;
        Phake::when($this->clock)->timeZone()->thenReturn($this->timeZone);
    }

    public function testGetName()
    {
        $this->assertSame('chrono_date', $this->type->getName());
    }

    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testToPhp()
    {
        $this->assertEquals(
            Date::fromIsoString('2001-01-01-05:00'),
            $this->type->convertToPHPValue('2001-01-01', $this->platform)
        );
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testToDatabase()
    {
        $this->assertSame(
            '2001-01-01',
            $this->type->convertToDatabaseValue(Date::fromIsoString('2001-01-01-05:00'), $this->platform)
        );
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testToDatabaseConvertTimezone()
    {
        $this->assertSame(
            '2000-12-31',
            $this->type->convertToDatabaseValue(Date::fromIsoString('2001-01-01+10:00'), $this->platform)
        );
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testToDatabaseFailureInvalidType()
    {
        $this->expectException('Doctrine\DBAL\Types\ConversionException');
        $this->type->convertToDatabaseValue('value', $this->platform);
    }

    public function testClock()
    {
        $this->liberatedType->clock = null;
        $actual = $this->liberatedType->clock();

        $this->assertEquals(new SystemClock(), $actual);
        $this->assertSame($actual, $this->liberatedType->clock());
    }
}
