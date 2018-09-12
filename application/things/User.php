<?php

namespace Things;

class User extends \App\Thing {

	protected $table = 'users';

	protected $fields = [
		'id' 		=> [ 'type' => "ai", 'uniq' => true ],
		'username' 	=> [ 'type' => "varchar(32)" ],
		'password' 	=> [ 'type' => "varchar(32)" ],
	];

	public function exists(string $username, string $password) : int {

		$info = $this->select('id', 'WHERE username = ? AND password = ? LIMIT 1', [ 
			$username, 
			$password 
		]);

		return (count($info) == 0 ? null : $info[0]['id']);

	}

	public static function logged() : bool {

		return isset($_SESSION['authorized']);

	}

	public static function login(int $id) : void {

		$_SESSION['authorized'] = $id;

	}

	public static function logout() : void {

		unset($_SESSION['authorized']);

	}

}