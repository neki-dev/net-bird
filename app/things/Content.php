<?php

namespace Things;

class Content extends \App\Thing {

	protected $table = 'content';

	protected $fields = [
		'id' 		=> [ 'type' => "ai", 'uniq' => true ],
		'title' 	=> [ 'type' => "varchar(128)" ],
		'content' 	=> [ 'type' => "text" ],
		'photo' 	=> [ 'type' => "varchar(128)" ],
		'postdate' 	=> [ 'type' => "int(11)" ]
	];

}