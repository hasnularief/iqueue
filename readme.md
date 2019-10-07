# IQUEUE SYSTEM 
## (SISTEM INFORMASI ANTRIAN BAHASA INDONESIA MULTI LOKASI)

## Requirement
Laravel 6.0 and configurated database

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
### Configurations
1. Set permission folder `public/iqueue/ticket` to `rw`
2. Set `printer_name` and `printer_type` in `config\iqueue.php`
3. Set `BROADCAST_DRIVER=pusher`, 
       `PUSHER_APP_ID=your_pusher_app_id`, 
       `PUSHER_APP_KEY=your_pusher_app_key`,
       `PUSHER_APP_SECRET=your_pusher_app_secret` in `.env` file
4. Set in `config/broadcasting.php`
```php
// config/broadcasting.php
'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
                'host' => '127.0.0.1', // add_key_value
                'port' => 6001, // add_key_value
                'scheme' => 'http' // add_key_value
            ],
        ], 
```
Set `timezone` in `config\app.php`

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
