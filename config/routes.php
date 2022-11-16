<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

Router::plugin(
    'Enforcer',
    ['path' => '/enforcer'],
    function (RouteBuilder $routes) {
        $routes->scope(
            '/admin', ['_namePrefix' => 'admin:'], function ($routes) {
                // This route's name will be `enforcer:admin:index`
                $routes->connect('/:action/*', ['controller' => 'EnforcerGroupPermissions'], ['_name' => 'enforcer']);
                $routes->connect('/groups/:action/*', ['controller' => 'EnforcerGroups']);
            }
        );

        $routes->fallbacks(DashedRoute::class);
    }
);
