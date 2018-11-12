<?php # example

use App\{Action,Explorer};

require($_SERVER['DOCUMENT_ROOT'] . '/app/run-post.php');

Action::exists([ 'maxsize' ]);
Action::exists([ 'file' ], $_FILES);

if($_FILES['file']['size'] > $_POST['maxsize'] * 1000000) {
	return Action::result(false, 'Файл не может весить больше ' . $_POST['maxsize'] . ' мб.');
}

if(!$file = Explorer::upload('file')) {
	return Action::result(false, 'Неизвестный сбой при загрузке изображения');
}

Action::result(true, [
	'file' => $file,
	'name' => $_FILES['file']['name']
]);