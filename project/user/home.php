<?php 
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: http://localhost/project/home/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header>
        <nav>
            <a href="proflie.php" class="profile-link"><i class="fas fa-user"></i> Profile</a>
            <a href="">Home</a>
            <a href="contact.html">Contacts</a>
            <a href="cinfo/index.html">Info</a>
             <!-- Profile Link -->
            <a href="http://localhost/project/home/login.php" class="btn btn-danger btn-block">Logout</a>
        </nav>
    </header>

    <!-- carousel -->
    <div class="carousel">
        <!-- list item -->
        <div class="list">
            <div class="item">
                <img src="imgs/home-1.jpg">
                <div class="content">
                    <div class="title">MAGIC</div>
                    <div class="topic">PAINTS</div>
                    <div class="des">WE DO ALL PAINTING AND DESIGN WORKS!</div>
                    <div class="buttons">
                        <button><a href="book.php">BOOK NOW</a></button>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="imgs/color4.jpg">
                <div class="content">
                    <div class="title">MAGIC</div>
                    <div class="topic">PAINTS</div>
                    <div class="des">WE DO ALL PAINTING AND DESIGN WORKS!</div>
                    <div class="buttons">
                        <button><a href="book.php">BOOK NOW</a></button>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="imgs/paint-4.jpg">
                <div class="content">
                    <div class="title">MAGIC</div>
                    <div class="topic">PAINTS</div>
                    <div class="des">WE DO ALL PAINTING AND DESIGN WORKS!</div>
                    <div class="buttons">
                        <button><a href="book.php">BOOK NOW</a></button>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="imgs/h3.jpg">
                <div class="content">
                    <div class="title">MAGIC</div>
                    <div class="topic">PAINTS</div>
                    <div class="des">WE DO ALL PAINTING AND DESIGN WORKS!</div>
                    <div class="buttons">
                        <button><a href="book.php">BOOK NOW</a></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="thumbnail">
            <div class="item"><img src="imgs/home-1.jpg"></div>
            <div class="item"><img src="imgs/color4.jpg"></div>
            <div class="item"><img src="imgs/paint-4.jpg"></div>
            <div class="item"><img src="imgs/h3.jpg"></div>
        </div>

        <div class="arrows">
            <button id="prev"><</button>
            <button id="next">></button>
        </div>

        <div class="time"></div>
    </div>

    <script src="app.js"></script>
</body>
</html>
