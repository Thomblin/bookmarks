<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 1/2/15
 * Time: 2:55 AM
 */

namespace Bookmarks;


class Environment
{
    /**
     * @var string[]
     */
    private $injectedVars = array();


    /**
     * inject a variable to be replaced after template was rendered, if template contains {$name}
     *
     * @param string $name
     * @param mixed $value
     */
    public function inject($name, $value)
    {
        $this->injectedVars['{' . $name . '}'] = $value;
    }

    /**
     * @param string $text
     * @return mixed
     */
    public function replaceInjectedVars($text)
    {
        return str_replace(
            array_keys($this->injectedVars),
            array_values($this->injectedVars),
            $text
        );
    }
}