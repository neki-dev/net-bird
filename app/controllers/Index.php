<?php # example

namespace Page;
use App\Assets;

class Controller extends \App\BaseController {

	public function __onPrevent(string $url, string $method) : bool {

		Assets::add('css', [ 'main' ]);
		
		return true;

	}

	public function index() {

		return [];
		
	}

#?method-generate

}