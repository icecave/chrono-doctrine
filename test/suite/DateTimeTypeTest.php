<?php
namespace Icecave\Chrono\Doctrine;

use Doctrine\DBAL\Types\Type;
use Eloquent\Liberator\Liberator;
use Icecave\Chrono\Clock\SystemClock;
use Icecave\Chrono\DateTime;
use Icecave\Chrono\TimeZone;
use Phake;
use PHPUnit_Framework_TestCase;

class DateTimeTypeTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $installer = new DoctrineTypeInstaller();
        $installer->installTypes();

        $this->type = Type::getType('chrono_datetime');
        $this->liberatedType = Liberator::liberate($this->type);

        $this->clock = Phake::mock('Icecave\Chrono\Clock\ClockInterface');
        $this->timeZone = TimeZone::fromIsoString('-05:00');
        $this->platform = Phake::mock('Doctrine\DBAL\Platforms\AbstractPlatform');

        $this->liberatedType->clock = $this->clock;
        Phake::when($this->clock)->timeZone()->thenReturn($this->timeZone);
    }

    public function testGetName()
    {
        $this->assertSame('chrono_datetime', $this->type->getName());
    }

    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testToPhp()
    {
        $this->assertEquals(
            DateTime::fromIsoString('2001-01-01T01:01:01-05:00'),
            $this->type->convertToPHPValue('2001-01-01 01:01:01', $this->platform)
        );
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testToDatabase()
    {
        $this->assertSame(
            '2001-01-01 01:01:01',
            $this->type->convertToDatabaseValue(DateTime::fromIsoString('2001-01-01T01:01:01-05:00'), $this->platform)
        );
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testToDatabaseFailureIncorrectTimeZone()
    {
        $this->setExpectedException('Doctrine\DBAL\Types\ConversionException');
        $this->type->convertToDatabaseValue(DateTime::fromIsoString('2001-01-01T01:01:01+00:00'), $this->platform);
    }

    public function testToDatabaseFailureInvalidType()
    {
        $this->setExpectedException('Doctrine\DBAL\Types\ConversionException');
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
