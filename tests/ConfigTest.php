<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/21/14
 * Time: 5:26 PM
 */

use \Bookmarks\Config;
use TestHelper\Group;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    private $config;

    public function __construct()
    {
        $this->config = new \Bookmarks\Config();
    }

    /**
     * @test
     */
    public function IssetAddAndGetGroups()
    {
        $group = new Group();

        $this->assertFalse($this->config->issetGroup($group->getId()));

        $this->config->addGroup($group);

        $this->assertTrue($this->config->issetGroup($group->getId()));

        $this->assertEquals($group, $this->config->getGroup($group->getId()));
    }

    /**
     * @test
     */
    public function addGroupTestsIfgroupIsValid()
    {
        $group = $this->getMock('Bookmarks\Group', array('assertValid'));
        $group->expects($this->once())
            ->method('assertValid');

        $this->config->addGroup($group);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage group [foo] not found
     */
    public function getGroupFailsIfGroupIsNotSet()
    {
        $this->config->getGroup('foo');
    }

    /**
     * @test
     */
    public function getGroupsReturnAllAddedGroups()
    {
        $this->assertEmpty($this->config->getGroups());

        $g1 = new Group();

        $this->config->addGroup($g1);

        $this->assertEquals(
            [$g1->getId() => $g1],
            $this->config->getGroups()
        );

        $g2 = new Group();
        $g2->id = 'id2';

        $this->config->addGroup($g2);

        $this->assertEquals(
            [$g1->getId() => $g1, $g2->getId() => $g2],
            $this->config->getGroups()
        );
    }

    /**
     * @test
     */
    public function toArrayReturnsArrayOfGroups()
    {
        $this->assertEquals(
            ['groups' => array()],
            $this->config->toArray()
        );

        $g1 = new Group();
        $g2 = new Group();
        $g2->id = 'id2';

        $this->config->addGroup($g1);
        $this->config->addGroup($g2);

        $this->assertEquals(
            ['groups' => array($g1->toArray(), $g2->toArray())],
            $this->config->toArray()
        );
    }

    /**
     * @test
     */
    public function fromArrayAddsNewGroups()
    {
        $data = array(
            'groups' => array(
                array(
                    'title' => 'foo'
                )
            )
        );

        $this->assertFalse($this->config->issetGroup(sha1('foo')));
        $this->config->fromArray($data);
        $this->assertTrue($this->config->issetGroup(sha1('foo')));
    }
}
