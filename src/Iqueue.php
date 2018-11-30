<?php

namespace Hasnularief\Iqueue;

use Illuminate\Database\Eloquent\Model;

class Iqueue extends Model
{
  	public function __construct()
	{
	 	parent::__construct();
		
	 	$this->connection = config('iqueue.connection');
	}
}
