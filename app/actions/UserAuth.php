<?php

namespace Action;

use App\App;
use App\Action;

class Controller extends \App\BaseController {

	public function login() : array {
		return [
			'scheme' => [
				'username' => [ 
					'type' => 'input',
					'desc' => 'Имя пользователя',
					'validate' => [
						[ '^.{3,32}$', 'Длина этого поля должна быть от 3 до 32 символов' ]
					]
				],
				'password' => [ 
					'type' => 'input',
					'subtype' => 'password',
					'desc' => 'Пароль',
					'validate' => [
						[ '^.{3,32}$', 'Длина этого поля должна быть от 3 до 32 символов' ]
					]
				],
				'_submit' => [ 
					'type' => 'button',
					'value' => 'Войти'
				]
			],
			'callback' => function(array $data) {

				$user = new \Things\User;

				if($id = $user->exists($data['username'], $data['password'])) {
					$user->login($id);
					return Action::result(true, 'Успешная авторизация');
				} else {
					return Action::result(false, 'Неверный пароль');
				}

			}
		];
	}

	public function register() : array {
		return [
			'scheme' => [
				'username' => [ 
					'type' => 'input',
					'desc' => 'Имя пользователя',
					'validate' => [
						[ '^.{3,32}$', 'Длина этого поля должна быть от 3 до 32 символов' ]
					]
				],
				'password' => [ 
					'type' => 'input',
					'subtype' => 'password',
					'desc' => 'Пароль',
					'validate' => [
						[ '^.{3,32}$', 'Длина этого поля должна быть от 3 до 32 символов' ]
					]
				],
				'_submit' => [ 
					'type' => 'button',
					'value' => 'Зарегистрироваться'
				]
			],
			'callback' => function(array $data) {

				$user = new \Things\User;

				if(is_null($user->exists($data['username']))) {
					$id = $user->register($data['username'], $data['password']);
					$user->login($id);
					return Action::result(true, 'Аккаунт успешно зарегистрирован');
				} else {
					return Action::result(false, 'Пользователь с таким именем уже существует');
				}

			}
		];
	}

#?method-generate

}