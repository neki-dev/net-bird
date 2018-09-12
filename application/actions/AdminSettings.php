<?php

namespace Action;

use App\Action;

class Controller extends \App\BaseController {

	public function edit() : array {
		return [
			'scheme' => [

				'sitename' => [ 
					'type' => 'input',
					'comment' => 'Название сайта',
					'saved' => true,
					'validate' => [
						[ '^.{3,64}$', 'Длина этого поля должна быть от 3 до 64 символов' ]
					]
				],
				'email' => [ 
					'type' => 'input',
					'comment' => 'Электронная почта',
					'saved' => true,
					'validate' => [
						[ '^.{5,128}$', 'Длина этого поля должна быть от 5 до 128 символов' ]
					]
				],
				'adminkey' => [ 
					'type' => 'input',
					'comment' => 'Ключ авторизации панели управления',
					'saved' => true,
					'validate' => [
						[ '^.{3,16}$', 'Длина этого поля должна быть от 3 до 16 символов' ]
					]
				],
				'_submit' => [ 
					'type' => 'button',
					'value' => 'Сохранить'
				],

			],
			'callback' => function(array $data) {

				$settings = new \Things\Settings;
				foreach($data as $key => $value) {
					$settings->update([ 'value' => $value ], 'WHERE `key` = ?', [ $key ]);
				}

				return Action::result(true, 'Настройки успешно сохранены', 'admin/settings');

			}
		];
	}

#?method-generate

}