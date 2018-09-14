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
			if(file_exists($path)) {
				$method = 'minify' . $type;
				$compress .= PHP_EOL . '/* ' . Explorer::info($path, 'filename') . ' */' . PHP_EOL . PHP_EOL . self::$method(file_get_contents($path)) . PHP_EOL;
			}
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
	 * Получение файлов из общего списка
	 * 
	 * @param string $type - тип файлов
	 * @return void
	 */
	public static function get(string $type) : array {

		return self::$items[$type];

	}

	/**
	 * Добавление общего списка в шаблонизатор
	 * 
	 * @return void
	 */
	public static function register() : void {

		self::parseFonts();

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
			'/\[\[([a-zA-Z\s-0-9-_]+)\]\]/'
		], [
			'',
			' ',
			'',
			'$1',
			'}' . PHP_EOL,
			'}',
			'',
			'"$1"',
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

	/**
	 * Автоматические подключение шрифтов
	 * 
	 * @return void
	 */
	private static function parseFonts() : void {

		$fonts = [];

		foreach(self::$items['css'] as $file) {
			$matches = [];
			preg_match_all(
				'/\[\[([a-zA-Z\s-0-9-_]+)\]\]/', 
				file_get_contents(Explorer::path('css', $file)), 
				$matches
			);
			unset($matches[0]);
			foreach($matches as $match) {
				if(isset($match[0])) {
					$fonts = array_merge($fonts, $match);
				}
			}
		}

		self::$items['fonts'] = array_unique($fonts);

		foreach(self::$items['fonts'] as $key => $value) {
			self::$items['fonts'][$key] = str_replace(' ', '+', $value);
		}

	}

}