<?php

use App\Action;
use App\Explorer;

require($_SERVER['DOCUMENT_ROOT'] . '/application/app-post.php');

Action::exists([ 'file' ]);

unlink(Explorer::path('uploads', $_POST['file']));

Action::result(true);