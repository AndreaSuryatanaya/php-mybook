<?php

require_once "./db.php";

try {
   $sql = "
   SELECT 
   mb.id, 
   mb.title, 
   mb.author, 
   mc.category_name 
   FROM mybook mb INNER JOIN mybook_category mc ON mb.category_id = mc.id";

   $stmt = $pdo->query($sql);
   $books = $stmt->fetchAll();

   // echo '<pre>';
   // var_dump($books);
   // echo '</pre>';
} catch (PDOException $e) {
   error_log('Gagal mengambil data buku: ' . $e->getMessage());
   die('Terjadi kesalahan saat memuat data buku.');
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
   <h2>Data mybook from database</h2>
   <table class="table">
      <thead>
         <tr>
            <th scope="col">Id</th>
            <th scope="col">Title</th>
            <th scope="col">Author</th>
            <th scope="col">Category</th>
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
            </tr>
         <?php endforeach; ?>
      </tbody>
   </table>
</body>

</html>