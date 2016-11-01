<?php
namespace SuiteCRM\Includes\SuiteContainer;

use Interop\Container\ContainerInterface;
use DI\ContainerBuilder;

class SuiteContainer implements ContainerInterface{

    public function get($id)
    {
       return DI\get($id);
    }

    public function has($id)
    {
    }

    public static function getContainer()
    {
        $builder = new DI\ContainerBuilder();

        $definitions = self::getDefinitions();

        foreach ($definitions as $definition) {
            $builder->addDefinitions($definition);
        }

        return $builder->build();
    }

    private function getDefinitions()
    {
        $definitionsArray = (array)glob('include/SuiteContainer/Config/*.php');

        if (is_dir('include/SuiteContainer/Config')) {
            return array_merge(
                $definitionsArray,
                (array)glob('custom/include/SuiteContainer/Config/*.php')
            );
        }
    }
}