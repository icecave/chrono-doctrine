<?php
namespace Icecave\Chrono\Doctrine;

use Doctrine\DBAL\Types\Type;
use Icecave\Chrono\TimeSpan\Duration;
use Phake;
use PHPUnit\Framework\TestCase;

class DurationTypeTest extends TestCase
{
    public function setUp(): void
    {
        $installer = new DoctrineTypeInstaller();
        $installer->installTypes();

        $this->type = Type::getType('chrono_duration');
        $this->platform = Phake::mock('Doctrine\DBAL\Platforms\AbstractPlatform');
    }

    public function testGetName()
    {
        $this->assertSame('chrono_duration', $this->type->getName());
    }

    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testToPhp()
    {
        $this->assertEquals(
            new Duration(100),
            $this->type->convertToPHPValue(100, $this->platform)
        );
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testToDatabase()
    {
        $this->assertSame(
            100,
            $this->type->convertToDatabaseValue(new Duration(100), $this->platform)
        );
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testToDatabaseFailureInvalidType()
    {
        $this->expectException('Doctrine\DBAL\Types\ConversionException');
        $this->type->convertToDatabaseValue('value', $this->platform);
    }
}
