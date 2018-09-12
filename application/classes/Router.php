<?php

namespace App;

/**
 * netBird/Router
 *
 * Маршрутизатор
 * 
 * @package netBird
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */
class Router {

	/**
	 * Cписок страниц сайта
	 *
	 * @var array
	 */
	private static $map = [];

	/**
	 * Запуск маршрутизатора с указанием страниц сайта
	 * 
	 * @param array $map - список страниц
	 * @return bool
	 */
	public static function run(array $map) : bool {

		self::$map = array_change_key_case(array_flip($map));

		$params = array_keys($_GET);
		if(isset($params[0]) && $params[0] != 'route') {
			return self::free();
		}

		$_GET['route'] = '/' . ($_GET['route'] ?? '');

		$controller = null;
		$matches = [
			'args' => [],
			'values' => []
		];

		foreach($map as $url => $c) {

			// Автоподстановка слэша адреса страницы
			if($url[0] != '/') {
				unset($map[$url]);
				$url = '/' . $url;
				$map[$url] = $c;
			}

			preg_match('/{([a-zA-Z]+)}/', $url, $matches['args']);
			if(count($matches['args']) == 0) {
				// Статический адрес страницы
				if($url == $_GET['route']) {
					$controller = $c;
					break;
				}
			} else {
				// Адрес страницы содержит параметры
				unset($matches['args'][0]);
				preg_match(
					'/^' . preg_replace('/{[a-zA-Z]+}/', '([^\/]+)', str_replace('/', '\/', $url)) . '$/', 
					$_GET['route'], 
					$matches['values']
				);
				if(count($matches['values']) > 1) {
					unset($matches['values'][0]);
					$controller = $c;
					break;
				}
			}

		}

		if(is_null($controller)) {
			return self::free();
		}

		// Генерация контроллера страницы
		Generate::parsePageController($controller, $matches['args']);

		App::getController($controller, ($class), ($method));

		// Загрузка класса контроллера
		$path = Explorer::path('controller', $class);
		if(!file_exists($path)) {
			throw new EngineException('Неизвестный контроллер страницы');
		}
		require($path);
		$controller = new \Page\Controller;

		if(method_exists($controller, '__onControllerLoad')) {
			call_user_func_array([ $controller, '__onControllerLoad' ], []);
		}

		require(Explorer::path('controller_static', 'Event'));

		\Event::__onControllerBeforeLoad($_GET['route'], $class, $method);
		$stopped = false;
		if(method_exists($controller, '__onPrevent')) {
			if(!call_user_func_array([ $controller, '__onPrevent' ], [ $_GET['route'], $method ])) {
				$stopped = true;
			}
		}
		if(!$stopped) {
			if(!method_exists($controller, $method)) {
				throw new EngineException('Неизвестный метод контроллера страницы');
			}
			Assets::register();
			$result = call_user_func_array([ $controller, $method ], $matches['values']);
			if($result !== false) {
				echo App::$template->render($class . '-' . $method . '.tpl', (gettype($result) == 'array' ? $result : []));
			}
		}
		\Event::__onControllerAfterLoad($_GET['route'], $class, $method);
		
		return true;

	}

	/**
	 * Перенаправление на другую страницу
	 * 
	 * @param string $location - адрес или контроллер страницы
	 * @return void
	 */
	public static function redirect(string $location) : void {

		header('Location: ' . (self::$map[strtolower($location)] ?? $location));
		exit;

	}

	/**
	 * Вывод ошибки 404
	 * 
	 * @return bool
	 */
	private static function free() : bool {

		header($header = ($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found'));

		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			die($header);
		} else {
			App::$template->addGlobal('_settings', App::$settings);
			die(App::$template->render('components/http-errors/404.tpl'));
		}

		return false;

	}

}