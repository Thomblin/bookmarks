<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/15/14
 * Time: 10:43 PM
 */

namespace Bookmarks;

class Controller
{
    public function __construct(Storage $storage, View $view)
    {
        $this->storage = $storage;
        $this->view    = $view;
    }

    public function parseRequest($requestMethod, $values)
    {
        $requestCallback = ucfirst(strtolower($requestMethod)) . 'RequestAction';

        if ( method_exists($this, $requestCallback) ) {
            return call_user_func_array(array($this, $requestCallback), array('values' => $values));
        } else {
            throw new InvalidArgumentException("unkown request method '{$_SERVER['REQUEST_METHOD']}'");
        }
    }

    /**
     * @param array $values
     */
    private function getRequestAction(array $values)
    {
        return $this->view->showAll($this->storage);
    }

    /**
     * @param array $values
     */
    private function postRequestAction(array $values)
    {
        $html = "";

        if ('create group' === $values['action']) {
            $group = new \Bookmarks\Group();
            $group->title = $values['title'];

            $this->storage->getConfig()->addGroup($group);

            $html = $this->view->showGroup($group, true);
        } elseif ('create link' === $values['action']) {

            $group = $this->storage->getConfig()->getGroup($values['group']);

            $link = new \Bookmarks\Link();
            $link->title = $values['title'];
            $link->url = $values['url'];

            $group->addLink($link);

            $html = $this->view->showLink($link);
        } elseif ('create tag' === $values['action']) {

            $group = $this->storage->getConfig()->getGroup($values['group']);
            $link  = $group->getLink($values['link']);
            $link->addTag($values['text']);

            $html = $values['text'];

        } elseif ('search' === $values['action'] ) {
            $words = 0 < strlen($values['search'])
                ? $this->storage->getAllWords($values['search'])
                : array();

            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Content-type: application/json');

            $html = json_encode($words);
        } elseif ('show' === $values['action'] ) {
            $ids = 0 < strlen($values['search'])
                ? $this->storage->getConfig()->search($values['search'])
                : array();

            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Content-type: application/json');

            $html = json_encode($ids);
        }

        $this->storage->saveConfig($this->storage->getConfig());

        return $html;
    }
}