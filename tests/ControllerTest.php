<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/31/14
 * Time: 12:22 AM
 */

class ControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Bookmarks\View|PHPUnit_Framework_MockObject_MockObject
     */
    private $view;
    /**
     * @var \Bookmarks\Storage|PHPUnit_Framework_MockObject_MockObject
     */
    private $storage;
    /**
     * @var \Bookmarks\Config|PHPUnit_Framework_MockObject_MockObject
     */
    private $config;
    /**
     * @var \Bookmarks\Controller
     */
    private $controller;

    public function setUp()
    {
        $this->view = $this->getMock(
            '\Bookmarks\View', array('showAll', 'header', 'showGroup', 'showLink'), array(), '', false
        );

        $this->config = $this->getMock(
            '\Bookmarks\Config', array('addGroup', 'getGroup', 'getGroups', 'search'), array(),  '', false
        );

        $this->storage = $this->getMock(
            '\Bookmarks\Storage', array('getConfig', 'saveConfig'), array(),  '', false
        );
        $this->storage->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($this->config));

        $this->controller = new \Bookmarks\Controller($this->storage, $this->view);
    }

    /**
     * @test
     */
    public function parseRequestCallsViewShowAllIfIsGetRequest()
    {
        $this->view->expects($this->once())
            ->method('showAll')
            ->with($this->equalTo($this->storage))
            ->will($this->returnValue('html response'));

        $this->assertEquals(
            'html response',
            $this->controller->parseRequest('GET', array())
        );
    }

    /**
     * @test
     */
    public function parseRequestReturnsErrorIfRequestMethodIsUnkown()
    {

        $this->view->expects($this->at(0))
            ->method('header')
            ->with($this->equalTo('HTTP/1.1 409 Conflict'));
        $this->view->expects($this->at(1))
            ->method('header')
            ->with($this->equalTo('Content-Type: application/json; charset=UTF-8'));

        $this->assertEquals(
            json_encode(array('message' => "unkown request method 'PUT'")),
            $this->controller->parseRequest('PUT', array())
        );
    }

    /**
     * @test
     */
    public function postRequestReturnsEmptyString()
    {
        $this->assertEquals(
            '',
            $this->controller->parseRequest('POST', array())
        );
    }

    /**
     * @test
     */
    public function postRequestCreateGroup()
    {
        $group = new \Bookmarks\Group();
        $group->title = 'my new group';

        $this->config->expects($this->once())
            ->method('addGroup')
            ->with($this->equalTo($group));

        $this->view->expects($this->once())
            ->method('showGroup')
            ->with($this->equalTo($group), $this->equalTo(true))
            ->will($this->returnValue('fancy group html'));

        $this->storage->expects($this->once())
            ->method('saveConfig')
            ->with($this->config);

        $this->assertEquals(
            'fancy group html',
            $this->controller->parseRequest('POST', array(
                'action' => 'createGroup',
                'title' => 'my new group'
            ))
        );
    }

    /**
     * @test
     */
    public function postRequestCreateLink()
    {
        $link = new \Bookmarks\Link();
        $link->url = '/test.html';
        $link->title = 'my new group';

        $group = $this->getMock('\Bookmarks\Group', array('addLink'));
        $group->expects($this->once())
            ->method('addLink')
            ->with($this->equalTo($link));

        $this->config->expects($this->once())
            ->method('getGroup')
            ->with($this->equalTo('groupId'))
            ->will($this->returnValue($group));

        $this->view->expects($this->once())
            ->method('showLink')
            ->with($this->equalTo($link))
            ->will($this->returnValue('fancy link html'));

        $this->storage->expects($this->once())
            ->method('saveConfig')
            ->with($this->config);

        $this->assertEquals(
            'fancy link html',
            $this->controller->parseRequest('POST', array(
                'action' => 'createLink',
                'group' => 'groupId',
                'url' => '/test.html',
                'title' => 'my new group'
            ))
        );
    }

    /**
     * @test
     */
    public function postRequestCreateTag()
    {
        $link = $this->getMock('\Bookmarks\Link', array('addTag'));
        $link->url = '/test.html';
        $link->title = 'my new group';

        $group = $this->getMock('\Bookmarks\Group', array('getLink'));
        $group->expects($this->once())
            ->method('getLink')
            ->with($this->equalTo('linkId'))
            ->will($this->returnValue($link));

        $this->config->expects($this->once())
            ->method('getGroup')
            ->with($this->equalTo('groupId'))
            ->will($this->returnValue($group));

        $link->expects($this->once())
            ->method('addTag')
            ->with($this->equalTo('my new tag'));

        $this->storage->expects($this->once())
            ->method('saveConfig')
            ->with($this->config);

        $this->assertEquals(
            'my new tag',
            $this->controller->parseRequest('POST', array(
                'action' => 'createTag',
                'group' => 'groupId',
                'link' => 'linkId',
                'text' => 'my new tag'
            ))
        );
    }

    /**
     * @test
     */
    public function postRequestSearch()
    {
        $group = new \Bookmarks\Group();
        $group->title = 'search engine';

        $this->config->expects($this->once())
            ->method('getGroups')
            ->will($this->returnValue(array($group)));

        $this->view->expects($this->at(0))
            ->method('header')
            ->with($this->equalTo('Cache-Control: no-cache, must-revalidate'));
        $this->view->expects($this->at(1))
            ->method('header')
            ->with($this->equalTo('Expires: Mon, 26 Jul 1997 05:00:00 GMT'));
        $this->view->expects($this->at(2))
            ->method('header')
            ->with($this->equalTo('Content-type: application/json'));

        $this->assertEquals(
            json_encode(array('search engine')),
            $this->controller->parseRequest('POST', array(
                'action' => 'search',
                'search' => 'engine',
            ))
        );
    }

    /**
     * @test
     */
    public function postRequestSearchWithEmptyStringReturnEmptyArray()
    {
        $group = new \Bookmarks\Group();
        $group->title = 'search engine';

        $this->config->expects($this->never())
            ->method('getGroups');

        $this->assertEquals(
            json_encode(array()),
            $this->controller->parseRequest('POST', array(
                'action' => 'search',
                'search' => '',
            ))
        );
    }

    /**
     * @test
     */
    public function postRequestShow()
    {
        $group = new \Bookmarks\Group();
        $group->title = 'search engine';

        $this->config->expects($this->once())
            ->method('getGroups')
            ->will($this->returnValue(array($group)));

        $this->view->expects($this->at(0))
            ->method('header')
            ->with($this->equalTo('Cache-Control: no-cache, must-revalidate'));
        $this->view->expects($this->at(1))
            ->method('header')
            ->with($this->equalTo('Expires: Mon, 26 Jul 1997 05:00:00 GMT'));
        $this->view->expects($this->at(2))
            ->method('header')
            ->with($this->equalTo('Content-type: application/json'));

        $this->assertEquals(
            json_encode(array($group->getId())),
            $this->controller->parseRequest('POST', array(
                'action' => 'show',
                'search' => 'engine',
            ))
        );
    }

    /**
     * @test
     */
    public function postRequestShowWithEmptyStringReturnEmptyArray()
    {
        $this->config->expects($this->never())
            ->method('search');

        $this->assertEquals(
            json_encode(array()),
            $this->controller->parseRequest('POST', array(
                'action' => 'show',
                'search' => '',
            ))
        );
    }
}
