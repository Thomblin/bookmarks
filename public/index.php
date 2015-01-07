<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bookmarks\Storage;
use Bookmarks\View;
use Bookmarks\Controller;

$server  = filter_input_array(INPUT_SERVER, FILTER_SANITIZE_STRING);
$get     = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$post    = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$request = array_merge(
    is_null($get) ? array() : $get,
    is_null($post) ? array() : $post
);

$env = new \Bookmarks\Environment();
$env->inject('subdomain', 'dev'); // replace all {subdomain} parts with dev

$storage    = new Storage('../config/links.json');
$view       = new View('../cache');
$controller = new Controller($storage, $view);
$controller->injectEnvironment($env);

echo $controller->parseRequest($server['REQUEST_METHOD'], $request);