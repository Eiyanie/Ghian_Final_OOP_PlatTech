<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$booksFile = 'data/books.json';

$books = file_exists($booksFile) ? json_decode(file_get_contents($booksFile), true) : [];
if (!is_array($books)) $books = [];

// Filter books for the current user
$user_books = array_filter($books, fn($b) => ($b['user_id'] ?? null) == $_SESSION['user_id']);

$total = count($user_books);
$read = count(array_filter($user_books, fn($b) => ($b['status'] ?? '') === 'Read'));
$reading = count(array_filter($user_books, fn($b) => ($b['status'] ?? '') === 'Currently Reading')); 
$unread = count(array_filter($user_books, fn($b) => ($b['status'] ?? '') === 'To Read'));
$favorites = count(array_filter($user_books, fn($b) => ($b['favorite'] ?? false) === true));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tracker</title>
   <link rel="stylesheet" href="styles.css"> 
</head>
<body>

    <div class="container">
        <aside class="sidebar">
            
            <div class="sidebar-label">STATUS</div>
            
            <div class="stat-card">
                <div class="stat-count"><?php echo $total; ?></div>
                <div class="stat-card-title">Total Books</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-count"><?php echo $read; ?></div>
                <div class="stat-card-title">Books Read</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-count"><?php echo $unread; ?></div>
                <div class="stat-card-title">To Read</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-count"><?php echo $favorites; ?></div>
                <div class="stat-card-title">Favorite Books</div>
            </div>
            <a href="logout.php" class="logout-link">Log Out</a>
        </aside>
        

         <main class="main-content">
            <section class="hero-section">
                <img src="manhwa.jpg" alt="Fantasy Book Cover" class="hero-image">
                <div class="hero-overlay"></div>
                
                <div class="hero-badge">
                    <span class="badge badge-imdb">Rating 4.5 ⭐</span>
                    <span class="badge badge-trending">Fantasy/Action</span>
                </div>

                <div class="hero-content">
                    <h1 class="hero-title">Solo Leveling</h1>
                    <p class="hero-description">
                        Ten years after “the Gate” opened between the real world and the monster realm, some humans awakened as Hunters. But not all Hunters are strong. I’m Sung Jin-Woo, an E-rank Hunter known as the “World’s Weakest,” risking my life in the lowest dungeons just to earn enough to survive. That changed when I entered a hidden high-difficulty dungeon and faced death—only to receive a mysterious power: a personal quest log only I can see. By completing quests and hunting monsters, I can level up endlessly. From the weakest Hunter… to the strongest S-rank.                    </p>
                    <div class="hero-buttons">
                        <button class="btn btn-primary">Read Now</button>
                        <button class="btn btn-secondary">⬇ Download</button>
                        <button class="btn btn-icon">⋯</button>
                    </div>
                    <div class="hero-logo">
                        <span style="font-size: 18px; font-weight: 600;">Manhwa/Manhua</span>
                    </div>
                </div>
            </section>

          <div class="carousel-header">
                <h2 class="carousel-title">MY COLLECTION</h2>
                <a href="#" class="add-book-btn" id="add-book-trigger">+ Add Book</a>
            </div>

            <div class="carousel-container-with-nav">
                <button id="prev-btn" class="nav-btn left-btn">◀</button>
                <div class="carousel" id="book-carousel">
                    <div class="group">
                        
                        <?php foreach ($user_books as $book): ?>
                        <div class="card book-card" data-book-id="<?php echo htmlspecialchars($book['id']); ?>">
                            <?php if ($book['favorite'] ?? false): ?>
                                <div class="favorite-badge">⭐</div>
                            <?php endif; ?>
                            <span class="card-title"><?php echo htmlspecialchars($book['title'] ?? 'ID: ' . $book['id']); ?></span>
                        </div>
                        <?php endforeach; ?>
                        
                    </div>
                </div>
                <button id="next-btn" class="nav-btn right-btn">▶</button>
            </div>
        </main>
    </div>

     <div id="book-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <div id="modal-body-content">
                </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>