<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/10/14
 * Time: 9:23 PM
 */

namespace Bookmarks;


class Group
{
    /**
     * @var string
     */
    public $title = '';
    /**
     * @var Link[]
     */
    private $links = array();

    /**
     * @param Link $link
     */
    public function addLink(Link $link)
    {
        $link->assertValid();

        $this->links[$link->getId()] = $link;
    }

    /**
     * @return Link[]
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @return bool
     */
    public function issetLink($id)
    {
        return isset($this->links[$id]);
    }

    /**
     * @return Link
     * @throws \InvalidArgumentException
     */
    public function getLink($id)
    {
        if ( !$this->issetLink($id) ) {
            throw new \InvalidArgumentException("link [$id] not found");
        }

        return $this->links[$id];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return sha1($this->title);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function assertValid()
    {
        if ( empty($this->title) ) {
            throw new \InvalidArgumentException("group title may not be empty");
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $links = array();
        foreach ( $this->links as $link ) {
            $links[] = $link->toArray();
        }

        return array(
            'title' => $this->title,
            'links' => $links,
        );
    }

    /**
     * @param $data
     */
    public function fromArray(array $data)
    {
        $this->title = isset($data['title']) ? $data['title'] : '';

        if ( isset($data['links']) && is_array($data['links']) ) {
            foreach ( $data['links'] as $linkData ) {
                $link = new Link();
                $link->fromArray($linkData);

                $this->addLink($link);
            }
        }
    }

    /**
     * @param string $word
     *
     * @return array
     */
    public function search($word)
    {
        $ids = array();

        foreach ( $this->getLinks() as $link ) {
            $ids = array_merge($ids, $link->search($word));
        }

        if ( !empty($ids) || false !== strpos($this->title, $word) ) {
            $ids[] = $this->getId();
        }

        return $ids;
    }
} 