<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/18/14
 * Time: 8:53 PM
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