<?php

namespace toom1996\base;

use YiiS;

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
            $config = array_merge(YiiS::$config['components'][$id], (array)$params);
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
    
}