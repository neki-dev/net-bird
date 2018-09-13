<?php

/**
 * Запуск приложения в режиме отображения ошибки
 * Приложение открыто через ./app/error.php
 */

use App\App;

require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

// Загрузка конфигурации приложения
\App\Explorer::configure();

// Загрузка шаблонизатора
App::$template = new \Twig_Environment(
	new \Twig_Loader_Filesystem(\App\Explorer::path('views')), [
		'debug'	=> App::$config['developed'],
		'cache'	=> \App\Explorer::path('views_cache'),
		'auto_reload' => true
	]
);

// Регистрация отладчика
\App\Debug::handle();

App::$template->addGlobal('_config', App::$config);

\App\Router::error($_GET['code'] ?? 404);