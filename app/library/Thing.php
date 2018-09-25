<?php

namespace App;

/**
 * netBird/Thing
 *
 * Экземпляр вещи
 * 
 * @package netBird
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */
abstract class Thing {

	/**
	 * Таблица вещи
	 *
	 * @var string
	 */
	protected $table = '';
	
	/**
	 * Информация о столбцах таблицы (свойства вещи)
	 *
	 * @var array
	 */
	protected $fields = [];
	
	/**
	 * Конструктор вещи
	 * 
	 * @return Thing
	 */
	public function __construct() {

		// Проверка базы данных
		$this->checkout();

	}
	
	/**
	 * Статический метод создания вещи
	 * 
	 * @param string $thing - имя вещи
	 * @return Thing
	 */
	public static function at(string $thing) : Thing {

		$thing = '\\Thing\\' . $thing;

		return new $thing();

	}
	
	/**
	 * Проверка базы данных на соответствие схеме вещи
	 * 
	 * @return void
	 */
	public function checkout() : void {

		// Проверка на наличие таблицы
		$result = App::$DB->isTableExists($this->table);
		if($result) {

			// Удаление лишних столбцов
			$outFields = App::$DB->getTableFields($this->table);
			foreach($outFields as $field) {
				if(empty($this->fields[$field])) {
					App::$DB->removeTableField($this->table, $field);
				}
			}

			// Создание недостающих столбцов
			foreach($this->fields as $field => $data) {
				if(!App::$DB->isTableFieldExists($this->table, $field)) {
					App::$DB->addTableField($this->table, $field, $data);
				}
			}

		} else {

			// Создание таблицы
			App::$DB->createTable($this->table, $this->fields);

		}

		if(method_exists($this, '__onCheckout')) {
			$this->__onCheckout($result);
		}

	}
	
	/**
	 * Наследование методов у модуля базы данных
	 * 
	 * @param string $name - имя метода
	 * @param array $arguments - аргументы
	 * @return mixed
	 */
	public function __call($name, $arguments) {

		if((App::$DB)::isExpansion($name)) {

			// Добавление названия таблицы текущей вещи к остальных аргументам
			array_unshift($arguments, $this->table);

			// Вызов метода
			return call_user_func_array([ App::$DB, $name ], $arguments);

		} else {
			throw new EngineException('Неизвестный метод ' . $name . ' вещи ' . get_class($this));
		}

	}

}