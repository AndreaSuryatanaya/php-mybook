<?php

require_once "./db.php";

$keyword = trim($_GET['q'] ?? '');


// Logic = kalo keyword nya ada tampilkan query berdasarkan keyword
//  kalo keyword ga ada tampilkan semua data

if ($keyword !== '') {
   try {
      $searchTerm = '%' . $keyword . '%';
      $stmt = $pdo->prepare(
         'SELECT mb.id, 
       mb.title, 
       mb.author, 
       mc.category_name 
     FROM mybook mb INNER JOIN mybook_category mc ON mb.category_id = mc.id
     WHERE mb.title LIKE :kw_title OR mb.author LIKE :kw_author OR mc.category_name LIKE :kw_category'
      );
      $stmt->execute([
         'kw_title'  => $searchTerm,
         'kw_author' => $searchTerm,
         'kw_category' => $searchTerm,
      ]);
      $books = $stmt->fetchAll();
   } catch (PDOException $e) {
      echo "Failed get data from database";
   }
} else {
   try {
      $sql = "
   SELECT 
   mb.id, 
   mb.title, 
   mb.author, 
   mc.category_name 
   FROM mybook mb INNER JOIN mybook_category mc ON mb.category_id = mc.id ORDER BY mb.id DESC";

      $stmt = $pdo->query($sql);
      $books = $stmt->fetchAll();

      // echo '<pre>';
      // var_dump($books);
      // echo '</pre>';
   } catch (PDOException $e) {
      error_log('Gagal mengambil data buku: ' . $e->getMessage());
      die('Terjadi kesalahan saat memuat data buku.');
   }
}




// PERULANGAN
// foreach

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
   <div class="container">
      <h2 class="mt-8">Data mybook from database</h2>
      <a href="/create.php">Create new data</a>

      <!-- Fitur Search -->
      <form action="" method="GET" class="d-flex mt-8 gap-2">
         <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control w-25" placeholder="Search book ...">
         <button type="submit" class="btn-solid theme-accent">Search</button>
      </form>


      <table class="table table-hover mt-8">
         <thead>
            <tr class="theme-accent">
               <th scope="col">Id</th>
               <th scope="col">Title</th>
               <th scope="col">Author</th>
               <th scope="col">Category</th>
               <th scope="col">Action</th>
            </tr>
         </thead>
         <tbody>
            <?php $no = 1; ?>
            <?php foreach ($books as $book): ?>
               <tr>
                  <td><?= $no++ ?></td>
                  <td><?= $book['title'] ?></td>
                  <td><?= $book['author'] ?></td>
                  <td><?= $book['category_name'] ?></td>
                  <td><a href="/update.php?id=<?= $book['id'] ?>">Edit</a></td>
               </tr>
            <?php endforeach; ?>
         </tbody>
      </table>
   </div>
</body>

</html>