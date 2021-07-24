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
    public function __construct($id, $params = null)
    {
        $config = array_merge(YiiS::$config['components'][$id], (array)$params);
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
//        echo "instance component {$id}" . PHP_EOL;
        if (isset($this->component[$id])) {
//            echo "is instance component {$id}" . PHP_EOL;
            return $this->component[$id];
        }

        if (isset(YiiS::$config['components'][$id])) {
//            echo "create {$id} component" . PHP_EOL;
            $className = YiiS::$config['components'][$id]['class'];
            return $this->component[$id] = new $className($id, $value);
        }else{
//            echo "can't find {$id} component";
            //            throw new Error("can't find {$id} component");
        }

    }

}