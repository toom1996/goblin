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

    /**
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->component($name);
    }

    public function component($id, $value = null)
    {
        if (isset($this->component[$id])) {
            return $this->component[$id];
        }

        if (isset(YiiS::$config['components'][$id])) {
            $className = YiiS::$config['components'][$id]['class'];
            new \ReflectionClass($className);
            return $this->component[$id] = new $className($id, $value);
        }else{
            // TODO
        }

    }

}