<?php

// kita pakai PDO buatk koneksi ke DB

$host = 'localhost'; // atau 'localhost'
$db   = 'learned'; // ganti dengan nama database tempat mybook berada
$user = 'root';
$pass = '';
$charset = 'utf8mb4'; //standart data proses

// Connection string
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
   PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lempar exception jika terjadi error query/koneksi
   PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Hasil query otomatis berbentuk associative array
   PDO::ATTR_EMULATE_PREPARES   => false,                  // Gunakan native prepared statements MySQL
];

// MATERI DIKIT

// array assosiacte
// $data = [
//    'id' => '1',
//    'title' => 'Laskar Pelangi',
//    'author' => 'Andrea Hirata'
// ];

// try {
//    // Jalanini sesuatu kalau ada error lempar ke CATCH
// } catch (error) {
//    // Munculkan error dari TRY
// }


try {

   $pdo = new PDO($dsn, $user, $pass, $options);

   echo "Koneksi berhasil!"; // Aktifkan sementara hanya untuk tes

} catch (PDOException $e) {
   // Jangan tampilkan detail error sensitif ke user di production
   error_log('Koneksi PDO gagal: ' . $e->getMessage());
   die('Terjadi kesalahan saat menghubungkan ke database.');
}
