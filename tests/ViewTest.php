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
            __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'links.yaml'
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
}
