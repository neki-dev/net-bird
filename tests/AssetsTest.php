<?php

use PHPUnit\Framework\TestCase;
use App\Assets;

class AssetsTest extends TestCase {

	public function testCombine() {
		$this->assertInternalType('string', Assets::combine('css', [ 'system/interface' ]));
	}

	public function testSetGetAdd() {
		Assets::set('css', [ 'system/interface' ]);
		Assets::add('css', [ 'core' ]);
		$this->assertEquals([ 'system/interface', 'core' ], Assets::get('css'));
	}

}