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

		$thing = 'Things_' . $thing;

		return new $thing();

	}
	
	/**
	 * Проверка базы данных на соответствие схеме вещи
	 * 
	 * @return void
	 */
	public function checkout() : void {

		$result = App::$DB->isTableExists($this->table);
		if($result) {

			// Удаление лишних столбцов
			$outFields = App::$DB->getTableFields($this->table);
			foreach ($outFields as $field) {
				if(empty($this->fields[$field])) {
					App::$DB->removeTableField($this->table, $field);
				}
			}

			// Создание недостающих столбцов
			foreach ($this->fields as $field => $data) {
				if(!App::$DB->isTableFieldExists($this->table, $field)) {
					App::$DB->addTableField($this->table, $field, $data);
				}
			}

		} else {
			App::$DB->createTable($this->table, $this->fields);
		}

		if(method_exists($this, '__onCheckout')) {
			$this->__onCheckout($result);
		}

	}
	
	/**
	 * Добавление записей в таблицу
	 * 
	 * @param array $data - массив данных (столбец => значение)
	 * @return int
	 */
	public function insert(array $data) : int {

		return App::$DB->insert($this->table, $data);

	}
	
	/**
	 * Выборка записей из таблицы
	 * 
	 * @param string $fields - перечень полей
	 * @param string $sql - sql-постфикс
	 * @param array $values - массив встраиваемых значений
	 * @return array
	 */
	public function select(string $fields = '*', string $sql = '', array $values = [], int &$count = NULL) : array {

		return App::$DB->select($this->table, $fields, $sql, $values, $count);

	}
	
	/**
	 * Выборка записей из таблицы в экранированом json формате
	 * 
	 * @param string $fields - перечень полей
	 * @param string $sql - sql-постфикс
	 * @param array $values - массив встраиваемых значений
	 * @return string
	 */
	public function selectEncoded(string $fields = '*', string $sql = '', array $values = [], int &$count = NULL) : string {

		return str_replace(
			[ "'", '"' ], [ "\'", '\"' ], 
			json_encode(
				App::$DB->select($this->table, $fields, $sql, $values, $count)
			)
		);

	}
	
	/**
	 * Удаление записей из таблицы
	 * 
	 * @param string $sql - sql-постфикс
	 * @param array $values - массив встраиваемых значений
	 * @return void
	 */
	public function delete(string $sql = '', array $values = []) : void {

		App::$DB->delete($this->table, $sql, $values);

	}
	
	/**
	 * Подсчет количества записей в таблице
	 * 
	 * @param string $sql - sql-постфикс
	 * @param array $values - массив встраиваемых значений
	 * @return int
	 */
	public function counted(string $sql = '', array $values = []) : int {

		return App::$DB->counted($this->table, $sql, $values);

	}
	
	/**
	 * Обновление значений в заданных столбцах таблицы
	 * 
	 * @param array $data - массив данных (столбец => значение)
	 * @param string $sql - sql-постфикс
	 * @param array $values - массив встраиваемых значений
	 * @return void
	 */
	public function update(array $data, string $sql = '', array $values = []) : void {

		App::$DB->update($this->table, $data, $sql, $values);
		
	}

}