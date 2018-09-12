<?php

namespace App;

/**
 * netBird/CSRF
 *
 * Модуль защиты от межсайтовой подделки запросов
 * 
 * @package netBird
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */
class CSRF {

	/**
	 * Первоначальная генерация токена CSRF
	 * 
	 * @return void
	 */
	public static function start() : void {

		if(!isset($_SESSION['csrf_token'])) {
			$_SESSION['csrf_token'] = base64_encode(
				openssl_random_pseudo_bytes(32)
			);
		}

	}

	/**
	 * Проверка на совпадение токена CSRF
	 * 
	 * @param array $post - массив для поиска токена CSRF
	 * @return bool
	 */
	public static function safely(array $post = null) : bool {

		$post = $post ?? $_POST;

		return (isset($post['_csrf_token']) && $post['_csrf_token'] === $_SESSION['csrf_token']);

	}

	/**
	 * Получение токена CSRF
	 * 
	 * @return string
	 */
	public static function get() : string {

		return $_SESSION['csrf_token'];

	}

	/**
	 * Интеграция модуля в шаблонизатор
	 * 
	 * @return void
	 */
	public static function integrateToTemplate() : void {

		App::$template->addFunction(
			new \Twig_SimpleFunction('csrf_token', function() : string {
				return CSRF::get();
			})
		);

	}
	
}