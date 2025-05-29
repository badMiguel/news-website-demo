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
        <div class="top--container">
            <!-- logo container placeholder for now... can be replaced -->
            <div class="logo--container"></div>
            <h1 class="website-title"><a href="/">Austro-Asian Times</a></h1>
            <div class="login--container">
                <?php
                session_start();
                if (isset($_SESSION['username'])) {
                    if ($_SESSION['privilege'] === EDITOR) {
                        echo "<p><a href='/admin'>Admin</a></p>";
                    }
                    if ($_SESSION['privilege'] >= JOURNALIST) {
                        echo "<p><a href='/news/create'>Create</a></p>";
                    }
                    echo "<p><a href='/logout'>Logout</a></p>";
                } else {
                    echo "<p><a href='/login'>Login</a></p>";
                }
                session_write_close();
                ?>
            </div>
        </div>
        <div class="nav--container">
            <nav class="nav--alignment">
                <ul class="nav--list">
                    <li><a href="/">Home</a></li>
                    <li><a href="/world">World</a></li>
                    <li><a href="/politics">Politics</a></li>
                    <li><a href="/business">Business</a></li>
                    <li><a href="/technology">Technology</a></li>
                    <li><a href="/entertainment">Entertainment</a></li>
                    <li><a href="/sports">Sports</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main--container">
        <?php if (isset($isHome) && $isHome): ?>
            <div class="home--container">
                <?php require_once $viewPath; ?>
            </div>
        <?php else: ?>
            <div class="main--spacing">
                <?php require_once $viewPath; ?>
            </div>
        <?php endif ?>
    </main>
</body>

</html>
