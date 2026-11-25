<?php

require_once "./db.php";
$keyword = trim($_GET['q'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 5;

$prevPage = max(1, $page - 1);
$nextPage = $page + 1;

$queryParams = array_filter(
   ['q' => $keyword],
   static fn($val) => $val !== '' && $val !== null
);

$books = [];
$totalRows  = 0; //12
$totalPages = 1;


// Logic = kalo keyword nya ada tampilkan query berdasarkan keyword
//  kalo keyword ga ada tampilkan semua data
try {
   // kondisi jika ada search yang aktif
   if ($keyword !== '') {
      $searchTerm = '%' . $keyword . '%';

      $countStmt = $pdo->prepare(
         'SELECT COUNT(*)
          FROM mybook mb
          INNER JOIN mybook_category mc ON mb.category_id = mc.id
          WHERE mb.title LIKE :kw_title
             OR mb.author LIKE :kw_author
             OR mc.category_name LIKE :kw_category'
      );
      $countStmt->execute([
         'kw_title'    => $searchTerm,
         'kw_author'   => $searchTerm,
         'kw_category' => $searchTerm,
      ]);
      $totalRows = (int) $countStmt->fetchColumn();
      $totalPages = max(1, (int) ceil($totalRows / $limit));
      $page       = min($page, $totalPages);
      $offset     = ($page - 1) * $limit;


      $stmt = $pdo->prepare(
         'SELECT mb.id,
                    mb.title,
                    mb.author,
                    mc.category_name
             FROM mybook mb
             INNER JOIN mybook_category mc ON mb.category_id = mc.id
             WHERE mb.title LIKE :kw_title
                OR mb.author LIKE :kw_author
                OR mc.category_name LIKE :kw_category
             ORDER BY mb.id DESC
             LIMIT :limit OFFSET :offset'
      );
      $stmt->bindValue(':kw_title', $searchTerm, PDO::PARAM_STR);
      $stmt->bindValue(':kw_author', $searchTerm, PDO::PARAM_STR);
      $stmt->bindValue(':kw_category', $searchTerm, PDO::PARAM_STR);
   } else {
      // kondisi tampilkan semua data

      $countStmt = $pdo->prepare(
         'SELECT COUNT(*)
          FROM mybook mb
          INNER JOIN mybook_category mc ON mb.category_id = mc.id'
      );
      $countStmt->execute();
      $totalRows = (int) $countStmt->fetchColumn();

      $totalPages = max(1, (int) ceil($totalRows / $limit)); //12 : 4 = 3
      $page       = min($page, $totalPages);
      $offset     = ($page - 1) * $limit;

      $stmt = $pdo->prepare(
         'SELECT mb.id,
                    mb.title,
                    mb.author,
                    mc.category_name
             FROM mybook mb
             INNER JOIN mybook_category mc ON mb.category_id = mc.id
             ORDER BY mb.id DESC
             LIMIT :limit OFFSET :offset'
      );
   }

   $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
   $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
   $stmt->execute();
   $books = $stmt->fetchAll();
} catch (PDOException $e) {
   error_log('Gagal mengambil data buku: ' . $e->getMessage());
   echo 'Failed get data from database';
}

// $prevPage   = max(1, $page - 1);
// $nextPage   = min($totalPages, $page + 1);
$isFirstPage = $page <= 1; //true/false
$isLastPage  = $page >= $totalPages; //true/false

// Fitur DELETE
if (isset($_POST['delete'])) {
   // DELETE FROM mybook WHERE id=12;
   $id = (int)$_POST['id'];

   try {
      $stmt = $pdo->prepare('DELETE FROM mybook WHERE id = :id');
      $stmt->execute(['id' => $id]);

      header('Location: index.php');
      exit;
   } catch (PDOException $e) {
      echo "Failed delete data";
      header('Location: index.php');
      exit;
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
                  <td class="d-flex gap-4 align-items-center">
                     <button class="btn-subtle theme-accent">
                        <a style="text-decoration: none;" href=" /update.php?id=<?= $book['id'] ?>">Edit</a>
                     </button>
                     <!-- Delete button -->
                     <form action="" method="POST" onsubmit="return confirm('Yakin ingin menghapus buku ini?');">
                        <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">
                        <button name=" delete" type="submit" class="btn-subtle theme-danger">Delete</button>
                     </form>
                     <!--  -->
                  </td>
               </tr>
            <?php endforeach; ?>
         </tbody>
      </table>

      <!-- Pagination -->
      <ul class="pagination">
         <li class="page-item <?= $isFirstPage ? 'disabled' : '' ?>">
            <a
               href="<?= $isFirstPage ? '#' : '?' . http_build_query($queryParams + ['page' => $prevPage]) ?>"
               class="page-link"
               <?= $isFirstPage ? 'tabindex="-1" aria-disabled="true"' : '' ?>>Previous</a>
         </li>

         <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php $isCurrentPage = ($i === $page); ?>
            <li class="page-item <?= $isCurrentPage ? 'active' : '' ?>">
               <a
                  class="page-link"
                  href="?<?= http_build_query($queryParams + ['page' => $i]) ?>"
                  <?= $isCurrentPage ? 'aria-current="page"' : '' ?>><?= $i ?></a>
            </li>
         <?php endfor; ?>

         <li class="page-item <?= $isLastPage ? 'disabled' : '' ?>">
            <a
               href="<?= $isLastPage ? '#' : '?' . http_build_query($queryParams + ['page' => $nextPage]) ?>"
               class="page-link"
               <?= $isLastPage ? 'tabindex="-1" aria-disabled="true"' : '' ?>>Next</a>
         </li>
      </ul>
   </div>
</body>

</html>