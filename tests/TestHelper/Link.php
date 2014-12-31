<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/21/14
 * Time: 5:34 PM
 */

namespace TestHelper;

use Bookmarks\Link as BLink;

class Link extends BLink
{
    /**
     * @var string
     */
    public $title = 'fancy group';
    /**
     * @var string
     */
    public $id = 'id';
    /**
     * @var string[]
     */
    public $searchResult = array();
    /**
     * @var bool
     */
    public $valid = true;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function assertValid()
    {
        return $this->valid;
    }

    /**
     * @param string $word
     *
     * @return array
     */
    public function search($word)
    {
        return isset($this->searchResult[$word])
            ? $this->searchResult[$word]
            : array();
    }
}