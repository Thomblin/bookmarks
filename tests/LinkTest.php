<?php

/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/20/14
 * Time: 1:38 AM
 */
class LinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function addTagAddsNewTags()
    {
        $link = new \Bookmarks\Link();

        $this->assertEmpty($link->tags);

        $link->addTag('test');

        $this->assertEquals(array('test'), $link->tags);
    }

    /**
     * @test
     */
    public function addTagDoesNotInsertDuplicates()
    {
        $link = new \Bookmarks\Link();

        $link->addTag('foo');

        $this->assertEquals(array('foo'), $link->tags);

        $link->addTag('foo');

        $this->assertEquals(array('foo'), $link->tags);
    }


    /**
     * @test
     */
    public function getIdReturnsSha1Hash()
    {
        $link = new \Bookmarks\Link();
        $link->title = 'my link';
        $link->url = 'http://abc.de';

        $expected = sha1('http://abc.de' . 'my link');

        $this->assertEquals($expected, $link->getId());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage link title may not be empty
     */
    public function assertValidFailsIfTitleIsEmpty()
    {
        $link = new \Bookmarks\Link();
        $link->assertValid();
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage link url may not be empty
     */
    public function assertValidFailsIfUrlIsEmpty()
    {
        $link = new \Bookmarks\Link();
        $link->title = 'i am a title';
        $link->assertValid();
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage is no valid url
     */
    public function assertValidFailsIfUrlIsNoUrl()
    {
        $link = new \Bookmarks\Link();
        $link->title = 'i am a title';
        $link->url = 'i am not an url';
        $link->assertValid();
    }

    /**
     * @test
     */
    public function toArrayReturnsArrayOfLink()
    {
        $link = new \Bookmarks\Link();
        $link->title = 'this is a titel';
        $link->url = 'http://abc.de';
        $link->tags = array('foo');

        $expected = array(
            'title' => 'this is a titel',
            'url' => 'http://abc.de',
            'tags' => array('foo')
        );

        $this->assertEquals($expected, $link->toArray());
    }

    /**
     * @test
     */
    public function fromArrayCreatesEmptyLinkWithEmptyArray()
    {
        $link = new \Bookmarks\Link();
        $link->fromArray(array());

        $this->assertEmpty($link->title);
        $this->assertEmpty($link->url);
        $this->assertEmpty($link->tags);
    }

    /**
     * @test
     */
    public function fromArrayCreatesLinkWithAllAttributes()
    {
        $link = new \Bookmarks\Link();
        $link->fromArray(array(
            'title' => 'this is a titel',
            'url' => 'http://abc.de',
            'tags' => array('foo')
        ));

        $this->assertEquals('this is a titel', $link->title);
        $this->assertEquals('http://abc.de', $link->url);
        $this->assertEquals(array('foo'), $link->tags);
    }

    /**
     * @test
     * @dataProvider provideSuccessfulSearchValues
     */
    public function searchReturnsLinkId($values, $word)
    {
        $link = new \Bookmarks\Link();
        $link->fromArray($values);

        $this->assertEquals(array($link->getId()), $link->search($word));
    }

    /**
     * @return array
     */
    public function provideSuccessfulSearchValues()
    {
        return array(
            'any word in title matches' => array(
                'values' => array('title' => 'i am the title'),
                'word' => 'am'
            ),
            'full title matches' => array(
                'values' => array('title' => 'i am the title'),
                'word' => 'i am the title'
            ),
            'any word in url matches' => array(
                'values' => array('url' => 'http://abc.de'),
                'word' => 'abc'
            ),
            'full url matches' => array(
                'values' => array('url' => 'http://abc.de'),
                'word' => 'http://abc.de'
            ),
            'any word in tag matches' => array(
                'values' => array('tags' => array('search engine')),
                'word' => 'search'
            ),
            'second word in tag matches' => array(
                'values' => array('tags' => array('search engine')),
                'word' => 'engine'
            ),
            'full tag matches' => array(
                'values' => array('tags' => array('search engine')),
                'word' => 'search engine'
            ),
        );
    }

    /**
     * @test
     * @dataProvider provideSearchValuesWithoutResult
     */
    public function searchReturnsEmptyArray($values, $word)
    {
        $link = new \Bookmarks\Link();
        $link->fromArray($values);

        $this->assertEquals(array(), $link->search($word));
    }

    /**
     * @return array
     */
    public function provideSearchValuesWithoutResult()
    {
        return array(
            'no values return empty result' => array(
                'values' => array(),
                'word' => 'foo'
            ),
        );
    }
}
