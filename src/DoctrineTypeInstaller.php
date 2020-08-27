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
        $this->installType('chrono_date', DateType::class);
        $this->installType('chrono_time', TimeType::class);
        $this->installType('chrono_datetime', DateTimeType::class);
        $this->installType('chrono_timezone', TimeZoneType::class);
        $this->installType('chrono_period', PeriodType::class);
        $this->installType('chrono_duration', DurationType::class);
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
