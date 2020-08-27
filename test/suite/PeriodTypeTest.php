<?php
namespace Icecave\Chrono\Doctrine;

use Doctrine\DBAL\Types\Type;
use Icecave\Chrono\TimeSpan\Period;
use Phake;
use PHPUnit\Framework\TestCase;

class PeriodTypeTest extends TestCase
{
    public function setUp(): void
    {
        $installer = new DoctrineTypeInstaller();
        $installer->installTypes();

        $this->type = Type::getType('chrono_period');
        $this->platform = Phake::mock('Doctrine\DBAL\Platforms\AbstractPlatform');
    }

    public function testGetName()
    {
        $this->assertSame('chrono_period', $this->type->getName());
    }

    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testToPhp()
    {
        $this->assertEquals(
            Period::fromIsoString('P1Y2M3DT4H5M6S'),
            $this->type->convertToPHPValue('P1Y2M3DT4H5M6S', $this->platform)
        );
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testToDatabase()
    {
        $this->assertSame(
            'P1Y2M3DT4H5M6S',
            $this->type->convertToDatabaseValue(Period::fromIsoString('P1Y2M3DT4H5M6S'), $this->platform)
        );
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testToDatabaseFailureInvalidType()
    {
        $this->expectException('Doctrine\DBAL\Types\ConversionException');
        $this->type->convertToDatabaseValue('value', $this->platform);
    }
}
