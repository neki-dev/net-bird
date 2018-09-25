<?php

use PHPUnit\Framework\TestCase;
use App\DataBase;

class DBTest extends TestCase {

	public function testOrder() {
		$this->assertEquals(' ORDER BY `id` DESC, `post` DESC', DataBase::order('id, post'));
		$this->assertEquals(' ORDER BY `id` ASC', DataBase::order('id', true));
	}

	public function testGroup() {
		$this->assertEquals(' GROUP BY `region`', DataBase::group('region'));
	}

}