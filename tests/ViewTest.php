<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/30/14
 * Time: 10:22 PM
 */

class ViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function showAllRendersOverview()
    {
        $storage = new \Bookmarks\Storage(
            __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'links.json'
        );

        $view = new \Bookmarks\View();
        $actual = (string) $view->showAll($storage);

        $this->assertStringContains(
            '<title>Bookmarks</title>',
            $actual,
            'html should contain title'
        );
        $this->assertStringContains(
            '<div class="container">',
            $actual,
            'html should contain div container'
        );
        $this->assertStringContains(
            '<legend>my new group</legend>',
            $actual,
            'html should contain group'
        );
        $this->assertStringContains(
            'BookmarkController',
            $actual,
            'html should contain js BookmarkController'
        );
    }

    private function assertStringContains($needle, $haystack, $message)
    {
        $this->assertTrue(false !== strpos($haystack, $needle), $message);
    }

    /**
     * @test
     */
    public function showGroupRendersGroup()
    {
        $group = new \Bookmarks\Group();
        $group->title = 'test';

        $link = new \Bookmarks\Link();
        $link->title = 'my link';
        $link->url = 'http://abc.de';
        $group->addLink($link);

        $view = new \Bookmarks\View();
        $actual = (string) $view->showGroup($group);

        $this->assertStringContains(
            'fieldset id="'.$group->getId().'"',
            $actual,
            'html should contain fieldset'
        );
        $this->assertStringContains(
            'hidden',
            $actual,
            'html should be hidden'
        );
        $this->assertStringContains(
            '<div class="content">',
            $actual,
            'html should contain div content'
        );
        $this->assertStringContains(
            'http://abc.de',
            $actual,
            'html should contain link'
        );
        $this->assertStringContains(
            'add link',
            $actual,
            'html should contain "add link" element'
        );
    }

    /**
     * @test
     */
    public function showLinkRendersLink()
    {
        $link = new \Bookmarks\Link();
        $link->title = 'my link';
        $link->url = 'http://abc.de';
        $link->tags = array('tag1', 'tag2');

        $view = new \Bookmarks\View();
        $actual = (string) $view->showLink($link);

        $this->assertStringContains(
            '<p id="'.$link->getId().'" class="bookmark">',
            $actual,
            'html should contain p tag with link id'
        );
        $this->assertStringContains(
            '<a href="http://abc.de"',
            $actual,
            'html should contain link'
        );
        $this->assertStringContains(
            '>my link</a>',
            $actual,
            'html should contain title'
        );
        $this->assertStringContains(
            'http://abc.de',
            $actual,
            'html should contain link'
        );
        $this->assertStringContains(
            '<span>tag1</span>',
            $actual,
            'html should contain tags'
        );
        $this->assertStringContains(
            'add tag',
            $actual,
            'html should contain "add tag" element'
        );
    }

    /**
     * @test
     */
    public function showAllReplacesUserVariables()
    {
        $storage = new \Bookmarks\Storage(
            __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'links.json'
        );

        $group = new \Bookmarks\Group();
        $group->title = "my new {domain}";

        $storage->getConfig()->addGroup($group);

        $env = new \Bookmarks\Environment();
        $env->inject('domain', 'abc');

        $view = new \Bookmarks\View();
        $view->injectEnvironment($env);

        $actual = (string) $view->showAll($storage);

        $this->assertStringContains(
            'my new abc',
            $actual,
            'html should replace {domain} and contain "my new abc"'
        );
    }

    /**
     * @test
     */
    public function showGroupReplacesUserVariables()
    {
        $group = new \Bookmarks\Group();
        $group->title = 'any {domain}';

        $link = new \Bookmarks\Link();
        $link->title = 'this is {title}';
        $link->url = 'http://{domain}.de';
        $group->addLink($link);

        $env = new \Bookmarks\Environment();
        $env->inject('domain', 'abc');
        $env->inject('title', 'my group');

        $view = new \Bookmarks\View();
        $view->injectEnvironment($env);

        $actual = (string) $view->showGroup($group);

        $this->assertStringContains(
            'any abc',
            $actual,
            'html should replace {domain} and contain "any abc" title'
        );
        $this->assertStringContains(
            'this is my group',
            $actual,
            'html should replace {title} and contain tag "this is my group"'
        );
        $this->assertStringContains(
            'http://abc.de',
            $actual,
            'html should replace {domain} and contain url "http://abc.de"'
        );
    }

    /**
     * @test
     */
    public function showLinkReplacesUserVariables()
    {
        $link = new \Bookmarks\Link();
        $link->title = 'my {domain}';
        $link->url = 'http://{domain}.de';
        $link->tags = array('tag {domain}', 'tag2');

        $env = new \Bookmarks\Environment();
        $env->inject('domain', 'abc');

        $view = new \Bookmarks\View();
        $view->injectEnvironment($env);

        $actual = (string) $view->showLink($link);

        $this->assertStringContains(
            'my abc',
            $actual,
            'html should replace {domain} and contain "my abc" title'
        );
        $this->assertStringContains(
            'my abc',
            $actual,
            'html should replace {domain} and contain url "http://abc.de"'
        );
        $this->assertStringContains(
            'my abc',
            $actual,
            'html should replace {domain} and contain tag "tag abc"'
        );
    }
    /**
     * @test
     */
    public function showAllRendersOverviewWithDifferentCacheDir()
    {
        $configDir = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
        $cacheDir = $configDir . 'cache';

        $this->removeDir($cacheDir);

        $storage = new \Bookmarks\Storage($configDir . 'links.json');

        $view = new \Bookmarks\View($cacheDir);
        $actual = (string) $view->showAll($storage);

        $this->assertStringContains(
            '<title>Bookmarks</title>',
            $actual,
            'html should contain title'
        );
        $this->assertStringContains(
            '<div class="container">',
            $actual,
            'html should contain div container'
        );
        $this->assertStringContains(
            '<legend>my new group</legend>',
            $actual,
            'html should contain group'
        );
        $this->assertStringContains(
            'BookmarkController',
            $actual,
            'html should contain js BookmarkController'
        );

        $this->assertTrue(is_dir($cacheDir), "cache dir should have been created");
        $this->assertNotEmpty(glob($cacheDir . DIRECTORY_SEPARATOR . '*'), "cache dir should contain some files");
    }

    /**
     * @param $cacheDir
     */
    private function removeDir($cacheDir)
    {
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . DIRECTORY_SEPARATOR . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($cacheDir);
        }
    }
}
