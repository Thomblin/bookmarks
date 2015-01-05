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

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir = null)
    {
        $this->createBladeInstance($cacheDir);
        $this->addPublicFileMatcher();
    }

    /**
     * @param $cacheDir
     */
    private function createBladeInstance($cacheDir)
    {
        $this->blade = new Blade(
            realpath(__DIR__ . '/../../views'),
            $this->getPreparedCacheDir($cacheDir)
        );
    }

    /**
     * @param string $cacheDir
     */
    private function getPreparedCacheDir($cacheDir)
    {
        if (null === $cacheDir) {
            $cacheDir = __DIR__ . '/../../../cache';
        } elseif (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        return realpath($cacheDir);
    }

    /**
     * add the @publicFile('/file/in/public/folder') syntax to Blade
     * to allow inline usage of css and js files
     */
    private function addPublicFileMatcher()
    {
        $this->blade->getCompiler()->extend(function ($view, $compiler) {
            $pattern = "/(?<!\w)(\s*)@publicFile\('(\s*.*)'\)/";

            return preg_replace(
                $pattern,
                '<?php include "' . realpath(__DIR__ . '/../../../public/') . '/$2"; ?>',
                $view
            );
        });
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
     * @param Environment $env
     */
    private function replaceInjectedVars($text)
    {
        if ( null !== $this->environment ) {
            $text = $this->environment->replaceInjectedVars($text);
        }

        return $text;
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
} 