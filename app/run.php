<?php

/**
 * Запуск приложения в обычном режиме
 * Приложение открыто через ./index.php
 */

if($_SERVER['REQUEST_METHOD'] !== 'GET' || !defined('__MODE_MAIN__')) {
	exit;
}

use App\App;
use App\Debug;

require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

// Загрузка конфигурации приложения
App::$config = \App\Explorer::configure();

// Указание временной зоны
date_default_timezone_set(App::$config['timezone']);

// Указание кодировки приложения
header('Content-type: text/html; charset=utf-8');

// Запуск сессии
session_set_cookie_params(App::$config['session_time']);
session_start();

// Загрузка шаблонизатора
App::$template = new \Twig_Environment(
	new \Twig_Loader_Filesystem(\App\Explorer::path('views')), [
		'debug'	=> App::$config['developed'],
		'cache'	=> \App\Explorer::path('views_cache'),
		'auto_reload' => true
	]
);

// Регистрация отладчика
Debug::handle();
Debug::integrateToTemplate();

// Загрузка модуля для работы с базой данных
App::$DB = new \App\DB;
App::$DB->connect(App::$config['database']);

// Добавление в локальное хранилище пользовательских настроек
App::$settings = (new Things\Settings)->parse();

App::$template->addGlobal('_settings', App::$settings);
App::$template->addGlobal('_config', App::$config);

// Загрузка модуля защиты от межсайтовой подделки запросов
\App\CSRF::start();
\App\CSRF::integrateToTemplate();

// Загрузка модуля для работы с ajax и html-формами
\App\Action::integrateToTemplate();