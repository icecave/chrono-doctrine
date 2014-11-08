<?php
namespace Icecave\Chrono\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateType as BaseDateType;
use Icecave\Chrono\Clock\ClockInterface;
use Icecave\Chrono\Clock\SystemClock;
use Icecave\Chrono\Date;
use Icecave\Chrono\TimePointInterface;

/**
 * A type for utilizing Chrono dates.
 */
class DateType extends BaseDateType
{
    /**
     * Get the name.
     *
     * @return string The name.
     */
    public function getName()
    {
        return 'chrono_date';
    }

    /**
     * If this Doctrine Type maps to an already mapped database type,
     * reverse schema engineering can't take them apart. You need to mark
     * one of those types as commented, which will have Doctrine use an SQL
     * comment to typehint the actual Doctrine Type.
     *
     * @param AbstractPlatform $platform
     *
     * @return boolean
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * Convert the supplied database value to its PHP equivalent.
     *
     * @param mixed            $value    The database value.
     * @param AbstractPlatform $platform The platform to use.
     *
     * @return mixed               The converted value.
     * @throws ConversionException If conversion fails.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        return Date::fromIsoString(
            $value . $this->clock()->timeZone()->isoString()
        );
    }

    /**
     * Convert the supplied PHP value to its database equivalent.
     *
     * @param mixed            $value    The PHP value.
     * @param AbstractPlatform $platform The platform to use.
     *
     * @return mixed               The converted value.
     * @throws ConversionException If conversion fails.
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        } elseif (!$value instanceof TimePointInterface) {
            throw ConversionException::conversionFailed(
                $value,
                $this->getName()
            );
        }

        $value = $value->toTimeZone(
            $this->clock()->timeZone()
        );

        return $value->format('Y-m-d');
    }

    /**
     * Get the clock.
     *
     * @return ClockInterface The clock.
     */
    protected function clock()
    {
        if (null === $this->clock) {
            $this->clock = new SystemClock();
        }

        return $this->clock;
    }

    private $clock;
}
