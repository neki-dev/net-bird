<?php

use App\Action;
use App\Explorer;

require($_SERVER['DOCUMENT_ROOT'] . '/application/app-post.php');

Action::exists([ 'setsize' ]);
Action::exists([ 'file' ], $_FILES);

if($_FILES['file']['size'] > $_POST['setsize'] * 1000000) {
	return Action::result(false, 'Файл не может весить больше ' . $_POST['setsize'] . ' мб.');
}

if(!$file = Explorer::upload('file')) {
	return Action::result(false, 'Неизвестный сбой при загрузке изображения');
}

Action::result(true, [
	'file' => $file,
	'name' => $_FILES['file']['name']
]);