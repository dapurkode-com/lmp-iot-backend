# LMP IoT Dashboard

IntelliFlector adalah sebuah smart device yang berbentuk mirror, hardware yang digunakan adalah monitor yang dipasangkan di belakang cermin 2 arah dan menampilkan berbagai informasi yang berkaitan dengan device smart home seperti konsumsi daya dari device smart home dan juga bisa untuk mengontrol device tersebut.

LMP IoT Dashboard adalah aplikasi website yang nantinya akan dipasang pada IntelliFlector. Dashboard ini berfungsi mengumpulkan dan menampilkan data dari berbagai modul yang nantinya terpasang pada IntelliFlector.

- [LMP IoT Dashboard](#lmp-iot-dashboard)
  - [Requirement Aplikasi](#requirement-aplikasi)
  - [Langkah-langkah dalam pengaplikasian sistem](#langkah-langkah-dalam-pengaplikasian-sistem)
  - [Dokumentasi](#dokumentasi)

## Requirement Aplikasi

Pastikan Anda sudah menginstall :

-[PHP ( _min 7.4.\*_ )](https://www.php.net/downloads.php)

-[MySQL Server](https://dev.mysql.com/downloads/mysql/)

-[Composer](https://getcomposer.org/download/)

## Langkah-langkah dalam pengaplikasian sistem

Berikut adalah langkah-langkah yang harus dilakukan untuk mengaplikasikan sistem ini.

**1. Download vendor-vendor PHP.**

Jalankan sintaks _**composer install**_.

**2. Buat file _.env_**

Buat file baru dengan nama _.env_ dengan isi file menyalin konten dari file _.env.example_ . Sesuaikan pada bagian konfigurasi database dan MQTT Broker.

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=root
    DB_PASSWORD=
    
    MQTT_HOST=
    MQTT_PORT=
    MQTT_TLS_ENABLED=
    MQTT_AUTH_USERNAME=
    MQTT_AUTH_PASSWORD=

**3. Generate key.**

Generate key encryption aplikasi dengan menjalankan sintaks _**php artisan key:generate**_

**4. Jalankan migrasi database dan seeder.**

Jalankan sintaks _**php artisan migrate --seed**_ untuk melakukan migrasi database dan seeding.

**5. Jalankan aplikasi.**

Jalankan aplikasi dengan menggunakan sintaks _**php artisan serve**_ .

## Dokumentasi

Dokumentasi dari REST API aplikasi dapat dilihat pada  [halaman /api/docs](/api/docs) setelah menjalankan aplikasi.
