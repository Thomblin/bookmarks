<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/30/14
 * Time: 10:15 PM
 */

class UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @param string $url
     * @param bool $expected
     *
     * @dataProvider provideUrls
     */
    public function isUrlValidates($url, $expected)
    {
        $this->assertEquals(
            $expected,
            \Bookmarks\Url::isUrl($url),
            $expected
                ? "'$url' should be a valid url"
                : "'$url' should not be a valid url"
        );
    }

    /**
     * @return array
     */
    public function provideUrls()
    {
        return array(
            array(
                'url' => 'no url',
                'expected' => false
            ),
            array(
                'url' => 'http://abc.de',
                'expected' => true
            ),
            array(
                'url' => 'https://abc.de',
                'expected' => true
            ),
            array(
                'url' => 'abc.de',
                'expected' => false
            ),
            array(
                'url' => '/index.php',
                'expected' => true
            ),
            array(
                'url' => '/index.php?a=b',
                'expected' => true
            ),
        );
    }
}
