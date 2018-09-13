<?php

use App\Action;
use App\App;

require($_SERVER['DOCUMENT_ROOT'] . '/app/run-post.php');

Action::exists([ 'type', 'id' ]);

if(empty($_SESSION['admin'])) {
	return Action::result(false);
}

App::$DB->delete($_POST['type'], 'WHERE `id` = ?', [ $_POST['id'] ]);

Action::result(true);