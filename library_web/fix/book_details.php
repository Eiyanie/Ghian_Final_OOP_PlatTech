<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die("Unauthorized.");
}

$dataDir = __DIR__ . '/data';
$booksFile = $dataDir . '/books.json';
$books = file_exists($booksFile) ? json_decode(file_get_contents($booksFile), true) : [];
if (!is_array($books)) $books = [];

$id = intval($_REQUEST['id'] ?? 0);
$book_index = -1;
$book = null;

// Find the book and its index
foreach ($books as $index => $b) {
    if ($b['id'] == $id && ($b['user_id'] ?? null) == $_SESSION['user_id']) {
        $book = $b;
        $book_index = $index;
        break;
    }
}

if (!$book) {
    http_response_code(404);
    die("<p class='error'>Book not found or access denied.</p>");
}

// --- ACTION HANDLING ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        // Delete the book
        unset($books[$book_index]);
        $books = array_values($books);
        file_put_contents($booksFile, json_encode($books, JSON_PRETTY_PRINT));
        die("SUCCESS: Book deleted.");
        
    } elseif ($action === 'update') {
        // Update fields
        $book['title'] = trim($_POST['title']);
        $book['author'] = trim($_POST['author']);
        $book['genre'] = trim($_POST['genre']);
        $book['status'] = $_POST['status'];
        $book['favorite'] = isset($_POST['favorite']) && $_POST['favorite'] === 'on';
        
        // Save the updated book
        $books[$book_index] = $book;
        file_put_contents($booksFile, json_encode(array_values($books), JSON_PRETTY_PRINT)); 
        
        die("SUCCESS: Book details updated.");
    }
}
// --- END ACTION HANDLING ---

// --- MODAL HTML OUTPUT (for GET request) ---
$allowed_statuses = ['To Read', 'Currently Reading', 'Read'];
$is_favorite = ($book['favorite'] ?? false) ? 'checked' : ''; 
?>

<div class="modal-header">
    <h3 class="modal-title">Book Details: <?php echo htmlspecialchars($book['title']); ?></h3>
</div>

<form id="book-details-form" method="POST" action="book_details.php">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="action" value="update">

    <div class="modal-body-grid">
        <div class="modal-form-section">
            <div class="input-group">
                <label for="title">Title:</label>
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
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <?php foreach ($allowed_statuses as $status_option): ?>
                        <option value="<?php echo $status_option; ?>" <?php echo (($book['status'] ?? '') === $status_option) ? 'selected' : ''; ?>>
                            <?php echo $status_option; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-group checkbox-group">
                <input type="checkbox" id="favorite" name="favorite" <?php echo $is_favorite; ?>>
                <label for="favorite" class="checkbox-label">⭐ Mark as Favorite</label>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btn-modal">Save Changes</button>
        <button type="button" id="delete-book-btn" class="btn btn-danger btn-modal" data-id="<?php echo $id; ?>">Delete Book</button>
    </div>
</form>