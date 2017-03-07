<?php
namespace Icecave\Chrono\Doctrine;

use Doctrine\DBAL\Types\Type;

/**
 * Installs custom Doctrine types.
 */
class DoctrineTypeInstaller
{
    /**
     * Install all custom Doctrine types.
     */
    public function installTypes()
    {
        $this->installType(
            'chrono_date',
            __NAMESPACE__ . '\DateType'
        );

        $this->installType(
            'chrono_time',
            __NAMESPACE__ . '\TimeType'
        );

        $this->installType(
            'chrono_datetime',
            __NAMESPACE__ . '\DateTimeType'
        );

        $this->installType(
            'chrono_timezone',
            __NAMESPACE__ . '\TimeZoneType'
        );

        $this->installType(
            'chrono_period',
            __NAMESPACE__ . '\PeriodType'
        );

        $this->installType(
            'chrono_duration',
            __NAMESPACE__ . '\DurationType'
        );
    }

    /**
     * Install a custom Doctrine type.
     *
     * @param string $name      The type name.
     * @param string $className The class name.
     */
    protected function installType($name, $className)
    {
        if (!Type::hasType($name)) {
            Type::addType($name, $className);
        }
    }
}
