<?php

use PHPUnit\Framework\TestCase;
use App\DB;

class DBTest extends TestCase {

	public function testOrder() {
		$this->assertEquals(' ORDER BY `id` DESC, `post` DESC', DB::order('id, post'));
		$this->assertEquals(' ORDER BY `id` ASC', DB::order('id', true));
	}

	public function testGroup() {
		$this->assertEquals(' GROUP BY `region`', DB::group('region'));
	}

}