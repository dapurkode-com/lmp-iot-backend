<p align="center">
    <img alt="Laravel" src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white">
    <img alt="MySQL" src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white">
</p>

# LMP IoT Dashboard - Back End

IntelliFlector adalah sebuah smart device yang berbentuk mirror, hardware yang digunakan adalah monitor yang dipasangkan
di belakang cermin 2 arah dan menampilkan berbagai informasi yang berkaitan dengan device smart home seperti konsumsi 
daya dari device smart home dan juga bisa untuk mengontrol device tersebut.

LMP IoT Dashboard adalah aplikasi website yang nantinya akan dipasang pada IntelliFlector. Dashboard ini berfungsi 
mengumpulkan dan menampilkan data dari berbagai modul yang nantinya terpasang pada IntelliFlector.

> **Daftar isi**
> - [Requirement Aplikasi](#requirement-aplikasi)
> - [Framework yang digunakan](#framework-yang-digunakan)
> - [Langkah-langkah dalam pengaplikasian sistem](#langkah-langkah-dalam-pengaplikasian-sistem)
> - [Catatan](#catatan)
> - [Sinkronisasi Google Fit](#sinkronisasi-google-fit)
> - [Dokumentasi Lainnya](#dokumentasi-lainnya)
> - [Kontak](#kontak)

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

    git clone https://github.com/dapurkode-com/lmp-iot-backend.git

atau dengan mengunduh _repository_ ini kemudian _extract zip file_.

#### 2. Download vendor-vendor PHP.

Masuk ke dalam _folder repository_  kemudian jalankan sintaks `composer install`.

#### 3. Buat file _.env_

Buat file baru dengan nama _.env_ dengan isi file menyalin konten dari file _.env.example_ . Sesuaikan pada bagian 
konfigurasi berikut ini.

    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=root
    DB_PASSWORD=

#### 4. Generate key

_Generate key encryption_ aplikasi dengan menjalankan sintaks `php artisan key:generate`.

#### 5. Jalankan migrasi _database_ dan _seeder_

Setelah anda memastikan pengaturan basis data di _file .env_ telah sesuai, 
jalankan sintaks `php artisan migrate --seed` untuk melakukan migrasi.

#### 6. Jalankan aplikasi secara lokal

Untuk menjalankan aplikasi secara lokal, jalankan sintak `php artisan serve` 
dan program akan berjalan di `http://localhost:8000`.

## Sinkronisasi Google Fit

Sebelum dapat menggunakan fitur sinkronasi terhadap Google Fit, terlebih dahulu silahkan daftarkan aplikasi ini pada 
Google Apps Console dengan mengikuti dokumentasi [berikut](https://gungsatya.github.io/2021/12/20/cara-mendapatkan-oauth-2-google-fit.html). 
1. Unduh _file credential_ dengan tipe `.json` dan simpan di folder `gclient` dalam _folder root_ aplikasi (Silahkan buat folder jika tidak ada).
2. Sesuaikan _file .env_ bagian `GOOGLE_FIT_AUTH_CONFIG_FILE_NAME` dengan nama _file json_ yang telah diunduh.
3. Jalankan sintak `php artisan mygooglefit:fetch` pada direktori project.
4. Buka url yang tampilkan pada web browser.
5. Masuk kedalam akun Google yang telah tersinkronisasi dengan Google Fit. 

## Catatan

Beberapa catatan penting yang perlu diingat.

- Untuk menjalankan aplikasi secara cloud, silahkan kontak programmer ataupun _system admin_.
- Aplikasi memantau data Google Fit secara berkala dengan menggunakan cron job. Untuk menjalankannya, ikuti dokumentasi 
[berikut](https://laravel.com/docs/8.x/scheduling#running-the-scheduler). 

## Dokumentasi lainnya

Dokumentasi dari REST API aplikasi dapat dilihat pada [halaman ini](https://dapurkode-com.github.io/lmp-iot-backend/docs/) 
atau `/api/docs` setelah menjalankan aplikasi. Anda juga bisa mengunduh dokumentasi seluruh sistem 
melalui [link ini](docs/files/Dokumentasi.pdf).

## Kontak
> Jika mengalami kendala silahkan kirim _email_ melalui [i.g.b.n.satyawibawa@gmail.com](mailto:i.g.b.n.satyawibawa@gmail.com)
