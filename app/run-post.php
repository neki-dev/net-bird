<?php

/**
 * Запуск приложения в режиме POST запроса
 * Приложение открыто через ./app/app-post.php
 */

if($_SERVER['REQUEST_METHOD'] !== 'POST') {	
	exit;
}

use App\App;

require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

// Загрузка конфигурации приложения
App::$config = \App\Explorer::configure();

// Указание временной зоны
date_default_timezone_set(App::$config['timezone']);

// Запуск сессии
session_set_cookie_params(App::$config['session_time']);
session_start();

// Загрузка модуля для работы с базой данных
App::$DB = new \App\DB;
App::$DB->connect(App::$config['database']);

// Добавление в локальное хранилище пользовательских настроек
App::$settings = (new Things\Settings)->parse();

if(isset($_POST['_action'])) {

	$_SERVER['REQUEST_AJAX'] = isset($_POST['_ajax']);
	
	// Обработка html-формы
	\App\Action::do($_POST['_action'], $_POST);

} else {

	$_SERVER['REQUEST_AJAX'] = true;

	// Защита от межсайтовой подделки запросов
	if(!\App\CSRF::safely()) {
		\App\Action::result(false, 'Недопустимый токен CSRF');
	}

}