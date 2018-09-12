<?php

namespace Page;

use App\Assets;

class Controller extends \App\BaseController {

	public function __onControllerLoad() : void {

		Assets::add('css', [ 'main' ]);

	}

	public function __onPrevent(string $url, string $method) : bool {
		return true;
	}

	public function index() {

		return [];
		
	}

#?method-generate

}