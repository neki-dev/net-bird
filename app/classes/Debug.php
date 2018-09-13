<?php

namespace App;

/**
 * netBird/Debug
 *
 * Модуль для отладки программного кода
 * 
 * @package netBird
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */
class Debug {

	/**
	 * Общий список переменных для отображения
	 *
	 * @var array
	 */
	private static $prints = [];

	/**
	 * Добавление переменной в общий список
	 * 
	 * @param mixed $var - переменная
	 * @return void
	 */
	public static function print($var) : void {

		self::$prints[] = self::parse($var);

	}

	/**
	 * Регистрация обработчиков ошибок
	 * 
	 * @return void
	 */
	public static function handle() : void {

		set_error_handler('App\Debug::handleCallback');
		register_shutdown_function('App\Debug::handleCallback');
		
		error_reporting(0);

	}

	/**
	 * Коллбэк обработчиков ошибок
	 * 
	 * @param int $errno - номер ошибки
	 * @param string $message - сообщение
	 * @param string $file - файл
	 * @param int $line - номер строки
	 * @return void
	 */
	public static function handleCallback(int $errno = null, string $message = null, string $file = null, int $line = null) : void {

		try {

			if(is_null($errno)) {
				$error = error_get_last();
				if(is_array($error)) {
					$errno = $error['type'];
					$message = $error['message'];
					$file = $error['file'] ?? '';
					$line = $error['line'] ?? '';
				} else {
					self::environmentReady();
					return;
				}
			}

			$message = EngineException::$data['message'] ?? $message;
			$backtrace = EngineException::$data['backtrace'] ?? debug_backtrace();
			$file = $backtrace[0]['file'] ?? $file;
			$line = $backtrace[0]['line'] ?? $line;

			// Формирование трассировки
			$backtraceDump = array_reverse($backtrace);
			foreach($backtraceDump as $key => $value) {
				foreach ([ 'type', 'args', 'object' ] as $param) {
					unset($backtraceDump[$key][$param]);
				}
			}

			// Получение именованного типа ошибки
			$consts = get_defined_constants(true)['Core'];
			foreach($consts as $key => $value) {
				if($value == $errno && stripos($key, 'E_') !== false) {
					$errno = $key;
					break;
				}
			}

			self::log('error', md5($file . '+' . $line . '+' . $errno), $message, $file, $line);

			Assets::add('css', [ 'debug', 'debug.parse', 'highlight' ], 'system/');
			Assets::add('js', [ 'highlight' ], 'system/');
			Assets::register();
			
			if(ob_get_length()) {
				ob_end_clean();
			}

			// Получаение участка кода, в котором произошла ошибка
			// Берется 10 строк до и после строки с ошибкой
			$code = file_get_contents($file);
			$codeLinesAll = explode(PHP_EOL, $code);
			$codeLinesFrm = [];
			$lineStart = -1;
			for($i = $line - 11; $i < $line + 10; $i++) {
				if(isset($codeLinesAll[$i])) {
					if($lineStart == -1) {
						$lineStart = $i + 1;
					}
					$codeLinesFrm[] = $codeLinesAll[$i];
				}
			}
			$lineEnd = $lineStart + count($codeLinesFrm) - 1;
			$code = implode(PHP_EOL, $codeLinesFrm);

			// Отображение информации об ошибке
			echo App::$template->render('components/error.tpl', [
				'message' => $message,
				'file' => str_replace(DIRECTORY_SEPARATOR, ' / ', $file),
				'line' => [
					'start' => $lineStart,
					'error' => $line,
					'end' => $lineEnd
				],
				'errno' => $errno,
				'presets' => [
					'get' => self::parse($_GET),
					'session' => self::parse($_SESSION ?? []),
					'post' => self::parse($_POST),
					'args' => self::parse($backtrace[0]['args']),
					'backtrace' => self::parse($backtraceDump)
				],
				'code' => $code,
				'developed' => App::$config['developed']
			]);

			exit;
			
		} catch(Exception $e) {}

	}

	/**
	 * Запись информации в логи
	 * 
	 * @param string $type - тип лога
	 * @param string $key - идентификатор
	 * @param string $message - собщение
	 * @param string $file - файл
	 * @param int $line - номер строки
	 * @return void
	 */
	public static function log(string $type, ?string $key, string $message, string $file, ?int $line = null) : void {

		if(is_null($key)) {
			$key = 'n/a';
		}

		$path = Explorer::path('debug', $type . '_' . date('d-m-Y', $_SERVER['REQUEST_TIME']));
		Explorer::make($path);
		$temp = fopen($path, 'a+');
		fwrite($temp, 
			'[' . $type . '=' . $key . ']' . PHP_EOL .
			'   > ' . $file . (is_int($line) ? ' :: ' . $line : '') . PHP_EOL .
			'   > ' . $message . PHP_EOL .
			'   > ' . date('d.m.Y H:i', $_SERVER['REQUEST_TIME']) . PHP_EOL .
			'[/' . $type . ']' . PHP_EOL
		);
		fclose($temp);

	}

	/**
	 * Рендеринг переменной
	 * 
	 * @param int $var - переменная
	 * @return string
	 */
	private static function parse($var) : string {

		$str = '';

		if(is_null($var)) {

			$str = 'NULL';

		} else if($var == "") {

			$str = 'EMPTY';

		} else if(is_array($var)) {

			if(count($var) == 0) {
				$str = 'Empty';
			} else {
				$str = '<span class="sd">[</span><div class="array">';
				foreach ($var as $key => $value) {
					$str .= '<div class="key"><span class="sk">' . $key . '</span> <span class="sd">&rArr;</span> <span class="sv">' . self::parse($value) . '</span></div>';
				}
				$str .= '</div><span class="sd">]</span>';
			}

		} else if(is_resource($var)) {

			$var_array = [];
			while($arr = mysql_fetch_array($var)) {
				$var_array[] = $arr;
			}
			$str = self::parse($var_array);

		} else if(is_object($var)) {

			$str = self::parse(
				get_object_vars($var)
			);

		} else if(is_bool($var)) {

			$str = ($var ? 'TRUE' : 'FALSE');

		} else {

			$str = preg_replace('/\n/', '<br/>\n', $var);

		}

		return '<span class="debug-parse-item">' . gettype($var) . ' &middot; </span> ' . $str;

	}

	/**
	 * Отображение подготовленных переменных
	 * 
	 * @return void
	 */
	private static function environmentReady() : void {

		if(count(self::$prints) == 0) {
			return;
		}

		echo PHP_EOL . App::$template->render('components/debug.tpl', [
			'messages' => self::$prints
		]);

		self::$prints = [];

	}

}