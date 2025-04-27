<?php

define("CONTROLLER", __DIR__ . "/../lib/controller/");
define("MODEL", __DIR__ . "/../lib/model/");
define("VIEWS", __DIR__ . "/../lib/views/");

$path = "/";
if (isset($_SERVER["PATH_INFO"])) {
    $path = $_SERVER["PATH_INFO"];
}

require_once CONTROLLER . "router.php";
require_once CONTROLLER . "main.php";

$app = new Application();
$router = new Router($app);
$router->dispatch($path);
