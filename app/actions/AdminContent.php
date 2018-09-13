<?php

namespace Action;

use App\Action;

class Controller extends \App\BaseController {

	public function add() : array {
		return [
			'scheme' => [

				'title' => [ 
					'type' => 'input',
					'desc' => 'Название',
					'saved' => true,
					'validate' => [
						[ '^.{3,128}$', 'Длина этого поля должна быть от 3 до 128 символов' ]
					]
				],
				'photo' => [ 
					'type' => 'file',
					'desc' => 'Фотография'
				],
				'content' => [ 
					'type' => 'textarea',
					'desc' => 'Содержимое',
					'saved' => true,
					'validate' => [
						[ '^.{3,4096}$', 'Длина этого поля должна быть от 3 до 4096 символов' ]
					]
				],
				'_submit' => [ 
					'type' => 'button',
					'value' => 'Добавить'
				],

			],
			'callback' => function(array $data) {

				$data['postdate'] = time();

				$content = new \Things\Content;
				$content->insert($data);

				return Action::result(true, 'Контент успешно добавлен', 'admin/content');

			}
		];
	}

	public function edit() : array {
		return [
			'scheme' => [

				'title' => [ 
					'type' => 'input',
					'desc' => 'Название',
					'saved' => true,
					'validate' => [
						[ '^.{3,128}$', 'Длина этого поля должна быть от 3 до 128 символов' ]
					]
				],
				'content' => [ 
					'type' => 'textarea',
					'desc' => 'Содержимое',
					'saved' => true,
					'validate' => [
						[ '^.{3,4096}$', 'Длина этого поля должна быть от 3 до 4096 символов' ]
					]
				],
				'id' => [ 
					'type' => 'hidden'
				],
				'_submit' => [ 
					'type' => 'button',
					'value' => 'Сохранить'
				],
				'_back' => [ 
					'type' => 'button',
					'value' => 'Назад',
					'class' => [ 'outline' ],
					'onclick' => 'window.history.back();return false;'
				],

			],
			'callback' => function(array $data) {

				$data['postdate'] = time();

				$content = new \Things\Content;
				$content->update($data, 'WHERE id = ?', [ $data['id'] ]);
				
				return Action::result(true, 'Контент успешно сохранен', 'admin/content/' . $data['id']);

			}, 
			'params' => [
				'ajax' => true
			]
		];
	}

#?method-generate

}