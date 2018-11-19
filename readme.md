# IQUEUE SYSTEM 
## (SISTEM INFORMASI ANTRIAN BAHASA INDONESIA MULTI LOKASI)

### Requirements
Install Socket.io server dan initialize [tlaverdure/laravel-echo-server](https://github.com/tlaverdure/laravel-echo-server)
Install dan jalankan [Redis](https://redis.io/) (Laravel echo server require redis)

### Installation
```shell
composer require hasnularief/iqueue
```
### Export configurations
``` shell
php artisan vendor:publish --tag=iqueue
```
Set permission folder `public/iqueue/ticket` to `rw`
Set `printer_name` and `printer_type` in `config\iqueue.php`
Set `BROADCAST_DRIVER=redis` in `.env`
Set `timezone` in `config\app.php`
Uncomment `BroadcastServiceProvider` in `config\app.php`
Finally run `php artisan config:cache`

### Run Services
Run laravel-echo-server
```shell
laravel-echo-server start
```
Run laravel queue
```shell
php artisan queue:work
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