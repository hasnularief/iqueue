

IQUEUE SYSTEM (SISTEM INFORMASI ANTRIAN BAHASA INDONESIA)

Installation

REQUIRE FIRST

php artisan vendor:publish --tag=iqueue

Set printer name
Set printer type
Set BROADCAST_DRIVER=redis in .env
Uncomment BroadcastServiceProvider in config\app.php
run artisan config:cache

Install and run https://redis.io/ (Laravel echo server require redis)
Install secara global dan jalankan https://github.com/tlaverdure/laravel-echo-server as laravel echo broadcasting server with socket.io
Run php artisan queue:work 

Iqueue akan menghapus tanggal sebelumnya saat membuka halaman tv. Pastikan timezone sudah diatur dengan benar.
http://your-app.test/iqueue/tv?location=location
http://your-app.test/iqueue/ticket?location=location

Untuk printer type windows, set net use connection from cmd. Windows harus memiliki user name dan password
net use LPT1: "\\COMPUTER_PRINTER\PRINTER_NAME" /persistent:yes /user:"computer user" password
coba hapus dan buat lagi, dan restart service net use jika gagal.

Untuk otomatis login user
https://www.cnet.com/how-to/how-to-log-on-to-windows-7-automatically/