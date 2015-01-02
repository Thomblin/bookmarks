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
    /**
     * @var Environment
     */
    private $environment;

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
        $groupHtml = '';
        foreach($storage->getConfig()->getGroups() as $group) {
            $groupHtml .= $this->showGroup($group);
        }

        return $this->blade->view()->make(
            'pages.overview',
            array(
                'groupHtml' => $groupHtml
            )
        );
    }

    /**
     * @param Group $group
     */
    public function showGroup(Group $group, $expanded = false)
    {
        return $this->replaceInjectedVars($this->blade->view()->make(
            'elements.group',
            array(
                'group' => $group,
                'expanded' => $expanded
            )
        ));
    }

    /**
     * @param Link $link
     */
    public function showLink(Link $link)
    {
        return $this->replaceInjectedVars($this->blade->view()->make(
            'elements.link',
            array(
                'link' => $link
            )
        ));
    }

    /**
     * @param string $text
     */
    public function showTag($text)
    {
        return $this->replaceInjectedVars($text);
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

    /**
     * @param Environment $env
     */
    public function injectEnvironment(Environment $env)
    {
        $this->environment = $env;
    }

    /**
     * @param Environment $env
     */
    private function replaceInjectedVars($text)
    {
        if ( null !== $this->environment ) {
            $text = $this->environment->replaceInjectedVars($text);
        }

        return $text;
    }
} 