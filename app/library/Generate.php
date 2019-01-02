<?php

namespace App;

/**
 * netBird/Generate
 *
 * Автогенератор классов
 * 
 * @package netBird
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */
class Generate {

	/**
	 * Автоматическая гереация класса и метода контроллера страницы
	 * 
	 * @param string $controller - класс и метод контроллера страницы
	 * @param array $args - аргументы метода контроллера
	 * @return void
	 */
	public static function parsePageController(string $controller, array $args = []) : void {

		if(!App::$config['developed']) {
			return;
		}

		App::getController($controller, ($class), ($method));

		$temp = '';

		$file = Explorer::path('controller', $class);
		if(!file_exists($file)) {
			$temp = self::source('controller-class');
			file_put_contents($file, $temp);
		} else {
			$temp = file_get_contents($file);
		}

		if(!strpos($temp, 'public function ' . $method . '(')) {
			$temp = str_replace(
				'#?method-generate', 
				str_replace([ '%method%', '%args%' ], [ $method, count($args) > 0 ? '$' . implode(', $', $args) : '' ], self::source('controller-method')), 
				$temp
			);
			file_put_contents($file, $temp);
		}

		$file = Explorer::path('views') . '/' . strtolower($class . '-' . $method) . '.tpl';
		if(!file_exists($file)) {
			file_put_contents($file, self::source('controller-view'));
		}

	}

	/**
	 * Автоматическая гереация класса и метода контроллера формы
	 * 
	 * @param string $controller - класс и метод контроллера формы
	 * @return void
	 */
	public static function parseActionController(string $controller) : void {

		if(!App::$config['developed']) {
			return;
		}

		App::getController($controller, ($class), ($method));

		$temp = '';

		$file = Explorer::path('action', $class);
		if(!file_exists($file)) {
			$temp = self::source('action-class');
			file_put_contents($file, $temp);
		} else {
			$temp = file_get_contents($file);
		}

		if(!strpos($temp, 'public function ' . $method . '(')) {
			$temp = str_replace(
				'#?method-generate', 
				str_replace('%method%', $method, self::source('action-method')), 
				$temp
			);
			file_put_contents($file, $temp);
		}

	}

	/**
	 * Загрузка шаблона для генерации контроллера
	 * 
	 * @param string $name - Название шаблона генерации
	 * @return string
	 */
	public static function source(string $name) : string {

		return file_get_contents(
			Explorer::path('generic', $name)
		);

	}

}