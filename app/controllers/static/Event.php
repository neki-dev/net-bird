<?php

use App\App;

class Event {

	public static function __onControllerBeforeLoad(string $url, string $class, string $method) : void {

		App::$template->addGlobal('_class', $class);
		App::$template->addGlobal('_method', $method);
		App::$template->addGlobal('_url', $url);

		App::$template->addGlobal('_session', $_SESSION);

	}

	public static function __onControllerAfterLoad(string $url, string $class, string $method) : void {

	}
	
}