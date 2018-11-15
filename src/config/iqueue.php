<?php

return [

	// Laravel Broadcast require authenticate user
	'user_id' => '1',

	// Iqueue connection database
	'connection' => 'mysql',

	// Laravel Queue name
	'queue_name' => null, 

	// Iqueue Support Multiple locations
	'locations' => [

		'lobby' => [
			'alias' => 'Lobi',
			'counters'  => ['L111', 'L222', 'L333', 'L444', 'L555'], //must be unique
			'types'  => ['U', 'B', 'J', 'G'],
			'tv_blade' => null, //if null use default blade
			'ticket_blade' => null, //if null use default blade
			'printer' => 'localhost/Epson TM-U220 Receipt',
			'printer_type' => 'network', // windows | network
			'print_copy' => 1,
			'ticket_template' => null, //if null use default template
		],

		'pharmacy' => [
			'alias' => 'Instalasi Farmasi',
			'counters'  => ['!F111', '!F222', 'F333', 'F444', 'F555'], //must be unique
			'types'  => ['A', 'B', 'R'],
			'tv_blade' => null, //if null use default blade
			'ticket_blade' => null, //if null use default blade
			'printer' => 'smb://localhost/Epson TM-U220 Receipt',
			'printer_type' => 'windows', // windows | network
			'print_copy' => 1,
			'ticket_template' => null, //if null use default template
		],

		'mcu' => [
			'alias' => 'Medical Checkup',
			'counters'  => ['M111', 'M222', 'M333', 'M444', 'M555'], //must be unique
			'types'  => [], // if empty queue just use number
			'tv_blade' => null, //if null use default blade
			'ticket_blade' => null, //if null use default blade
			'printer' => 'smb://localhost/Epson TM-U220 Receipt',
			'printer_type' => 'windows', // windows | network
			'print_copy' => 1,
			'ticket_template' => null, //if null use default template
		],

		// ... You can add other locations with their configurations
	]

];