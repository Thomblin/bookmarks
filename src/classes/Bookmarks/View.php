<?php
/**
 * View is used to render templates with the Laravel Blade engine
 */

namespace Bookmarks;

use Philo\Blade\Blade;

class View
{
    /**
     * @var Blade
     */
    private $blade;

    public function __construct()
    {
        $views = realpath(__DIR__ . '/../../views');
        $cache = realpath(__DIR__ . '/../../../cache');

        $this->blade = new Blade($views, $cache);
    }

    /**
     * @param Storage $storage
     */
    public function showAll(Storage $storage)
    {
        return $this->blade->view()->make('pages.overview', array('storage' => $storage));
    }

    /**
     * @param Group $group
     */
    public function showGroup(Group $group, $expanded = false)
    {
        return $this->blade->view()->make('elements.group', array('group' => $group, 'expanded' => $expanded));
    }

    /**
     * @param Link $link
     */
    public function showLink(Link $link)
    {
        return $this->blade->view()->make('elements.link', array('link' => $link));
    }

    /**
     * send header
     *
     * @param string $string
     * @param bool $replace
     * @param int $http_response_code
     */
    public function header($string, $replace = null, $http_response_code = null)
    {
        header($string, $replace, $http_response_code);
    }
} 