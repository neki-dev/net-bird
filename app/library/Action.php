<?php

namespace App;

/**
 * netBird/Action
 * 
 * Модуль для работы с ajax и html-формами
 * 
 * @package netBird
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */
class Action {

	/**
	 * Генерирование и отображение html-формы
	 * 
	 * @param string $actionController - класс и метод контроллера формы
	 * @param array $default - значения по умолчанию
	 * @return string
	 */
	public static function render(string $actionController, array $default = []) : string {

		$parser = '';

		// Загрузка схемы и коллбэка
		$action = self::load($actionController);

		// Отображение прошлого результата выполнения формы
		$messages = [ 'success', 'error' ];
		foreach($messages as $type) {
			if(isset($_SESSION['action'][$type])) {
				$parser .= App::$template->render('components/actions/message.tpl', [
					'type' => $type,
					'text' => $_SESSION['action'][$type]
				]);
				break;
			}
		}

		// Обработка схемы html-формы
		foreach($action['scheme'] as $name => $data) {

			$data['class'] = $data['class'] ?? '';
			$data['subtype'] = $data['subtype'] ?? 'text';
			$data['name'] = $name;

			$tags = ($data['type'] == 'file') ? '' : 'name="' . $name . '"';
			foreach($data as $key => $value) {
				switch($key) {
					case 'type':
					case 'validate':
					case 'value':
					case 'title':
						continue 2;
						break;
					case 'class':
						if(is_array($value)) {
							$value = implode(' ', $value);
						} else {
							$value = str_replace(',', ' ', $value);
						}
						if(isset($_SESSION['action']) && isset($_SESSION['action']['element']) && $_SESSION['action']['element'] == $name) {
							$value .= 'error';
						}
						break;
					case 'subtype':
						$key = 'type';
						break;
						break;
				}
				$value = trim($value);
				if(strlen($value) > 0) {
					$tags .= ' ' . $key . '="' . $value .'"';
				}
			}

			$value = ($_SESSION['action']['saved'][$name] ?? ($default[$name] ?? ($data['value'] ?? '')));

			if(isset($data['title'])) {
				$parser .= App::$template->render('components/actions/title.tpl', [
					'text' => $data['title']
				]) . PHP_EOL;
			}

			$parser .= App::$template->render('components/actions/' . $data['type'] . '.tpl', [
				'tags' => $tags,
				'value' => $value,
				'data' => $data,
			]) . PHP_EOL;
	
		}

		if(isset($action['params']['ajax']) && $action['params']['ajax']) {
			$parser = "<input type='hidden' name='_ajax' value='1' />" . $parser;
		}

		unset($_SESSION['action']);

		// Отображение html-формы
		return '<form ' . 
			(isset($action['params']['autocomplete']) && !$action['params']['autocomplete'] ? 'autocomplete="off"' : '') . 
			' method="post" action="/app/run-post.php" ' . 
			(isset($action['params']['ajax']) && $action['params']['ajax'] ? 'data-ajax' : '') . 
			'>
			<input type="hidden" name="_action" value="' . $actionController . '" />
			<input type="hidden" name="_csrf_token" value="' . CSRF::get() . '" />
			' . $parser . '
		</form>';

	}

	/**
	 * Обработка html-формы
	 * 
	 * @param string $actionController - класс и метод контроллера формы
	 * @param array $post - данные формы
	 * @return void
	 */
	public static function do(string $actionController, array $post) : void {

		// Защита от межсайтовой подделки запросов
		if(!CSRF::safely($post)) {
			self::sendResult(false, 'Недопустимый токен CSRF');
		}

		// Загрузка схемы и коллбэка
		$action = self::load($actionController);

		$error = null;

		// Удаление из списка данных локальных элементов
		foreach($post as $key => $value) {
			if($key[0] == '_') {
				unset($post[$key]);
			}
		}

		// Сохранение данных для следущего отображения html-формы
		$_SESSION['action']['saved'] = [];
		foreach($action['scheme'] as $name => $data) {
			$_SESSION['action']['saved'][$name] = $post[$name];
		}

		// Валидация данных
		foreach($action['scheme'] as $name => $data) {
			if(empty($data['validate'])) {
				continue;
			}
			foreach($data['validate'] as $validate) {
				if(preg_match('/' . $validate[0] . '/', $post[$name])) {
					continue;
				}
				$_SESSION['action']['element'] = $name;
				self::sendResult(false, $validate[1]);
			}
		}

		// Возвращение результата выполнения
		self::sendResult($action['callback']($post));

	}

