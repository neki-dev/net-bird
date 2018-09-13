<?php

/**
 * NetBird Framework
 *
 * @version 1.0.1 release
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */

define('__MODE_MAIN__', true);

require($_SERVER['DOCUMENT_ROOT'] . '/app/run.php');

App\Router::run([

	'/' 					=> 'Index@index',

	'/admin' 				=> 'Admin@settings',
	'/admin/settings' 		=> 'Admin@settings',
	'/admin/login' 			=> 'Admin@login',
	'/admin/logout' 		=> 'Admin@logout',
	'/admin/content' 		=> 'Admin@content',
	'/admin/content/{id}' 	=> 'Admin@contentEdit',

]);