<?php

use PHPUnit\Framework\TestCase;
use App\Explorer;

class ExplorerTest extends TestCase {

	public function testPath() {
		$this->assertEquals('app' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'Explorer.php', Explorer::path('class', 'Explorer'));
	}

	public function testConfigure() {
		$this->assertInternalType('array', Explorer::configure());
	}

	public function testInfo() {
		$this->assertInternalType('array', Explorer::info('D:/root/test.php'));
		$this->assertEquals('php', Explorer::info('D:/root/test.php', 'extension'));
	}

	public function testIsPublicPath() {
		$this->assertEquals(true, Explorer::isPublicPath('app/library/extra'));
		$this->assertEquals(false, Explorer::isPublicPath('app/library'));
	}

}