<p align="center">
    <img alt="Laravel" src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white">
    <img alt="MySQL" src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white">
</p>

# LMP IoT Dashboard

IntelliFlector adalah sebuah smart device yang berbentuk mirror, hardware yang digunakan adalah monitor yang dipasangkan di belakang cermin 2 arah dan menampilkan berbagai informasi yang berkaitan dengan device smart home seperti konsumsi daya dari device smart home dan juga bisa untuk mengontrol device tersebut.

LMP IoT Dashboard adalah aplikasi website yang nantinya akan dipasang pada IntelliFlector. Dashboard ini berfungsi mengumpulkan dan menampilkan data dari berbagai modul yang nantinya terpasang pada IntelliFlector.

> **Daftar isi**
>- [Requirement Aplikasi](#requirement-aplikasi)
>- [Framework yang digunakan](#framework-yang-digunakan)
>- [Langkah-langkah dalam pengaplikasian sistem](#langkah-langkah-dalam-pengaplikasian-sistem)
>- [Dokumentasi](#dokumentasi)
>- [Catatan](#catatan)
>- [Kontak](#kontak)

## Requirement Aplikasi

Pastikan Anda sudah menginstall :

- [PHP ( _min 7.4.\*_ )](https://www.php.net/downloads.php)

- [MySQL Server](https://dev.mysql.com/downloads/mysql/)

- [Composer](https://getcomposer.org/download/)


## Framework yang digunakan

- [Framework Laravel versi 8](https://laravel.com/docs/8.x)

## Langkah-langkah dalam pengaplikasian sistem

Berikut adalah langkah-langkah yang harus dilakukan untuk mengaplikasikan sistem ini.

#### 1. Clone repository

Lakukan sintaks _clone repository_ seperti sintaks dibawah ini.

    git clone https://github.com/dapurkode-com/lmp-stock-watcher.git

atau dengan mengunduh _repository_ ini kemudian _extract zip file_.

#### 2. Download vendor-vendor PHP.

Masuk ke dalam _folder repository_  kemudian jalankan sintaks `composer install`.

#### 3. Buat file _.env_

Buat file baru dengan nama _.env_ dengan isi file menyalin konten dari file _.env.example_ . Sesuaikan pada bagian konfigurasi berikut ini.

    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=root
    DB_PASSWORD=

#### 4. Generate key

_Generate key encryption_ aplikasi dengan menjalankan sintaks `php artisan key:generate`.

#### 5. Jalankan migrasi _database_ dan _seeder_

Setelah anda memastikan pengaturan basis data di _file .env_ telah sesuai, jalankan sintaks `php artisan migrate --seed` untuk melakukan migrasi.

## Masuk ke dalam Google Fit

Untuk mensinkronasi aplikasi terhadap Google Fit, Anda perlu masuk kedalam Google Fit melalui aplikasi, dengan cara sebagai berikut.

#### 1. 

## Dokumentasi

Dokumentasi dari REST API aplikasi dapat dilihat pada halaman [/api/docs](https://dapurkode-com.github.io/lmp-iot-backend/docs/) setelah menjalankan aplikasi. Anda juga bisa mengunduh dokumentasi seluruh sistem melalui [link ini](docs/files/Dokumentasi.pdf).

## Catatan

Beberapa catatan penting yang perlu diingat.
- Untuk menjalankan aplikasi secara lokal, jalankan sintak `php artisan serve` dan program akan berjalan di `http://localhost:8000`.
- Untuk menjalankan aplikasi secara cloud, silahkan kontak programmer ataupun _system admin_.

## Kontak
> Jika mengalami kendala silahkan kirim _email_ melalui [i.g.b.n.satyawibawa@gmail.com](mailto:i.g.b.n.satyawibawa@gmail.com)
