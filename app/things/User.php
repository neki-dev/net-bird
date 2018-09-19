<?php

namespace Things;

class User extends \App\Thing {

	protected $table = 'users';

	protected $fields = [
		'id' 		=> [ 'type' => 'ai', 'uniq' => true ],
		'username' 	=> [ 'type' => 'varchar(32)' ],
		'password' 	=> [ 'type' => 'varchar(60)' ],
		'token' 	=> [ 'type' => 'varchar(32)' ]
	];

	public function exists(string $username, string $password = null) : ?int {

		$info = $this->select('id,password', 'WHERE username = ? LIMIT 1', [ $username ]);

		if(count($info) == 1) {
			if(!is_null($password) && !password_verify($password, $info[0]['password'])) {
				return null;
			}
			return $info[0]['id'];
		} else {
			return null;
		}

	}

	public static function logged() : bool {

		return isset($_SESSION['authorized']);

	}

	public function login(int $id) : void {

		$token = md5($id . '+' . md5(uniqid()));

		$this->update([
			'token' => $token
		], 'WHERE id = ?', [ $id ]);

		$_SESSION['authorized'] = $token;

	}

	public function logout() : void {

		if(empty($_SESSION['authorized'])) {
			return;
		}

		$this->update([
			'token' => ''
		], 'WHERE token = ?', [ $_SESSION['authorized'] ]);

		unset($_SESSION['authorized']);

	}

	public function register(string $username, string $password) : int {

		return $this->insert([
			'username' => $username,
			'password' => password_hash($password, PASSWORD_BCRYPT)
		]);

	}

}