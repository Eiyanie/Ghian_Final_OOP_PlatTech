<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$dataDir = __DIR__ . '/data';
if (!file_exists($dataDir)) {
    mkdir($dataDir, 0777, true);
}
$booksFile = $dataDir . '/books.json';

if (!file_exists($booksFile)) {
    file_put_contents($booksFile, json_encode([]));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $allowed_statuses = ['To Read', 'Currently Reading', 'Read'];
    $status = in_array(($_POST['status'] ?? 'To Read'), $allowed_statuses) 
              ? $_POST['status'] 
              : 'To Read'; 
            
    $is_favorite = isset($_POST['favorite']) && $_POST['favorite'] === 'on';

    if (empty($title)) {
        $error = "Book Title is required.";
    } else {
        $books = json_decode(file_get_contents($booksFile), true);
        if (!is_array($books)) $books = [];
        
        $nextId = 1;
        if (!empty($books)) {
            $ids = array_column($books, 'id');
            $nextId = max($ids) + 1;
        }

        $newBook = [
            'id' => $nextId,
            'user_id' => $_SESSION['user_id'],
            'title' => $title,
            'author' => $author,
            'genre' => $genre,
            'status' => $status,
            'favorite' => $is_favorite, // Default to not favorite
        ];

        $books[] = $newBook;

        if (file_put_contents($booksFile, json_encode(array_values($books), JSON_PRETTY_PRINT))) {
            // FIX: Return SUCCESS message for AJAX
            die("SUCCESS: Book added successfully!"); 
        } else {
            $error = "Failed to save book data. Check file permissions.";
        }
    }
}
?>
<div class="modal-header">
    <h2>Add New Book 📚</h2>
</div>
<div class="form-container" style="background: none; padding: 0; margin: 0 auto;">
    <?php if ($error): ?>
        <div id="modal-message" class="modal-message error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" id="add-book-form" action="add_book.php"> 
        <div class="input-group">
            <label for="title">Book Title:</label>
            <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
        </div>
        <div class="input-group">
            <label for="author">Author (Optional):</label>
            <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($_POST['author'] ?? ''); ?>">
        </div>
        <div class="input-group">
            <label for="genre">Genre (Optional):</label>
            <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($_POST['genre'] ?? ''); ?>">
        </div>
        <div class="input-group">
            <label for="status">Reading Status:</label>
            <select id="status" name="status" required>
                <option value="To Read" <?php echo (($_POST['status'] ?? 'To Read') === 'To Read') ? 'selected' : ''; ?>>To Read</option>
                <option value="Currently Reading" <?php echo (($_POST['status'] ?? '') === 'Currently Reading') ? 'selected' : ''; ?>>Currently Reading</option>
                <option value="Read" <?php echo (($_POST['status'] ?? '') === 'Read') ? 'selected' : ''; ?>>Read</option>
            </select>
        </div>
        <div class="input-group checkbox-group">
    <input type="checkbox" id="favorite" name="favorite">
    <label for="favorite" class="checkbox-label">⭐ Mark as Favorite</label>
</div>
        <button type="submit" class="btn-submit btn-primary">Add Book to Collection</button>
        <p style="text-align: center; margin-top: 15px;"><a href="#" onclick="window.closeModal(); return false;" class="back-link">Cancel and Close</a></p>
    </form>
</div>