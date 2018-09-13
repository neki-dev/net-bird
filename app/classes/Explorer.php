<?php

namespace App;

/**
 * netBird/Explorer
 *
 * Модуль для работы с директориями и файлами
 * 
 * @package netBird
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */
class Explorer {
	
	/**
	 * Массив публичных директорий
	 *
	 * @var array
	 */
	private const PUBLIC_PATH = [
		'app/actions',
		'app/classes/extra',
		'app/things',
		'app/controllers',
		'template'
	];
	
	/**
	 * Общий массив директорий приложения
	 *
	 * @var array
	 */
	private const PATH = [
		'conf' 				=> '.conf',
		'application' 		=> 'app',
		'assets' 			=> 'app/cache/assets/?',
		'conf_default' 		=> 'app/cache/.conf-default',
		'class' 			=> 'app/classes/?.php',
		'views' 			=> 'template/views',
		'views_cache' 		=> 'app/cache/template',
		'controller' 		=> 'app/controllers/?.php',
		'controller_static' => 'app/controllers/static/?.php',
		'action' 			=> 'app/actions/?.php',
		'action_custom' 	=> 'app/actions/cutsom/__?.php',
		'debug' 			=> 'app/cache/debug/?.log',
		'generic' 			=> 'app/cache/generic/?.tmp',
		'uploads' 			=> 'app/cache/uploads/?',
		'js' 				=> 'template/js/?.js',
		'css' 				=> 'template/css/?.css',
	];

	/**
	 * Получение полного пути директории по ключу
	 * 
	 * @param string $pathKey - Ключ директории
	 * @param string $item - Файл директории
	 * @return string
	 */
	public static function path(string $pathKey, string $item = '?') : string {

		if(empty(self::PATH[$pathKey])) {
			throw new EngineException('Неизвестный путь проводника (' . $pathKey . ')');
		}

		return str_replace('/', DIRECTORY_SEPARATOR, $_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . str_replace('?', $item, self::PATH[$pathKey]);

	}

	/**
	 * Проверка директории от публичный доступ
	 * 
	 * @param string $path - Путь директории
	 * @return bool
	 */
	public static function isPublicPath(string $path) : bool {

		foreach (self::PUBLIC_PATH as $value) {
			$publicPath = str_replace('/', DIRECTORY_SEPARATOR, $_SERVER['DOCUMENT_ROOT'] . '/' . $value);
			$parts = explode(DIRECTORY_SEPARATOR, str_replace('/', DIRECTORY_SEPARATOR, $path));
			for($i = count($parts) - 1; $i >= 0; $i--) {
				if($publicPath == implode(DIRECTORY_SEPARATOR, $parts)) {
					return true;
				}
				unset($parts[$i]);
			}
		}

		return false;

	}

	/**
	 * Копирование папки
	 * 
	 * @param string $pathFrom - Путь откуда копировать
	 * @param string $pathTo - Путь куда копировать
	 * @param bool $onlyPublic - Флаг доступа к копированию публичных путей
	 * @return void
	 */
	public static function copyDir(string $pathFrom, string $pathTo, bool $onlyPublic = false) : array {

		$assets = [];
		
		$it = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($pathFrom, RecursiveDirectoryIterator::SKIP_DOTS), 
			RecursiveIteratorIterator::SELF_FIRST
		);
		foreach($it as $item) {
			$path = $pathTo . '/' . $it->getSubPathName();
			if(!$item->isDir()) {
				if($onlyPublic && !self::isPublicPath(self::info($path, 'dir'))) {
					continue;
				}
				copy($item, $path);
				$assets[] = $path;
			} else if(!file_exists($path)) {
				mkdir($path);
			}
		}

		return $assets;

	}

	/**
	 * Удаление папки
	 * 
	 * @param string $path - Путь директории
	 * @return void
	 */
	public static function removeDir(string $path) : void {
		
		$it = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS), 
			RecursiveIteratorIterator::CHILD_FIRST
		);
		foreach($it as $item) {
			if($item->isDir()){
				rmdir($item->getRealPath());
			} else {
				unlink($item->getRealPath());
			}
		}
		rmdir($path);

	}

	/**
	 * Загрузка файла на сервер
	 * 
	 * @param string $key - Название ключа файла 
	 * @param string $fileName - Сохраняемое название файла
	 * @param string $pathKey - Ключ директории для сохранения файла
	 * @return string
	 */
	public static function upload(string $key, string $fileName = null, string $pathKey = null) : string {

		if(empty($_FILES[$key])) {
			throw new EngineException('Неизвестный ключ загружаемого файла');
		}
		
		$fileName = $fileName ?? md5(uniqid()) . '.' . self::info($_FILES[$key]['name'], 'extension');
		$path = Explorer::path($pathKey ?? 'uploads', $fileName);
		Explorer::make($path);

		if(move_uploaded_file($_FILES[$key]['tmp_name'], $path)) {
			return $fileName;
		} else {
			return null;
		}

	}

	/**
	 * Получение информации о директории
	 * 
	 * @param string $path - Путь директории
	 * @param string $type - Требуемый параметр
	 * @return mixed
	 */
	public static function info(string $path, string $type = null) {

		$info = pathinfo($path);
		$info['extension'] = strtolower($info['extension']);

		return (is_null($type) ? $info : $info[$type]);

	}

	/**
	 * Загрузка конфигурации приложения
	 * 
	 * @return void
	 */
	public static function configure() : void {

		// Проверка на наличие конфигурационного файла
		if(!file_exists(Explorer::path('conf'))) {
			// Создание рабочей копии конфигурационного файла
			// Создание уникального ключа приложения
			$default = file_get_contents(
				Explorer::path('conf_default')
			);
			file_put_contents(
				Explorer::path('conf'), 
				preg_replace("/'application_key'\s*=>\s*''/", "'application_key'	=> '" . md5(uniqid()) . "'", $default)
			);
		}

		// Загрузка конфигурации приложения
		require(Explorer::path('conf'));
		// Добавление в локальное хранилище конфигурации приложения
		App::$config = $_CONF;

	}

	/**
	 * Создание директории
	 * 
	 * @param string $path - Путь директории
	 * @return void
	 */
	public static function make(string $path) : void {

		$dir = self::info($path, 'dirname');
		if(!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}

	}

}