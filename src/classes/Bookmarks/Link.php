<?php

namespace Bookmarks;

class Link
{
    /**
     * @var string
     */
    public $url = '';
    /**
     * @var string
     */
    public $title = '';
    /**
     * @var string[]
     */
    public $tags = array();

    /**
     * @param string $tag
     */
    public function addTag($tag)
    {
        if ( !in_array($tag, $this->tags) ) {
            $this->tags[] = $tag;
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return sha1($this->url . $this->title);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function assertValid()
    {
        if ( empty($this->title) ) {
            throw new \InvalidArgumentException("link title may not be empty");
        }
        if ( empty($this->url) ) {
            throw new \InvalidArgumentException("link url may not be empty");
        }
        if ( !Url::isUrl($this->url) ) {
            throw new \InvalidArgumentException("'{$this->url}' is no valid url");
        }
    }



    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'url' => $this->url,
            'title' => $this->title,
            'tags' => $this->tags,
        );
    }

    /**
     * @param array $data
     */
    public function fromArray(array $data)
    {
        $this->url = isset($data['url']) ? $data['url'] : '';
        $this->title = isset($data['title']) ? $data['title'] : '';
        $this->tags = isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : array();
    }

    /**
     * @param string $word
     *
     * @return array
     */
    public function search($word)
    {
        $ids = array();

        if ( false !== strpos($this->title, $word) ) {
            $ids[] = $this->getId();
        } else if ( false !== strpos($this->url, $word) ) {
            $ids[] = $this->getId();
        } else {
            foreach ( $this->tags as $tag ) {
                if ( false !== strpos($tag, $word) ) {
                    $ids[] = $this->getId();
                    break;
                }
            }
        }

        return $ids;
    }
} 