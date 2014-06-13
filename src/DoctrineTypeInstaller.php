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
            'chrono_datetime',
            __NAMESPACE__ . '\DateTimeType'
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
