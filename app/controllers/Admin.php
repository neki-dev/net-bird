<?php # example

namespace Page;
use App\{Assets,Router,DataBase,App};

class Controller extends \App\BaseController {

	public function __onPrevent(string $url, string $method) : bool {

		Assets::add('css', [ 'admin' ]);
		Assets::add('js', [ 'admin' ]);

		if($method != 'login' && empty($_SESSION['admin'])) {
			Router::go('admin@login');
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

		Router::go('admin@login');

		return false;

	}

	public function settings() {

		return [
			'defaultSettings' => App::$settings
		];

	}

	public function content() {

		return [
			'content' => json_encode(
				(new \Thing\Content)->select('title,id', DataBase::order('id')), 
				JSON_NUMERIC_CHECK|JSON_UNESCAPED_UNICODE 
			)
		];

	}

	public function contentEdit(int $id) {

		return [
			'defaultContent' => (new \Thing\Content)->exact($id)
		];

	}

#?method-generate

}