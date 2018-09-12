<?php

namespace App;

/**
 * netBird/Assets
 *
 * Модуль для работы с js/css
 * 
 * @package netBird
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */
class Assets {
	
	/**
	 * Общий список с названиями файлов
	 *
	 * @var array
	 */
	private static $items = [
		'css' => [ 'system/normalize', 'system/interface' ],
		'js' => [ 'system/jquery.min', 'system/interface' ]
	];

	/**
	 * Сжатие и сборка нескольких файлов в один
	 * 
	 * @param string $type - тип файлов
	 * @param array $list - список названий файлов
	 * @return string
	 */
	public static function combine(string $type, array $list) : string {

		if(empty(self::$items[$type])) {
			throw new EngineException('Неизвестный тип сборщика (' . $type . ')');
		}

		$compress = '';
		foreach ($list as $file) {
			$path = Explorer::path($type, $file);
			$method = 'minify' . $type;
			$compress .= PHP_EOL . '/* ' . Explorer::info($path, 'filename') . ' */' . PHP_EOL . PHP_EOL . self::$method(file_get_contents($path)) . PHP_EOL;
		}

		return $compress;

	}

	/**
	 * Добавление файлов в общий список
	 * 
	 * @param string $type - тип файлов
	 * @param array $list - список названий файлов
	 * @param string $env - приставка к названиям файлов
	 * @return void
	 */
	public static function add(string $type, array $list, string $env = '') : void {

		self::$items[$type] = array_merge(self::$items[$type], preg_filter('/^/', $env, $list));

	}

	/**
	 * Установка файлов в общий список
	 * 
	 * @param string $type - тип файлов
	 * @param array $list - список названий файлов
	 * @param string $env - приставка к названиям файлов
	 * @return void
	 */
	public static function set(string $type, array $list, string $env = '') : void {

		self::$items[$type] = preg_filter('/^/', $env, $list);

	}

	/**
	 * Добавление общего списка в шаблонизатор
	 * 
	 * @return void
	 */
	public static function register() : void {

		if(method_exists(App::$template, 'addGlobal')) {
			App::$template->addGlobal('_assets', self::$items);
		}

	}

	/**
	 * Сжатие CSS
	 * 
	 * @param string $css - css код
	 * @return string
	 */
	public static function minifyCSS(string $css) : string {

		return preg_replace([
			'/\/\*.*?\*\//si',
			'/\s+/',
			'/^\s+/',
			'/\s*([{:,;}])\s*/',
			'/}/',
			'/;}/',
			'/\s+$/',
		], [
			'',
			' ',
			'',
			'$1',
			'}' . PHP_EOL,
			'}',
			'',
		], $css);

	}

	/**
	 * Сжатие JS
	 * 
	 * @param string $js - js код
	 * @return string
	 */
	public static function minifyJS(string $js) : string {

		return \JShrink\Minifier::minify($js, [ 
			'flaggedComments' => false 
		]);

	}

}