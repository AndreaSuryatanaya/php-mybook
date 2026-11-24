<?php

require_once "./db.php";

// GET ambil dari PARAMETER
// $name = $_GET['name'];
// $age = $_GET['age'];

// echo $name;
// echo $age;

// POST ambil dari INPUT
// $email = $_POST['email'];
// $password = $_POST['password'];

// echo $email;
// echo $password;

$errorMessage = '';
$successMessage = '';

// ============>
// 1. Tangkap semua inputan dari USER
// $title = $_POST['title'];
// $author = $_POST['author'];
// $category = $_POST['category_id'];

// 2. Ambil semua data Category dari DB
try {
   $sql = "SELECT * FROM mybook_category";

   $stmt = $pdo->query($sql);
   $categories = $stmt->fetchAll();
   // echo '<pre>';
   // var_dump($categories);
   // echo '</pre>';
} catch (PDOException $e) {
   error_log('Gagal mengambil data buku: ' . $e->getMessage());
   die('Terjadi kesalahan saat memuat data buku.');
}

if (isset($_POST['save']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
   $title = trim($_POST['title'] ?? '');
   $author = trim($_POST['author'] ?? '');
   $categoryId = $_POST['category_id'] ?? '';

   if ($title === '' || $author === '') {
      $errorMessage = 'Judul buku dan penulis wajib diisi.';
   } else {
      // Jika category_id kosong, simpan sebagai NULL (karena kolom category_id mengizinkan NULL)
      $categoryParam = ($categoryId === '') ? null : (int) $categoryId;

      try {
         $insertSql = "
                INSERT INTO mybook (title, author, category_id)
                VALUES (:title, :author, :category_id)
            ";

         $insertStmt = $pdo->prepare($insertSql);
         $insertStmt->execute([
            ':title'       => $title,
            ':author'      => $author,
            ':category_id' => $categoryParam,
         ]);

         $successMessage = 'Data buku berhasil ditambahkan!';

         // kembali ke index.php
         header('Location: index.php');
      } catch (PDOException $e) {
         echo "Gagal insert data ke database";
      }
   }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-B/GM4XqrwHnWXNOWMbloTmrYXZg10cakYGmpfsR/bbzQ6JAJI4ihuyADKLnBgrCe" crossorigin="anonymous">
   <script type="module" src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
</head>

<body>
   <div class="container py-5 min-vh-100 d-flex flex-column justify-content-center">

      <!-- ERROR MESSAGE -->
      <?php if (!empty($errorMessage)): ?>
         <div style="padding: 10px 14px; margin-bottom: 16px; background-color: #fde8e8; color: #9b1c1c; border: 1px solid #f8b4b4; border-radius: 4px;">
            <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
         </div>
      <?php endif; ?>
      <h2>Create data mybook</h2>
      <form action="" method="POST" class="">
         <div class="d-flex flex-column gap-3 w-25">

            <!-- <input type="text" name="title" placeholder="title"> -->
            <label for="title" class="form-label">Title book</label>
            <input type="text" class="form-control" id="title" name="title">
            <!-- <input type="text" name="author" placeholder="author"> -->
            <label for="author" class="form-label">Author</label>
            <input type="text" class="form-control" id="author" name="author">

            <select name="category_id" class="form-control">
               <option value="">-- Pilih Kategori --</option>
               <?php foreach ($categories as $category): ?>
                  <option value="<?= htmlspecialchars((string) $category['id'], ENT_QUOTES, 'UTF-8') ?>">
                     <?= htmlspecialchars($category['category_name'] ?? 'Tanpa Nama', ENT_QUOTES, 'UTF-8') ?>
                  </option>
               <?php endforeach; ?>
            </select>

            <button name="save" class="btn-solid theme-accent">Save</button>
         </div>

      </form>
   </div>

</body>

</html>