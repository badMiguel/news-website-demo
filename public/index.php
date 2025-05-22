<?php

declare(strict_types=1);

define("CONTROLLER", __DIR__ . "/../lib/controller/");
define("MODEL", __DIR__ . "/../lib/model/");
define("VIEWS", __DIR__ . "/../lib/views/");

define("USER", 0);
define("JOURNALIST", 1);
define("EDITOR", 2);

session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$path = "/";
if (isset($_SERVER["PATH_INFO"])) {
    $path = $_SERVER["PATH_INFO"];
}
session_write_close();

require_once CONTROLLER . "router.php";
require_once CONTROLLER . "main.php";
require_once CONTROLLER . "paginator.php";
require_once MODEL . "db.php";

$model = new Model();
$paginator = new Paginator($model);
$app = new Application($model, $paginator);
$router = new Router($app);
$router->dispatch($path);
