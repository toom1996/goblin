<?php

namespace toom1996\base;

class Component
{

    /**
     * Component constructor.
     *
     * @param $id
     * @param  null  $params
     */
    public function __construct($id, $params = null)
    {
        $config = array_merge(YiiS::config()['components'][$id], (array)$params);
        foreach ($config as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        }
        $this->init();
    }

    public function init()
    {

    }

}