<?php
namespace Icecave\Chrono\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;
use Icecave\Chrono\TimeSpan\Period;

/**
 * A type for utilizing Chrono periods.
 */
class PeriodType extends StringType
{
    /**
     * Get the name.
     *
     * @return string The name.
     */
    public function getName()
    {
        return 'chrono_period';
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

        return Period::fromIsoString($value);
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
        } elseif (!$value instanceof Period) {
            throw ConversionException::conversionFailed(
                $value,
                $this->getName()
            );
        }

        return $value->isoString();
    }
}
