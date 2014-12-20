<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bookmarks\Storage;
use Bookmarks\View;
use Bookmarks\Controller;

try {

    $storage    = new Storage('../config/links.yaml');
    $view       = new View();
    $controller = new Controller($storage, $view);

    echo $controller->parseRequest($_SERVER['REQUEST_METHOD'], $_POST);

} catch ( InvalidArgumentException $e ) {
    header('HTTP/1.1 409 Conflict');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array('message' => $e->getMessage())));
}