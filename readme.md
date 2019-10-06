# IQUEUE SYSTEM 
## (SISTEM INFORMASI ANTRIAN BAHASA INDONESIA MULTI LOKASI)

### Requirements
Install Laravel Websocket server dan initialize [beyondcode/laravel-websockets](https://github.com/beyondcode/laravel-websockets)
```shell
composer require beyondcode/laravel-websockets
```

### Optional Requirements
Install pusher-php-server jika menggunakan pusher [pusher/pusher-http-php](https://github.com/pusher/pusher-http-php)
```shell
composer require pusher/pusher-php-server "~3.0"
```

### Installation
```shell
composer require hasnularief/iqueue
```

### Export configurations
``` shell
php artisan vendor:publish --tag=iqueue
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
php artisan migrate
```
Set permission folder `public/iqueue/ticket` to `rw`
Set `printer_name` and `printer_type` in `config\iqueue.php`
Set `'socket' => 'pusher'` in `config\iqueue.php`
Set `DATABASE`, 
    `BROADCAST_DRIVER=pusher`, 
    `PUSHER_APP_ID`
    `PUSHER_APP_KEY`
    `PUSHER_APP_SECRET` in `.env`
Set `'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
                'host' => '127.0.0.1', // edit
                'port' => 6001, // edit
                'scheme' => 'http' // edit
            ],
        ],` in `config\broadcasting.php`
Set `timezone` in `config\app.php`
Uncomment `BroadcastServiceProvider` in `config\app.php`

Finally run `php artisan config:cache` and `php artisan route:cache`

### Run Services
Run websocket-server
```shell
php artisan websocket:serve
```

#### Link TV dan Cetak Ticket
    http://your-app.test/iqueue/tv?location={location}
    http://your-app.test/iqueue/ticket?location={location}

### Link Pemanggil
    http://your-app.test/iqueue/call?location={location}&type={A}&key={counter_key}&mode={CALL}
Atau dapat menggunakan aplikasi windows yang telah disediakan.

### Customize
Untuk mengkustom tampilan tv dan ticket sebelumnya dapat mengeksport blade terlebih dahulu.
    php artisan vendor:publish --tag=iqueue-view
file blade baru akan ada di folder `view\iqueue` project laravel. Silahkan kustom kemudian daftarkan di `config\iqueue.php`

### Catatan
Iqueue akan menghapus database tanggal sebelumnya saat merefresh halaman tv.

### Bantuan
Untuk printer type windows, set net use connection from cmd. Windows harus memiliki user name dan password
```` shell
net use LPT1: "\\COMPUTER_PRINTER\PRINTER_NAME" /persistent:yes /user:"computer user" password
````
Jika gagal coba hapus dan buat lagi, serta restart service net use .

Untuk otomatis login user windows
https://www.cnet.com/how-to/how-to-log-on-to-windows-7-automatically/
