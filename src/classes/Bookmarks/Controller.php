<?php
/**
 * Controller is used to handle all GET and POST requests
 */

namespace Bookmarks;

class Controller
{
    /**
     * @var Storage
     */
    private $storage;
    /**
     * @var View
     */
    private $view;
    /**
     * @var Search
     */
    private $search;
    /**
     * @var string
     */
    private $baseDir;

    /**
     * @param Storage $storage
     * @param View $view
     */
    public function __construct(Storage $storage, View $view)
    {
        $this->storage = $storage;
        $this->view    = $view;
        $this->baseDir = realpath(__DIR__ . '/../../..');
    }

    /**
     * @param string $requestMethod
     * @param array $values
     *
     * @return string
     */
    public function parseRequest($requestMethod, $values)
    {
        try {
            $requestCallback = ucfirst(strtolower($requestMethod)) . 'RequestAction';

            if ( method_exists($this, $requestCallback) ) {
                return call_user_func_array(array($this, $requestCallback), array('values' => $values));
            } else {
                throw new \InvalidArgumentException("unkown request method '{$requestMethod}'");
            }

        } catch ( \InvalidArgumentException $e ) {
            $this->view->header('HTTP/1.1 409 Conflict');
            $this->view->header('Content-Type: application/json; charset=UTF-8');

            return json_encode(array('message' => $e->getMessage()));
        }
    }

    /**
     * @param Environment $env
     */
    public function injectEnvironment(Environment $env)
    {
        $this->getSearch()->injectEnvironment($env);
        $this->view->injectEnvironment($env);
    }

    /**
     * @param array $values
     */
    private function getRequestAction(array $values)
    {
        if ( isset($values['file']) ) {
            return $this->getPublicFileContents($values['file']);
        } else {
            return $this->view->showAll($this->storage);
        }
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function getPublicFileContents($filename)
    {
        $path = realpath($this->getBaseDir() . '/public/' . $filename);
        if (is_file($path)) {

            if ('graphics' === substr($filename, 0, 8)) {
                $this->view->header("Content-Type: image/png");
                $this->view->header("Content-Length: " . filesize($path));
            }

            return file_get_contents($path);
        }

        return '';
    }

    public function getBaseDir()
    {
        return $this->baseDir;
    }

    /**
     * @param array $values
     */
    private function postRequestAction(array $values)
    {
        $html = "";

        if ( isset($values['action']) && is_string($values['action']) ) {
            $requestCallback = 'postRequest' . ucfirst(strtolower($values['action']));

            if (method_exists($this, $requestCallback)) {
                $html = call_user_func_array(array($this, $requestCallback), array('values' => $values));
                $this->storage->saveConfig($this->storage->getConfig());
            }
        }

        return $html;
    }

    /**
     * @param array $values
     * @return mixed
     */
    private function postRequestCreateGroup(array $values)
    {
        $group = new \Bookmarks\Group();
        $group->title = $values['title'];

        $this->storage->getConfig()->addGroup($group);

        return $this->view->showGroup($group, true);
    }

    /**
     * @param array $values
     * @return mixed
     */
    private function postRequestCreateLink(array $values)
    {
        $group = $this->storage->getConfig()->getGroup($values['group']);

        $link = new \Bookmarks\Link();
        $link->title = $values['title'];
        $link->url = $values['url'];

        $group->addLink($link);

        return $this->view->showLink($link);
    }

    /**
     * @param array $values
     * @return mixed
     */
    private function postRequestCreateTag(array $values)
    {
        $group = $this->storage->getConfig()->getGroup($values['group']);
        $link = $group->getLink($values['link']);
        $link->addTag($values['text']);

        return $this->view->showTag($values['text']);
    }

    /**
     * @param array $values
     * @return string
     */
    private function postRequestSearch(array $values)
    {
        $words = 0 < strlen($values['search'])
            ? $this->getSearch()->findWords($values['search'])
            : array();

        $this->view->header('Cache-Control: no-cache, must-revalidate');
        $this->view->header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        $this->view->header('Content-type: application/json');

        return json_encode($words);
    }

    private function getSearch()
    {
        if ( null === $this->search ) {
            $this->search = new Search($this->storage->getConfig());
        }

        return $this->search;
    }

    /**
     * @param array $values
     * @return string
     */
    private function postRequestShow(array $values)
    {
        $ids = 0 < strlen($values['search'])
            ? $this->getSearch()->findIds($values['search'])
            : array();

        $this->view->header('Cache-Control: no-cache, must-revalidate');
        $this->view->header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        $this->view->header('Content-type: application/json');

        return json_encode($ids);
    }
}