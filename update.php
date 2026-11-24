<?php
require_once "./db.php";

$id = $_GET['id'];
// var_dump($id);

// sql
// SELECT * FROM mybook WHERE id=1;
try {
   $sql = "SELECT * FROM mybook WHERE id = :id";

   $stmt = $pdo->prepare($sql);
   $stmt->execute([
      ':id' => $id,
   ]);

   $book = $stmt->fetch();
} catch (PDOException $e) {
   echo "Error get data from database";
}


// AMBIL DATA UNTUK CATEGORY

// 2. Ambil semua data Category dari DB
try {
   $sqlCategory = "SELECT * FROM mybook_category";

   $stmt = $pdo->query($sqlCategory);
   $categories = $stmt->fetchAll();
} catch (PDOException $e) {
   error_log('Gagal mengambil data buku: ' . $e->getMessage());
   die('Terjadi kesalahan saat memuat data buku.');
}

// UPDATE DATA KE DATABASE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $title = $_POST['title'];
   $author = $_POST['author'];
   $categoryId = $_POST['category_id'];

   // var_dump($title, $author, $categoryId);

   // update ke database
   try {
      $sqlUpdate = " UPDATE mybook SET title = :title, author = :author, category_id = :categoryId WHERE id= :id";
      $stmt = $pdo->prepare($sqlUpdate);

      $stmt->execute([
         ':id' => (int)$id,
         ':title' => $title,
         ':author' => $author,
         ':categoryId' => ($categoryId === '') ? null : (int) $categoryId
      ]);

      header('Location: index.php');
      exit;
   } catch (PDOException $e) {
      echo "Error update data ke database";
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
      <!-- <?php if (!empty($errorMessage)): ?>
         <div style="padding: 10px 14px; margin-bottom: 16px; background-color: #fde8e8; color: #9b1c1c; border: 1px solid #f8b4b4; border-radius: 4px;">
            <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
         </div>
      <?php endif; ?> -->


      <h2>Update data mybook</h2>
      <form action="" method="POST" class="">
         <div class="d-flex flex-column gap-3 w-25">

            <!-- <input type="text" name="title" placeholder="title"> -->
            <label for="title" class="form-label">Title book</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= $book['title'] ?>">
            <!-- <input type="text" name="author" placeholder="author"> -->
            <label for="author" class="form-label">Author</label>
            <input type="text" class="form-control" id="author" name="author" value="<?= $book['author'] ?>">

            <select name="category_id" class="form-control">
               <option value="">-- Pilih Kategori --</option>
               <?php foreach ($categories as $category): ?>
                  <?php
                  $catId = (string) $category['id'];

                  //  ini untuk insert data Category id ke Database
                  $currentCatId = isset($_POST['category_id'])
                     ? (string) $_POST['category_id']
                     : (string) ($book['category_id']);

                  //ini untuk tampilan UI yang di pilih
                  $isSelected = ($currentCatId !== '' && $currentCatId === $catId) ? 'selected' : '';
                  ?>

                  <option value="<?= htmlspecialchars((string) $category['id'], ENT_QUOTES, 'UTF-8') ?>" <?= $isSelected ?>>
                     <?= htmlspecialchars($category['category_name'] ?? 'Tanpa Nama', ENT_QUOTES, 'UTF-8') ?>
                  </option>
               <?php endforeach; ?>
            </select>

            <button name="update" class="btn-solid theme-accent">Update</button>
         </div>

      </form>
   </div>

</body>

</html>