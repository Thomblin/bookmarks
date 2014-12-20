<?php

namespace Bookmarks;

class Storage
{
    /**
     * @var string
     */
    private $filename;
    /**
     * @var Config
     */
    private $config;

    public function __construct($filename)
    {
        $this->filename = $filename;
        $this->config   = $this->parseConfig($filename);
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    private function parseConfig($filename)
    {
        $config = new Config();

        $config->fromArray($this->readConfig());

        return $config;
    }

    /**
     * @param string $filename
     *
     * @return array
     */
    private function readConfig()
    {
        if ( file_exists($this->filename) ) {
            return json_decode(file_get_contents($this->filename), true);
        }

        return array();
    }

    /**
     * @param Config $config
     */
    public function saveConfig($config)
    {
        $this->config = $config;

        file_put_contents(
            $this->filename,
            json_encode($this->config->toArray(), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)
        );
    }

    public function getAllWords($search = null)
    {
        $config = $this->readConfig();

        $words = array();

        array_walk_recursive($config, function($text) use(&$words, $search) {

            $isUrl = Url::isUrl($text);

            if ( !$isUrl && null !== $search && 0 === strpos($text, $search) ) {
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

        $words = array_unique($words);
        sort($words);

        return $words;
    }
} 