	/**
	 * Интеграция модуля в шаблонизатор
	 * 
	 * @return void
	 */
	public static function integrate() : void {

		App::$template->addFunction(
			new \Twig_SimpleFunction('action', function(string $form, array $default = []) : string {
				return Action::render($form, $default);
			})
		);

		Assets::add('js', [ 'run-post' ], 'system/');

	}

	/**
	 * Проверка на наличие данных ajax
	 * 
	 * @param array $list - список ключей
	 * @param array $check - массив для проверки
	 * @return void
	 */
	public static function exists(array $list, array $check = null) : void {

		$check = $check ?? $_POST;

		foreach ($list as $post) {
			if(empty($check[$post])) {
				echo self::$result(false, 'Потерян параметр `' . $post . '`');
			}
		}

	}

	/**
	 * Подготовка результата выполнения ajax или html-формы
	 * 
	 * @param bool $status - флаг состояние ответа (true - успешно, false - ошибка)
	 * @param mixed $result - данные, передоваемые клиенту
	 * @param string $redirect - адрес редиректа
	 * @return mixed
	 */
	public static function result(bool $status, $result = null, $redirect = null) {

		$result = [
			'status' => $status, 
			'result' => $result, 
			'redirect' => (!is_null($redirect) && $redirect[0] != '/') ? '/' . $redirect : $redirect
		];

		if(isset($_SERVER['REQUEST_AJAX'])) {
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
			exit;
		} else {
			return $result;
		}

	}

	/**
	 * Возвращение результата выполнения ajax или html-формы
	 * 
	 * @param mixed $status - флаг состояние ответа (true - успешно, false - ошибка)
	 * @param mixed $result - данные, передоваемые клиенту
	 * @param string $redirect - адрес редиректа
	 * @return void
	 */
	private static function sendResult($status, $result = null, $redirect = null) : void {

		if(is_string($status)) {
			echo $status;
			return;
		} else if(is_array($status)) {
			$redirect = $status['redirect'];
			$result = $status['result'];
			$status = $status['status'];
		}

		if(isset($_SERVER['REQUEST_AJAX'])) {
			self::result($status, $result);
		} else {
			if(!is_null($result)) {
				$_SESSION['action'][$status ? 'success' : 'error'] = $result;
			}
			if($status) {
				$_SESSION['action']['saved'] = [];
			}
			if($redirect = $redirect ?? $_SERVER['HTTP_REFERER']) {
				header('Location: ' . $redirect);
			}
			exit;
		}

	}

	/**
	 * Загрузка контроллера формы
	 * 
	 * @param string $actionController - класс и метод контроллера формы
	 * @return array
	 */
	private static function load(string $actionController) : array {

		// Генерация контроллера формы в случае его отсутствия
		Generate::parseActionController($actionController);

		// Загрузка класса контроллера
		App::getController($actionController, ($class), ($method));
		if(!class_exists('\Action\Controller', false)) {
			$path = Explorer::path('action', $class);
			if(!file_exists($path)) {
				throw new EngineException('Неизвестный контроллер формы');
			}
			require($path);
		}
		$action = new \Action\Controller;

		// Загрузка данных из класса контроллера
		if(!method_exists($action, $method)) {
			throw new EngineException('Неизвестный метод контроллера формы');
		}
		$action = call_user_func_array([ $action, $method ], []);
		$action['params'] = $action['params'] ?? [];
		return $action;

	}

}