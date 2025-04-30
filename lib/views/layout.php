<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title ?? ucfirst($viewName) ?></title>
    <link href="/../css/styles.css" rel="stylesheet">
</head>

<body>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/login">Login?</a></li>
        </ul>
    </nav>
    <?php require $viewPath ?>
</body>

</html>
