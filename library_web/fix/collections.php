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

// Filter books for the current user
$user_books = array_filter($books, fn($b) => ($b['user_id'] ?? null) == $_SESSION['user_id']);

// Delete book
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    // Filter out the book only if it belongs to the current user
    $books = array_filter($books, fn($b) => !($b['id'] == $delete_id && ($b['user_id'] ?? null) == $_SESSION['user_id']));
    
    // Save the array, re-indexing it (array_values) is good practice for JSON lists
    file_put_contents($booksFile, json_encode(array_values($books), JSON_PRETTY_PRINT));
    header('Location: collections.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Collection</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="collection-page-body">
<div class="container-small">
    <h2>My Book Collection</h2>
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    <a href="add_book.php" class="add-book-btn add-float">+ Add Book</a>

    <table class="data-table">
        <thead>
            <tr>
                <th>Title</th><th>Author</th><th>Genre</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($user_books as $book): ?>
            <tr>
                <td><?php echo htmlspecialchars($book['title'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($book['author'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($book['genre'] ?? 'N/A'); ?></td>
                <td><span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $book['status'] ?? 'to-read')); ?>"><?php echo htmlspecialchars($book['status'] ?? 'N/A'); ?></span></td>
                <td>
                    <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="action-link edit-link">Edit</a> | 
                    <a href="?delete=<?php echo $book['id']; ?>" onclick="return confirm('Are you sure you want to delete this book?')" class="action-link delete-link">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty($user_books)): ?>
        <p class="no-data-message">Your collection is empty. <a href="add_book.php">Start adding books now!</a></p>
    <?php endif; ?>
    
</div>
</body>
</html>