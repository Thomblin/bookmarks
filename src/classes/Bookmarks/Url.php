<?php
/**
 * Url is used to decide whether a string is a valid url
 */

namespace Bookmarks;

class Url
{
    const ABSOLUTE_PATH = "#/(?:(?:[A-Za-z0-9\-._~!$&'()*+,;=:@]|%[0-9A-Fa-f]{2})+(?:/(?:[A-Za-z0-9\-._~!$&'()*+,;=:@]|%[0-9A-Fa-f]{2})*)*)?#";

    /**
     * @param string $string
     *
     * @return bool
     */
    public static function isUrl($string)
    {
        return filter_var($string, FILTER_VALIDATE_URL) || preg_match(self::ABSOLUTE_PATH, $string);
    }
}