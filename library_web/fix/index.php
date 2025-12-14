<?php
session_start();

// If the user is logged in, redirect them to the dashboard.
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// If the user is not logged in, display the styled landing page.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Book Collection</title>
  <style>
    /* COMPILED AND FIXED UI STYLES */

body {
    margin: 0;
    padding: 0;
    /* Retained original background as fallback */
    background: #cfe1d9; 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    height: 100vh;
    width: 100vw;
    overflow: hidden; /* Prevents unwanted scrollbars */
}

.container {
    width: 100vw;
    height: 100vh;
    /* IMPROVEMENT: Added a dark overlay to the background image for better text contrast */
    background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.4)), url('book.jpg'); 
    background-size: cover;
    background-position: center;
    border-radius: 0;
    overflow: hidden;
    position: relative;
    display: flex;
    color: white;
}

/* Sidebar */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 10;
    height: 100vh;
    width: 70px;
    /* IMPROVEMENT: Darker, more prominent blur effect */
    background: rgba(15, 15, 15, 0.6); 
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 0; /* Changed to 0 as logo-container handles top padding */
    gap: 35px; /* Increased space between icons */
}

/* NEW: Logo Container Styling */
.logo-container {
    width: 100%;
    padding: 20px 0; /* Vertical padding for the logo */
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 20px; /* Space between logo and first icon */
}

.logo-container img {
    width: 40px; /* Logo size */
    height: 40px;
    opacity: 1;
    /* OPTIONAL: You might want to use a specific logo image here instead of a generic icon */
}


.main {
    flex: 1;
    /* FIX: Pushes the main content area past the fixed sidebar width (70px) */
    margin-left: 70px; 
    
    padding: 40px;
    position: relative;
    /* FIX: Center the hero content vertically for a landing page feel */
    display: flex;
    flex-direction: column;
    justify-content: center; 
    align-items: flex-start;
    /* Removed redundant background image setting */
}

.title {
    font-size: 150px; /* Larger, more dominant title */
    font-weight: 700; 
    margin-top: 0; 
    line-height: 1.1;
    /* IMPROVEMENT: Adds depth for readability over the background image */
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7); 
}

.subtitle {
    margin-top: 15px;
    font-size: 24px; /* Larger subtitle */
    opacity: 1;
    font-weight: 400;
    max-width: 600px; /* Constrain width for better reading flow */
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

.platforms {
    margin-top: 40px; 
    display: flex;
    gap: 20px; /* Increased gap between buttons */
}

.btn {
    /* IMPROVEMENT: More subtle background for platform buttons */
    background: rgba(255, 255, 255, 0.1);
    padding: 12px 25px;
    border-radius: 50px; /* PILL SHAPE */
    border: 1px solid rgba(255, 255, 255, 0.5);
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    color: white; 
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: background 0.3s ease, border-color 0.3s ease;
}

.btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: white;
}

/* Removed .login-btn styling as it was not used in the HTML buttons, 
   but keeping it here in case you add it back for the login link */
.login-btn {
    position: absolute;
    top: 30px; 
    right: 30px; 
    padding: 8px 18px;
    /* IMPROVEMENT: Distinct primary color for the Call to Action (Login) */
    background: #4a90e2; 
    border-radius: 50px; /* PILL SHAPE */
    border: none;
    font-weight: 600;
    color: white; 
    text-decoration: none;
    transition: background 0.2s ease, box-shadow 0.2s ease;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.login-btn:hover {
    background: #357bd8;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
}
  </style>
</head>
<body>

<div class="container">
  <div class="sidebar">
    <div class="logo-container">
        <img src="Logo.png" alt="Logo" />
    </div>

  </div>

  <div class="main">

    <div class="title">LIBRO</div>
    <div class="subtitle">is your personal collection library — a little space where all your books, stories, and random reads come together.
Add your books, organize your shelves, and keep track of everything you love in one place.
Simple, neat, and made for every book hoarder at heart.</div>

    <div class="platforms">
      <a href="register.php" class="btn">Register</a>
      <a href="login.php" class="btn">Login</a>
    </div>
  </div>
</div>

</body>
</html>