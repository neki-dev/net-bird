<?php

/**
 * NetBird Framework
 *
 * @version 1.0.0 release
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */

define('__MODE_MAIN__', true);

require($_SERVER['DOCUMENT_ROOT'] . '/application/app.php');

App\Router::run([

	'/' 					=> 'Index@index',

	'/admin' 				=> 'Admin@settings',
	'/admin/settings' 		=> 'Admin@settings',
	'/admin/login' 			=> 'Admin@login',
	'/admin/logout' 		=> 'Admin@logout',
	'/admin/content' 		=> 'Admin@content',
	'/admin/content/{id}' 	=> 'Admin@contentEdit',

]);