<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/30/14
 * Time: 9:30 PM
 */

class SearchTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @param array $words
     * @param string $search
     * @param array $expected
     *
     * @dataProvider provideSearchConfig
     */
    public function getAllWordsReturnsMatchingWords($words, $search, $expected)
    {
        $config = $this->getMock('Bookmarks\Config', array('toArray'));
        $config->expects($this->once())
            ->method('toArray')
            ->will($this->returnValue($words));

        $searchObj = new \Bookmarks\Search($config);

        $this->assertEquals(
            $expected,
            $searchObj->getAllWords($search)
        );
    }

    public function provideSearchConfig()
    {
        return array(
            'empty search finds nothing' => array(
                'words' => array('test'),
                'search' => '',
                'expected' => array(),
            ),
            'search finds nothing' => array(
                'words' => array(),
                'search' => 'search',
                'expected' => array(),
            ),
            'string starts with search returns string' => array(
                'words' => array('search engine'),
                'search' => 'search',
                'expected' => array('search engine'),
            ),
            'string with search returns string' => array(
                'words' => array('search engine'),
                'search' => 'engine',
                'expected' => array('search engine'),
            ),
            'search in url returns domain' => array(
                'words' => array('http://abc.de'),
                'search' => 'abc',
                'expected' => array('abc'),
            ),
            'search in url returns part of url' => array(
                'words' => array('http://abc.de/index.php'),
                'search' => 'index',
                'expected' => array('index'),
            ),
            'search is executed on key only' => array(
                'words' => array('search' => 'any values'),
                'search' => 'search',
                'expected' => array(),
            ),
            'search is executed recursive' => array(
                'words' => array('group' => array('search engine')),
                'search' => 'search',
                'expected' => array('search engine'),
            ),
            'search results are ordered' => array(
                'words' => array('ghi abc', 'abc def', 'def abc'),
                'search' => 'abc',
                'expected' => array('abc def', 'def abc', 'ghi abc'),
            ),
            'search results are unqiue' => array(
                'words' => array(array('abc'), array('abc', 'abc def')),
                'search' => 'abc',
                'expected' => array('abc', 'abc def'),
            ),
        );
    }
}
