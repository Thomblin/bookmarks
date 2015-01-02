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
     */
    public function findWordsReturnsWordInGroupTitle()
    {
        $group = new \Bookmarks\Group();
        $group->title = 'search engine';

        $config = new \Bookmarks\Config();
        $config->addGroup($group);

        $search = new \Bookmarks\Search($config);

        $this->assertEquals(
            array(),
            $search->findWords(''),
            'empty search should find nothing'
        );
        $this->assertEquals(
            array(),
            $search->findWords('foo'),
            'search should find nothing'
        );
        $this->assertEquals(
            array('search engine'),
            $search->findWords('search'),
            'string starts with search returns string'
        );
        $this->assertEquals(
            array('search engine'),
            $search->findWords('engine'),
            'string with search returns string'
        );
        $this->assertEquals(
            array(),
            $search->findWords('ngine'),
            'string with search returns only string if word begins with search'
        );
    }

    /**
     * @test
     */
    public function findWordsReturnsWordInLinkTitle()
    {
        $group = new \Bookmarks\Group();
        $group->title = 'search engine';

        $link = new \Bookmarks\Link();
        $link->title = 'fancy link';
        $link->url = 'http://abc.de';

        $group->addLink($link);

        $config = new \Bookmarks\Config();
        $config->addGroup($group);

        $search = new \Bookmarks\Search($config);

        $this->assertEquals(
            array(),
            $search->findWords(''),
            'empty search should find nothing'
        );
        $this->assertEquals(
            array(),
            $search->findWords('foo'),
            'search should find nothing'
        );
        $this->assertEquals(
            array('fancy link'),
            $search->findWords('fancy'),
            'string starts with search returns string'
        );
        $this->assertEquals(
            array('fancy link'),
            $search->findWords('link'),
            'string with search returns string'
        );
        $this->assertEquals(
            array(),
            $search->findWords('ink'),
            'string with search returns only string if word begins with search'
        );
    }

    /**
     * @test
     */
    public function findWordsReturnsWordInLinkUrl()
    {
        $group = new \Bookmarks\Group();
        $group->title = 'search engine';

        $link = new \Bookmarks\Link();
        $link->title = 'fancy link';
        $link->url = 'http://abc.de/index.php';

        $group->addLink($link);

        $config = new \Bookmarks\Config();
        $config->addGroup($group);

        $search = new \Bookmarks\Search($config);

        $this->assertEquals(
            array(),
            $search->findWords(''),
            'empty search should find nothing'
        );
        $this->assertEquals(
            array(),
            $search->findWords('foo'),
            'search should find nothing'
        );
        $this->assertEquals(
            array('abc'),
            $search->findWords('abc'),
            'search in url returns domain'
        );
        $this->assertEquals(
            array('index'),
            $search->findWords('index'),
            'search in url returns part of url'
        );
        $this->assertEquals(
            array(),
            $search->findWords('bc'),
            'string with search returns only string if word begins with search'
        );
        $this->assertEquals(
            array(),
            $search->findWords('ndex'),
            'string with search returns only string if word begins with search'
        );
    }

    /**
     * @test
     */
    public function findWordsReturnsWordInLinkTag()
    {
        $group = new \Bookmarks\Group();
        $group->title = 'search engine';

        $link = new \Bookmarks\Link();
        $link->title = 'fancy link';
        $link->url = 'http://abc.de/index.php';
        $link->tags = array('mysearch');

        $group->addLink($link);

        $config = new \Bookmarks\Config();
        $config->addGroup($group);

        $search = new \Bookmarks\Search($config);

        $this->assertEquals(
            array(),
            $search->findWords(''),
            'empty search should find nothing'
        );
        $this->assertEquals(
            array(),
            $search->findWords('foo'),
            'search should find nothing'
        );
        $this->assertEquals(
            array('mysearch'),
            $search->findWords('mysearch'),
            'search should be found in link tag'
        );
        $this->assertEquals(
            array(),
            $search->findWords('ysearch'),
            'string with search returns only string if word begins with search'
        );
    }

    /**
     * @test
     */
    public function findWordsReturnsWordsUniqueAndInOrder()
    {
        $config = new \Bookmarks\Config();

        $group = new \Bookmarks\Group();
        $group->title = 'search engine';

        $link = new \Bookmarks\Link();
        $link->title = 'search';
        $link->url = 'http://any-search.de';
        $link->tags = array('search engine', 'search');

        $group->addLink($link);

        $config->addGroup($group);

        $search = new \Bookmarks\Search($config);

        $this->assertEquals(
            array('search', 'search engine'),
            $search->findWords('search'),
            'search results shall be ordered and unique'
        );
    }

    /**
     * @test
     */
    public function findIdsReturnsIdsIfGroupTitleMatches()
    {
        $config = new \Bookmarks\Config();

        $group = new \Bookmarks\Group();
        $group->title = 'search engine';

        $config->addGroup($group);

        $search = new \Bookmarks\Search($config);

        $this->assertEquals(
            array($group->getId()),
            $search->findIds('search'),
            'search result should contain group id if search matches'
        );
        $this->assertEquals(
            array(),
            $search->findIds('foo'),
            'search result should not contain group id if search not matches'
        );
    }

    /**
     * @test
     */
    public function findIdsReturnsGroupIdAndLinkIdIfLinkTitleMatches()
    {
        $group = new \Bookmarks\Group();
        $group->title = 'search engine';

        $link = new \Bookmarks\Link();
        $link->title = 'fancy link';
        $link->url = 'http://abc.de';

        $group->addLink($link);

        $config = new \Bookmarks\Config();
        $config->addGroup($group);

        $search = new \Bookmarks\Search($config);

        $this->assertEquals(
            array(),
            $search->findIds('foo'),
            'search should find nothing'
        );
        $this->assertEquals(
            array($link->getId(), $group->getId()),
            $search->findIds('fancy'),
            'search result should contain group id and link id if search matches'
        );
    }

    /**
     * @test
     */
    public function findIdsReturnsGroupIdAndLinkIdIfLinkUrlMatches()
    {
        $group = new \Bookmarks\Group();
        $group->title = 'search engine';

        $link = new \Bookmarks\Link();
        $link->title = 'fancy link';
        $link->url = 'http://abc.de';

        $group->addLink($link);

        $config = new \Bookmarks\Config();
        $config->addGroup($group);

        $search = new \Bookmarks\Search($config);

        $this->assertEquals(
            array(),
            $search->findIds('foo'),
            'search should find nothing'
        );
        $this->assertEquals(
            array($link->getId(), $group->getId()),
            $search->findIds('abc'),
            'search result should contain group id and link id if search matches'
        );
    }

    /**
     * @test
     */
    public function findIdsReturnsGroupIdAndLinkIdIfLinkTagMatches()
    {
        $group = new \Bookmarks\Group();
        $group->title = 'search engine';

        $link = new \Bookmarks\Link();
        $link->title = 'fancy link';
        $link->url = 'http://abc.de';
        $link->tags = array('mylink');

        $group->addLink($link);

        $config = new \Bookmarks\Config();
        $config->addGroup($group);

        $search = new \Bookmarks\Search($config);

        $this->assertEquals(
            array(),
            $search->findIds('foo'),
            'search should find nothing'
        );
        $this->assertEquals(
            array($link->getId(), $group->getId()),
            $search->findIds('mylink'),
            'search result should contain group id and link id if search matches'
        );
    }

    /**
     * @test
     */
    public function findWordsReturnsWordWithInjectedVars()
    {
        $group = new \Bookmarks\Group();
        $group->title = '{group.title}';

        $link = new \Bookmarks\Link();
        $link->title = '{link.title}';
        $link->url = 'http://{link.domain}.de';
        $link->tags = array('{link.tag}');

        $group->addLink($link);

        $config = new \Bookmarks\Config();
        $config->addGroup($group);

        $env = new \Bookmarks\Environment();
        $env->inject('group.title', 'mytitle');
        $env->inject('link.title', 'mylinktitle');
        $env->inject('link.domain', 'mydomain');
        $env->inject('link.tag', 'mytag');

        $search = new \Bookmarks\Search($config);
        $search->injectEnvironment($env);

        $this->assertEquals(
            array('mytitle'),
            $search->findWords('mytitle'),
            'search should find replaced string in group title'
        );
        $this->assertEquals(
            array('mylinktitle'),
            $search->findWords('mylinktitle'),
            'search should find replaced string in link title'
        );
        $this->assertEquals(
            array('mydomain'),
            $search->findWords('mydomain'),
            'search should find replaced string in link url'
        );
        $this->assertEquals(
            array('mytag'),
            $search->findWords('mytag'),
            'search should find replaced string in link tag'
        );
    }
}
