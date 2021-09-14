<?php

namespace toom1996\base;

use toom1996\http\Goblin;
use yii\base\UnknownPropertyException;

class Component extends BaseObject
{
    /**
     *
     * @param $name
     *
     * @return mixed
     * @throws UnknownClassException
     */
    public function __get(string $name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            // read property, e.g. getName()
            return $this->$getter();
        }

        if (method_exists($this, 'set' . $name)) {
            throw new InvalidCallException('Getting write-only property: ' . get_class($this) . '::' . $name);
        }

        throw new UnknownClassException('Getting unknown property: ' . get_class($this) . '::' . $name);
    }
}