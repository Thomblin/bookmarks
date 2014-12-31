<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/21/14
 * Time: 5:34 PM
 */

namespace TestHelper;

use Bookmarks\Group as BGroup;

class Group extends BGroup
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
     * @return string
     */
    public function getId()
    {
        return $this->id;
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