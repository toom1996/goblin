<?php


namespace toom1996\base;


use yii\base\InvalidConfigException;
use YiiS;

class ServiceLocator extends Component
{

    /**
     * @var
     */
    private $_components;

    /**
     *
     * @param  string  $id
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function get(string $id)
    {
        if (isset($this->_component[$id])) {
            return $this->_components[$id];
        }

        if (isset(YiiS::$config['components'][$id])) {
            $className = YiiS::$config['components'][$id]['class'];
            $this->set();
        }else{
            throw new InvalidConfigException("Unknown component ID: $id");
        }
    }

    public function has(string $id)
    {
        // TODO: Implement has() method.
    }

    /**
     *
     * @param $id
     * @param $definition
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function set($id, $definition)
    {
        unset($this->_components[$id]);

        if (is_array($definition)) {
            if (isset($definition['class'])) {
                $class = new \ReflectionClass($definition['class']);
                $this->_components[$id] = $class->newInstanceArgs($definition);
            } else {
                throw new InvalidConfigException("The configuration for the \"$id\" component must contain a \"class\" element.");
            }
        } else {
            throw new InvalidConfigException("Unexpected configuration type for the \"$id\" component: " . gettype($definition));
        }
    }
}