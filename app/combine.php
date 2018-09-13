<?php

/**
 * Запуск приложения в режиме сборщика
 * Приложение открыто через ./app/combine.php
 */

use App\App;
use App\Explorer;
use App\Assets;

require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

// Загрузка конфигурации приложения
Explorer::configure();

if(empty($_GET['type']) || empty($_GET['assets']) || !App::$config['combine']['enabled']) {
	\App\Router::error(404, false);
}

if($_GET['type'] != 'css' && $_GET['type'] != 'js') {
	throw new EngineException('Неизвестный тип сборщика (' . $_GET['type'] . ')');
}

$list = explode(',', $_GET['assets']);
$path = Explorer::path('assets', md5($_GET['assets']) . '.' . $_GET['type'] . (App::$config['combine']['gzip'] ? '.gz' : ''));

// Кеширование сборки
if(App::$config['developed'] || !file_exists($path)) {
	$combined = Assets::combine($_GET['type'], $list);
	if(App::$config['combine']['gzip']) {
		$combined = gzencode($combined, App::$config['combine']['level']);
	}
	Explorer::make($path);
	file_put_contents($path, $combined);
}

header('Content-type: ' . ($_GET['type'] == 'js' ? 'application/javascript' : 'text/css') . '; charset=utf-8');
if(App::$config['combine']['gzip']) {
	header('Content-Encoding: gzip');
}
echo file_get_contents($path) . '/* memset: ' . memory_get_usage() .' */';