<?php

namespace App;

/**
 * netBird/EngineException
 *
 * Локальный тип исключения
 * 
 * @package netBird
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */
class EngineException extends \Exception {

	/**
	 * Информация для отображения ошибки
	 *
	 * @var array
	 */
	public static $data = [];

	/**
	 * Конструктор ошибки
	 *
	 * @param string $message - сообщение
	 * @return Exception
	 */
	public function __construct(string $message) {

		// Получение стека вызовов функций
		$backtraceTemp = debug_backtrace();
		$backtrace = [];
		foreach ($backtraceTemp as $key => $trace) {
			// Убираем из стека место выброса ошибки (throw new EngineException)
			// Тем самым в дальнейшем в ошибке указывается место, в котором была вызвана проблемная функция
			if($key == 0) {
				continue;
			}
			$backtrace[] = $trace;
		}

		self::$data = [
			'message' => $message,
			'backtrace' => $backtrace
		];

	}

}