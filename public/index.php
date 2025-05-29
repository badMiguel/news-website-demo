<?php

declare(strict_types=1);

define("CONTROLLER", __DIR__ . "/../lib/controller/");
define("MODEL", __DIR__ . "/../lib/model/");
define("VIEWS", __DIR__ . "/../lib/views/");
define("IMAGE_DIR", __DIR__ . "/../public/images/");

define("USER", 0);
define("JOURNALIST", 1);
define("EDITOR", 2);

$path = "/";
if (isset($_SERVER["PATH_INFO"])) {
    $path = $_SERVER["PATH_INFO"];
}

require_once CONTROLLER . "router.php";
require_once CONTROLLER . "main.php";
require_once CONTROLLER . "paginator.php";
require_once CONTROLLER . "csrf.php";
require_once MODEL . "db.php";

$model = new Model();
$csrf = new CSRF();
$paginator = new Paginator($model);
$app = new Application($model, $paginator, $csrf);
$router = new Router($app);
$router->dispatch($path);
