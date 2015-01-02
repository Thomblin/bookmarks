<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/30/14
 * Time: 6:11 PM
 */

use Bookmarks\Group;
use TestHelper\Link;

class GroupTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Group
     */
    private $group;

    public function setUp()
    {
        $this->group = new Group();
    }

    /**
     * @test
     */
    public function addAndGetLinks()
    {
        $this->assertEquals(array(), $this->group->getLinks());

        $l1 = new Link();
        $this->group->addLink($l1);

        $this->assertEquals(
            $l1,
            $this->group->getLink($l1->getId())
        );
        $this->assertEquals(
            array($l1->getId() => $l1),
            $this->group->getLinks()
        );
    }

    /**
     * @test
     */
    public function addLinkCallsIsValid()
    {
        $link = $this->getMock('\Bookmarks\Link', array('assertValid'));
        $link->expects($this->once())
            ->method('assertValid');

        $this->group->addLink($link);
    }

    /**
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage link [i do not exist] not found
     */
    public function getLinkThrowsExceptionIfLinkIsNotSet()
    {
        $this->group->getLink('i do not exist');
    }

    /**
     * @test
     */
    public function getIdReturnsSha1OfTitle()
    {
        $expected = sha1('my title');
        $this->group->title = 'my title';

        $this->assertEquals(
            $expected,
            $this->group->getId()
        );
    }

    /**
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage group title may not be empty
     */
    public function assertValidFailsIfTitleIsEmpty()
    {
        $this->group->assertValid();
    }

    /**
     * @test
     */
    public function assertValidSucceedsIfTitleIsNotEmpty()
    {
        $this->group->title = 'foo';
        $this->group->assertValid();
    }

    /**
     * @test
     */
    public function toArrayReturnsTitleAndLinks()
    {
        $expected = array(
            'title' => 'my group',
            'links' => array()
        );

        $this->group->title = 'my group';

        $this->assertEquals(
            $expected,
            $this->group->toArray()
        );

        $l1 = new Link();
        $this->group->addLink($l1);


        $expected = array(
            'title' => 'my group',
            'links' => array($l1->toArray())
        );

        $this->assertEquals(
            $expected,
            $this->group->toArray()
        );
    }

    /**
     * @test
     */
    public function fromArrayCreatesGroupWithLinks()
    {
        $l1 = new \Bookmarks\Link();
        $l1->title = 'test';
        $l1->url = 'http://abc.de';

        $data = array(
            'title' => 'my group',
            'links' => array(
                $l1->toArray()
            )
        );

        $this->group->fromArray($data);

        $this->assertEquals(
            'my group',
            $this->group->title
        );

        $this->assertTrue($this->group->issetLink($l1->getId()));
    }
}
