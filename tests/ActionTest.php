<?php

use PHPUnit\Framework\TestCase;
use App\Action;

class ActionTest extends TestCase {

	public function testResult() {
		$this->assertEquals([
			'status' => true, 
			'redirect' => '/test',
			'result' => 'done'
		], Action::result(true, 'done', 'test'));
	}

}