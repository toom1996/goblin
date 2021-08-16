<?php

namespace toom1996\base;

use toom1996\http\Goblin;
use yii\base\UnknownPropertyException;

class Component
{

    /**
     * Application component
     * @var
     */
    protected $component;


    /**
     * Component constructor.
     *
     * @param $id
     * @param  null  $params
     */
    public function __construct($id = null, $params = null)
    {
        if ($id) {
            $config = array_merge(Goblin::$config['components'][$id], (array)$params);
            foreach ($config as $name => $value) {
                if (property_exists($this, $name)) {
                    $this->{$name} = $value;
                }
            }
        }
        $this->init();
    }

    public function init()
    {

    }

    /**
     *
     * @param $name
     *
     * @return mixed
     * @throws \toom1996\base\UnknownClassException
     */
    public function __get(string $name)
    {
        // TODO: Implement __get() method.
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