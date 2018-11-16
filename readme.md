https://www.cnet.com/how-to/how-to-log-on-to-windows-7-automatically/

IQUEUE SYSTEM (SISTEM INFORMASI ANTRIAN BAHASA INDONESIA)

Installation

REQUIRE FIRST

php artisan vendor:publish --tag=iqueue

Laravel user must be exist at least one.
	php artisan make:auth
	php artisan migrate
Register user in http://your-app.test/register
Set user_id in config/iqueue.php, Default 1
Set printer name
Set printer type
Set broadcast driver redis

Install and run https://redis.io/ (Laravel echo server require redis)
Install globally and run https://github.com/tlaverdure/laravel-echo-server as laravel echo broadcasting server with socket.io
Run php artisan queue:work 
You can specify queue_name in config and set connection to redis

Iqueue will delete previous days when opening tv page. Make sure your timezone setting up correctly.


http://your-app.test/iqueue/tv?location=location
http://your-app.test/iqueue/ticket?location=location