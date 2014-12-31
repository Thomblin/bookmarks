<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bookmarks\Storage;
use Bookmarks\View;
use Bookmarks\Controller;

$storage    = new Storage('../config/links.yaml');
$view       = new View();
$controller = new Controller($storage, $view);

echo $controller->parseRequest($_SERVER['REQUEST_METHOD'], $_POST);