<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title ?? ucfirst($viewName) ?></title>
    <link href="/../css/styles.css" rel="stylesheet">
</head>

<body>
<header>
        <h1>Austro-Asian Times</h1>
        <nav>
            <a href="/">Home</a>
            <?php session_start(); ?>
            <?php if (isset($_SESSION['username'])): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="/logout">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <?php require_once $viewPath; ?>
    </main>
</body>
</html>
