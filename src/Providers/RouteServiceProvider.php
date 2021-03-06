<?php

namespace App\Providers;

use Silex\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * RouteServiceProvider
 *
 * @category  Providers
 * @package   Providers
 * @author    Jesus Farfan <jesu.farfan23@gmail.com>
 * @copyright Jesus Farfan
 * @license   MIT 
 * @link      https://github.com/jesusfar
 */
class RouteServiceProvider implements ServiceProviderInterface
{

    /**
     * boot serviceprovider
     *
     * @param Application $app App object inyected
     *
     * @return void
     */
    public function boot(Application $app)
    {

    }

    /**
     * register serviceprovider
     *
     * @param Application $app App object inyected
     *
     * @return void
     */
    public function register(Container $app)
    {
        $this->loadRoutes($app);
    }

    private function loadRoutes(Container $app)
    {

        $routes = $this->parseRoutes();

        foreach ($routes as $route) {
            $path = $route['path'];
            $method = strtolower($route['method']);
            $controller = explode('@', $route['controller']);
            $classController = $controller[0];
            $actionController = $controller[1];

            if (!array_key_exists($classController, $app)) {
                $app[$classController] = function($app) use ($classController) {
                   return new $classController();
                };
            }

            $app->$method($path, $classController . ':' . $actionController);
        }
    }

    private function parseRoutes()
    {
        $routes = [];

        try {
            $routes = Yaml::parse(file_get_contents(__DIR__ . '/../../app/routes/routes.yml'));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }

        return $routes;
    }
}
