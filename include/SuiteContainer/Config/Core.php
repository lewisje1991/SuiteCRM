<?php

use Interop\Container\ContainerInterface;

return [
    DBManager::class => function () {
        return $GLOBALS['db'];
    },
    LewisTestClass::class => function (ContainerInterface $container) {
        return new LewisTestClass(
            $container->get(DBManager::class),
            $container->get('config')
        );
    },
    'config' => function () {
        global $sugar_config;
        return $sugar_config;
    }

];