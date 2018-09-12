<?php

use App\Action;
use App\App;
use App\Explorer;
use App\CSRF;
use App\Debug;
use App\DB;

//
require($_SERVER['DOCUMENT_ROOT'] . '/application/vendor/autoload.php');

// Загрузка конфигурации приложения
Explorer::configure();

/**
 * Запуск приложения в обычном режиме
 * Приложение открыто через ./index.php
 */

if($_SERVER['REQUEST_METHOD'] !== 'GET' || !defined('__MODE_MAIN__')) {
	return header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
}

// Указание временной зоны
date_default_timezone_set(App::$config['timezone']);

// Указание кодировки приложения
header('Content-type: text/html; charset=utf-8');

// Запуск сессии
session_set_cookie_params(App::$config['session_time']);
session_start();

// Загрузка шаблонизатора
App::$template = new \Twig_Environment(
	new \Twig_Loader_Filesystem(Explorer::path('views')), [
		'debug'	=> App::$config['developed'],
		'cache'	=> Explorer::path('views_cache'),
		'auto_reload' => true
	]
);

// Регистрация отладчика
Debug::handle();

// Загрузка модуля защиты от межсайтовой подделки запросов
CSRF::start();
CSRF::integrateToTemplate();

// Загрузка модуля для работы с ajax и html-формами
Action::integrateToTemplate();

// Загрузка модуля для работы с базой данных
App::$DB = new DB;
App::$DB->connect(App::$config['database']);

// Добавление в локальное хранилище пользовательских настроек
App::$settings = (new Things\Settings)->parse();