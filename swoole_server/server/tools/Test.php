<?php
namespace server\tools;

use Medoo\Medoo;

class Test
{
    public $components = [];

    public function __construct(array $modules)
    {
        $factory = new Factory();

        foreach ($modules as $moduleName => $moduleOptions) {
            $this->components[] = $factory->makeModule($moduleName,$moduleOptions); // new $moduleName();
        }
    }

    public function __get($property)
    {
        if (!property_exists($this, $property)) {
            return $this->components[$property] ? : '';
        }

    }

    public function getDb()
    {
        return $this->components['db'];
    }
}