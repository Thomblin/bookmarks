<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bookmarks\Storage;
use Bookmarks\View;
use Bookmarks\Controller;

$env = new \Bookmarks\Environment();
$env->inject('subdomain', 'dev'); // replace all {subdomain} parts with dev

$storage    = new Storage('../config/links.json');
$view       = new View();
$controller = new Controller($storage, $view);
$controller->injectEnvironment($env);

echo $controller->parseRequest($_SERVER['REQUEST_METHOD'], $_POST);