<?php

namespace App;

/**
 * netBird/Paginator
 *
 * Модуль переключателя страниц
 * 
 * @package netBird
 * @author Essle Jaxcate <me@essle.ru>
 * @copyright Copyright (c) 2013 netBird, Inc
 */
class Paginator {

	/**
	 * Общее количество контента
	 *
	 * @var int
	 */
	private $count;

	/**
	 * Количество контента на одной странице
	 *
	 * @var int
	 */
	private $onPage;
	
	/**
	 * Текущая страница
	 *
	 * @var int
	 */
	private $page;

	/**
	 * Общее количество страниц
	 *
	 * @var int
	 */
	private $pageCount;

	/**
	 * Ссылка переключателя страницы
	 *
	 * @var string
	 */
	private $link;
	
	/**
	 * Конструктор переключателя страниц
	 * 
	 * @return Paginator
	 */
	public function __construct(int $count, int $onPage = 10) {
		
		$this->count = $count;
		$this->pageCount = ceil($count / $onPage);
		$this->page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$this->onPage = $onPage;

		$link = $_SERVER['REQUEST_URI'];
		if(isset($_GET['page'])) {
			$link = preg_replace([ '#&page=(\S+)#', '#&page#' ], '', $link);
		}
		$this->link = $link . '&page=';

	}
	
	/**
	 * Проверка на валидность текущей страницы
	 * 
	 * @return bool
	 */
	public function isValid() : bool {

		return ($this->page >= 1 && $this->page <= $this->pageCount);

	}
	
	/**
	 * Фильтр выборки для sql-запроса
	 * 
	 * @return string
	 */
	public function selection() : string {

		return (($this->page - 1) * $this->onPage) . ',' . $this->onPage;

	}
		
	/**
	 * Подготовка данных для отображения переключателей страниц
	 * 
	 * @return array
	 */
	public function parse() : array {

		$data = [];

		if($this->pageCount < 2) {
			return $data;
		}

		$limit = ($this->pageCount <= $this->page + 2) ? $this->pageCount : $this->page + 2;
		
		if($this->page > 4) {
			$data[] = [
				'type' => 'link',
				'link' => $this->link . '1',
				'name' => '1'
			];
			$data[] = [
				'type' => 'text',
				'name' => '...'
			];
		}
		for($i = ($this->page < 4) ? 1 : $this->page - 2; $i <= $limit; ++$i) {
			if($this->page == $i) {
				$data[] = [
					'type' => 'span',
					'name' => $i
				];
			} else {
				$data[] = [
					'type' => 'link',
					'link' => $this->link . $i,
					'name' => $i
				];
			}
		}
		if($limit + 1 < $this->pageCount) {
			$data[] = [
				'type' => 'text',
				'name' => '...'
			];
			$data[] = [
				'type' => 'link',
				'link' => $this->link . $this->pageCount,
				'name' => $this->pageCount
			];
		}

		return $data;

	}
	
}