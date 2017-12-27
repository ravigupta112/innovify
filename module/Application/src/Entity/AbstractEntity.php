<?php

/**
 * Description of AbstractEntity
 *
 * @author Ravi
 */

namespace Application\Entity;

use Application\Library\DoctraineSerializor;

abstract class AbstractEntity
{

    protected $em = null;

    public function __construct($em = null)
    {
        $this->em = $em;
    }

    /**
     * Exchange array function set data to enity variable
     * @param array $data
     * @return void
     */
    public function exchangeArray(array $data)
    {
        $var = $this->getChidClassVars();
        array_walk($data, function(&$value, &$key) use(&$var) {
            $key = $this->__keyToVariable($key);
            if (in_array($key, $var)) {
                $this->$key = $value;
            }
        });
    }

    /**
     * it convert string to doctrain entity variable format
     * @param string $string
     * @return string
     */
    private function __keyToVariable($string)
    {
        return str_replace("'", "", str_replace(' ', '', ucwords(str_replace('_', ' ', "'" . $string))));
    }

    /**
     * It return child class varables
     * @return array
     */
    public function getChidClassVars()
    {
        return array_diff(array_keys(get_object_vars($this)), array_keys(get_class_vars(__CLASS__)));
    }

    public function save($em, $data = array())
    {
        $this->em = $em;
        $this->em->persist($this);
        $this->em->flush();
        return $this;
    }

    public function toArray($depth = 0, $whitelist = array(), $blacklist = array())
    {
        return DoctraineSerializor::toArray($this, $depth, $whitelist, $blacklist);
    }

}
