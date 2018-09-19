<?php

namespace Action;

use App\App;
use App\Action;

class Controller extends \App\BaseController {

	public function login() : array {
		return [
			'scheme' => [
				'adminkey' => [ 
					'type' => 'input',
					'subtype' => 'password',
					'placeholder' => 'Ключ авторизации',
					'class' => [ 'noborder' ],
					'validate' => [
						[ '^.{3,16}$', 'Длина этого поля должна быть от 3 до 16 символов' ]
					]
				],
				'_submit' => [ 
					'type' => 'button',
					'value' => 'Продолжить'
				]
			],
			'callback' => function(array $data) {

				if($data['adminkey'] == App::$settings['adminkey']) {
					$_SESSION['admin'] = true;
					return Action::result(true, null, 'admin');
				} else {
					return Action::result(false, 'Неверный ключ авторизации', 'admin');
				}

			}
		];
	}

#?method-generate

}