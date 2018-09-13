<?php

namespace Page;

use App\Assets;
use App\Router;
use App\DB;
use App\App;

class Controller extends \App\BaseController {

	public function __onControllerLoad() : void {

		Assets::add('css', [ 'admin' ]);
		Assets::add('js', [ 'admin' ]);

	}

	public function __onPrevent(string $url, string $method) : bool {

		if($method != 'login' && empty($_SESSION['admin'])) {
			Router::redirect('admin@login');
			return false;
		} else {
			return true;
		}

	}

	public function login() {

		return [];

	}

	public function logout() {

		unset($_SESSION['admin']);

		Router::redirect('admin@login');

		return false;

	}

	public function settings() {

		return [
			'defaultSettings' => App::$settings
		];

	}

	public function content() {

		return [
			'fond' => (new \Things\Content)->selectEncoded('*', DB::order('id'))
		];

	}

	public function contentEdit($id) {

		return [
			'defaultContent' => (new \Things\Content)->selectOnce('*', 'WHERE id = ?', [ $id ])
		];

	}

#?method-generate

}