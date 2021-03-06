<?php

namespace PlugRoute\Routes;

use PlugRoute\Helpers\PlugHelper;
use PlugRoute\Helpers\ValidateHelper;
use PlugRoute\Callback\Callback;

class DynamicRoute implements IRoute
{
	private $callback;

	private $data;

	public function __construct($name)
	{
		$this->callback = new Callback($name);
	}

	public function execute($route, $urlPath)
    {
        $route = $this->handleRoute($route, $urlPath);

        if (ValidateHelper::isEqual($route['route'], $urlPath)) {
			return $this->callback->handleCallback($route, $this->data);
		}

		return ManagerRoute::$accountUrlNotFound++;
    }

    private function handleRoute($route, $urlPath)
    {
        $match          = PlugHelper::getMatch($route['route']);
        $routeArray     = PlugHelper::returnArrayWithoutEmptyValues($route['route'], '/');
        $urlArray       = PlugHelper::returnArrayWithoutEmptyValues($urlPath, '/');
        $indexes        = PlugHelper::getIndexDynamicOnRoute($routeArray, $match[0]);
        $this->data     = PlugHelper::getValuesDynamics($indexes, $urlArray);
        $route['route'] = $this->mountUrlPath($routeArray, $urlArray, $indexes);
        return $route;
    }

	private function mountUrlPath($route, $url, $index)
	{
		foreach ($index as $v) {
			if (!empty($url[$v])) {
				$route[$v] = $url[$v];
			}
		}
		$route = implode('/', $route);
		return count($url) > 0 ? '/'.$route : $route;
	}
}