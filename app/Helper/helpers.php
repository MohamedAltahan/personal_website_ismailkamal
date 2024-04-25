<?php
//set sidebar acive
function setActive(array $routes)
{
    if (is_array($routes)) {
        foreach ($routes as $route) {
            //check the current request if like $route
            if (request()->routeIs($route)) {
                return 'active';
            }
        }
    }
}
