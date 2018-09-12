<?php

use App\Action;
use App\App;
use App\Explorer;
use App\CSRF;
use App\DB;

//
require($_SERVER['DOCUMENT_ROOT'] . '/application/vendor/autoload.php');

// Загрузка конфигурации приложения
Explorer::configure();

/**
 * Запуск приложения в режиме POST запроса
 * Приложение открыто через ./application/app-post.php
 */

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
	return header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
}

// Указание временной зоны
date_default_timezone_set(App::$config['timezone']);

// Запуск сессии
session_set_cookie_params(App::$config['session_time']);
session_start();

// Загрузка модуля для работы с базой данных
App::$DB = new DB;
App::$DB->connect(App::$config['database']);

// Добавление в локальное хранилище пользовательских настроек
App::$settings = (new Things\Settings)->parse();

if(isset($_POST['_action'])) {

	$_SERVER['REQUEST_AJAX'] = isset($_POST['_ajax']);
	
	// Обработка html-формы
	Action::do($_POST['_action'], $_POST);

} else {

	$_SERVER['REQUEST_AJAX'] = true;

	// Защита от межсайтовой подделки запросов
	if(!CSRF::safely()) {
		Action::result(false, 'Недопустимый токен CSRF');
	}

}