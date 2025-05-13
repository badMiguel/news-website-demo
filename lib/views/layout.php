<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title ?? ucfirst($viewName) ?></title>
    <link href="/../css/styles.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap');
    </style>
</head>

<body>
    <header>
        <h1>Austro-Asian Times</h1>
        <nav>
            <a href="/">Home</a>
            <?php
            session_start();
            if (isset($_SESSION['username'])) {
                echo "<a href='/logout'>Logout</a>";
            } else {
                echo "<a href='/login'>Login</a>";
            }
            session_write_close();
            ?>
        </nav>
        <?php session_start(); ?>
        <?php if (isset($_SESSION['username'])): ?>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <?php endif ?>
        <?php session_write_close(); ?>
    </header>
    <main>
        <?php require_once $viewPath; ?>
    </main>
</body>

</html>
