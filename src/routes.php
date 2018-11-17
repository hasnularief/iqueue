<?php

Route::group(['name' => 'Iqueue::'], function(){
	Route::get('iqueue', 'hasnularief\iqueue\IqueueController@index');
	Route::get('iqueue/tv', 'hasnularief\iqueue\IqueueController@tv');
	Route::get('iqueue/call', 'hasnularief\iqueue\IqueueController@call');
	Route::get('iqueue/counter', 'hasnularief\iqueue\IqueueController@counter');
	Route::get('iqueue/tickets', 'hasnularief\iqueue\IqueueController@tickets');
	Route::get('iqueue/last', 'hasnularief\iqueue\IqueueController@last');
	Route::get('iqueue/print', 'hasnularief\iqueue\IqueueController@print');
});