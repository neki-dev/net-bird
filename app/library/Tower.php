<?php

namespace App;

/**
 * netBird/Tower
 *
 * Модуль загрузчика башен
 * 
 * @package netBird
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */
class Tower {

	/**
	 * Загрузка и запуск башен
	 * 
	 * @return void
	 */
	public static function boot() {

		foreach(glob(Explorer::path('tower', '*')) as $tower) {
			// Загрузка
			require($tower);
			// Запуск
			$tower = '\\Tower\\' . Explorer::info($tower, 'filename');
			new $tower(
				// Режим запуска приложения
				!defined('__MODE_MAIN__')
			);
		}

	}

}