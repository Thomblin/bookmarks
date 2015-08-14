<?php
/**
 * Search is used to return words and according ids that are stored in given Config and matching a search string.
 */

namespace Bookmarks;


class Search
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Environment
     */
    private $environment;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $search
     */
    public function findWords($search)
    {
        $result = array();

        $search = trim($search);

        if ( null === $search || '' === $search ) {
            return $result;
        }

        foreach ( $this->config->getGroups() as $group ) {
            $result = array_merge($result, $this->getWordsInGroup($group, $search));

            foreach ( $group->getLinks() as $link ) {
                $result = array_merge($result, $this->getWordsInLink($link, $search));
            }
        }

        sort($result);
        $result = array_values(array_intersect_key(
            $result,
            array_unique(array_map("StrToLower", $result))
        ));

        return $result;
    }

    /**
     * @param Group $group
     * @param string $search
     * @return array
     */
    private function getWordsInGroup(Group $group, $search)
    {
        $result = array();

        $text = $this->replaceInjectedVars($group->title);

        if ( $this->isWordPartOfText($search, $text) ) {
            $result[] = $text;
        }

        return $result;
    }

    /**
     * @param string $search
     * @param string $text
     * @return bool
     */
    private function isWordPartOfText($search, $text)
    {
        return false !== stripos($text, " {$search}") || 0 === stripos($text, $search);
    }

    /**
     * @param Group $group
     * @param string $search
     * @return array
     */
    private function getWordsInLink(Link $link, $search)
    {
        $result = array();

        $title = $this->replaceInjectedVars($link->title);

        if ( $this->isWordPartOfText($search, $title) ) {
            $result[] = $title;
        }
        $url = $this->replaceInjectedVars($link->url);
        foreach (preg_split('/[\W]/', $url, -1, PREG_SPLIT_NO_EMPTY) as $word) {
            if ( $this->isWordPartOfText($search, $word)) {
                $result[] = $word;
            }
        }
        foreach ( $link->tags as $tag ) {
            $tag = $this->replaceInjectedVars($tag);
            if ( $this->isWordPartOfText($search, $tag) ) {
                $result[] = $tag;
            }
        }

        return $result;
    }

    /**
     * @param string $search
     */
    public function findIds($search)
    {
        $result = array();

        foreach ( $this->config->getGroups() as $group ) {
            if ( 0 < count($this->getWordsInGroup($group, $search)) ) {
                $result[] = $group->getId();
            }

            foreach ( $group->getLinks() as $link ) {
                if (0 < count($this->getWordsInLink($link, $search))) {
                    $result[] = $link->getId();
                    $result[] = $group->getId();
                }
            }
        }

        return $result;
    }

    /**
     * @param Environment $env
     */
    public function injectEnvironment(Environment $env)
    {
        $this->environment = $env;
    }

    /**
     * @param Environment $env
     */
    private function replaceInjectedVars($text)
    {
        if ( null !== $this->environment ) {
            $text = $this->environment->replaceInjectedVars($text);
        }

        return $text;
    }
}