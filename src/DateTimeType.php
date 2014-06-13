<?php
namespace Icecave\Chrono\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType as BaseDateTimeType;
use Icecave\Chrono\Clock\ClockInterface;
use Icecave\Chrono\Clock\SystemClock;
use Icecave\Chrono\DateTime;
use Icecave\Chrono\TimePointInterface;

/**
 * A date type for utilizing Chrono dates.
 */
class DateTimeType extends BaseDateTimeType
{
    /**
     * Get the name.
     *
     * @return string The name.
     */
    public function getName()
    {
        return 'chrono-datetime';
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

        return DateTime::fromIsoString(
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
        }
        if (
            !$value instanceof TimePointInterface ||
            $this->clock()->timeZone()->isNotEqualTo($value->timeZone())
        ) {
            throw ConversionException::conversionFailed(
                $value,
                $this->getName()
            );
        }

        return $value->format('Y-m-d H:i:s');
    }

    /**
     * Get the clock.
     *
     * @return ClockInterface The clock.
     */
    protected function clock()
    {
        if (null === $this->clock) {
            $this->clock = new SystemClock;
        }

        return $this->clock;
    }

    private $clock;
}
