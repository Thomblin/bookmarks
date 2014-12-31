<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/30/14
 * Time: 8:16 PM
 */

namespace Bookmarks;


class Search
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getAllWords($search = null)
    {
        $config = $this->config->toArray();

        $words = array();

        if ( '' !== $search ) {
            array_walk_recursive($config, function ($text) use (&$words, $search) {

                $isUrl = Url::isUrl($text);

                if (!$isUrl && null !== $search && 0 === strpos($text, $search)) {
                    $words[] = $text;
                } else {
                    foreach (preg_split('/[\W]/', $text, -1, PREG_SPLIT_NO_EMPTY) as $word) {
                        if (null === $search || 0 === strpos($word, $search)) {
                            if ($isUrl) {
                                $words[] = $word;
                            } else {
                                $words[] = $text;
                                break;
                            }
                        }
                    }
                }
            });
        }

        $words = array_unique($words);
        sort($words);

        return $words;
    }
}