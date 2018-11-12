<?php # example

namespace Thing;

class Settings extends \App\Thing {

	protected $table = 'settings';

	protected $fields = [
		'key' 	=> [ 'type' => 'varchar(32)', 'uniq' => true ],
		'value' => [ 'type' => 'varchar(128)' ]
	];

	protected function __onCheckout(bool $result) : void {

		if(!$result) {
			$this->insert([
				[ 'key' => 'sitename', 	'value' => 'netBird' ],
				[ 'key' => 'email', 	'value' => 'me@essle.ru' ],
				[ 'key' => 'adminkey', 	'value' => 'root' ]
			]);
		}

	}

	public function parse() : array {

		$data = $this->select();

		$settings = [];
		foreach($data as $value) {
			$settings[$value['key']] = is_numeric($value['value']) ? (int)$value['value'] : $value['value'];
		}

		return $settings;

	}

}