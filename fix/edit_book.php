<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$dataDir = __DIR__ . '/data';
$booksFile = $dataDir . '/books.json';
// Handle case where file might not exist or be empty
$books = file_exists($booksFile) ? json_decode(file_get_contents($booksFile), true) : [];
if (!is_array($books)) $books = [];

// Find the book to edit
$id = intval($_GET['id'] ?? 0);
$book = null;
$book_index = -1;

foreach ($books as $index => $b) {
    if ($b['id'] == $id && ($b['user_id'] ?? null) == $_SESSION['user_id']) {
        $book = $b;
        $book_index = $index;
        break;
    }
}
if (!$book) {
    die("Book not found or you do not have permission to edit it.");
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book['title'] = trim($_POST['title']);
    $book['author'] = trim($_POST['author']);
    $book['genre'] = trim($_POST['genre']); // Added genre
    $book['status'] = $_POST['status'];

    if (empty($book['title'])) {
         $err = "Book Title is required.";
    } else {
        // Update the book in the array
        $books[$book_index] = $book;

        // Save the array
        file_put_contents($booksFile, json_encode($books, JSON_PRETTY_PRINT));
        header('Location: /library_web/fix/collections.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Book</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="form-container">
    <h2>Edit Book: <?php echo htmlspecialchars($book['title']); ?></h2>
    <?php if($err) echo "<div class='error'>$err</div>"; ?>
    <form method="POST" action="edit_book.php?id=<?php echo $id; ?>">
        <div class="input-group">
            <label for="title">Book Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
        </div>
        
        <div class="input-group">
            <label for="author">Author:</label>
            <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($book['author'] ?? ''); ?>">
        </div>

        <div class="input-group">
            <label for="genre">Genre:</label>
            <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($book['genre'] ?? ''); ?>">
        </div>
        
        <div class="input-group">
            <label for="status">Reading Status:</label>
            <select id="status" name="status" required>
                <option value="To Read" <?php if(($book['status'] ?? '')=='To Read') echo 'selected'; ?>>To Read</option>
                <option value="Currently Reading" <?php if(($book['status'] ?? '')=='Currently Reading') echo 'selected'; ?>>Currently Reading</option>
                <option value="Read" <?php if(($book['status'] ?? '')=='Read') echo 'selected'; ?>>Read</option>
            </select>
        </div>
        <button type="submit" class="btn-submit">Save Changes</button>
    </form>
    <p style="text-align: center; margin-top: 15px;"><a href="/library_web/fix/collection.php" class="back-link">Back to Collection</a></p>
</div>
</body>
</html>