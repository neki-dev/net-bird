<?php

namespace App;

/**
 * netBird/App
 *
 * Локальное хранилище приложения
 * 
 * @package netBird
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */
class App {

	/**
	 * Модель шаблонизатора
	 *
	 * @var Twig_Environment
	 */
	public static $template;

	/**
	 * Модель базы данных
	 *
	 * @var DB
	 */
	public static $DB;

	/**
	 * Массив пользовательских настроек
	 * Хранит значения полученные из базы данных
	 *
	 * @var array
	 */
	public static $settings;

	/**
	 * Массив системных настроек
	 * Хранит значения полученные из конфигурации (.conf)
	 *
	 * @var array
	 */
	public static $config;

	/**
	 * Получение контроллера
	 * 
	 * @param string $controller - класс и метод контроллера
	 * @return void
	 */
	public static function getController(string $controller, &$class, &$method) : void {

		$controller = explode('@', $controller);
		$class = $controller[0];
		$method = $controller[1];

	}

}