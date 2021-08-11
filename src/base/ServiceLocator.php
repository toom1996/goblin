<?php


namespace toom1996\base;


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
     * @throws \toom1996\base\InvalidConfigException
     */
    public function get(string $id)
    {
        if (isset($this->_components[$id])) {
            return $this->_components[$id];
        }

        if (isset(YiiS::$config['components'][$id])) {
            return $this->set($id);
        }else{
            throw new InvalidConfigException("Unknown component ID: $id");
        }
    }

    /**
     * Has component.
     * @param  string  $id
     *
     * @return bool
     */
    public function has(string $id)
    {
        // TODO: Implement has() method.
        return isset($this->_components[$id]);
    }

    /**
     *
     * @param $id
     * @param  null  $definition
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     */
    public function set($id, $definition = null)
    {
        unset($this->_components[$id]);

        if (is_array($definition) || is_object($definition)) {
            if (is_object($definition)) {
                $definition = (array)$definition;
            }

            // e.g YiiS::$app->set('foo', ['class' => foo\bar, 'a' => 'b'])
            // If has class, it will be overwrite all component attribuets.
            if (isset($definition['class'])) {
                $class = new \ReflectionClass($definition['class']);
                $this->_components[$id] = $class->newInstanceArgs([$id, $definition]);
            }
        }

        // e.g YiiS::$app->set('foo', ['a' => 'b'])
        if (!isset(YiiS::$config['components'][$id]['class'])) {
            throw new InvalidConfigException("Unexpected configuration type for the \"$id\" component: " . gettype($definition));
        }
        $class = new \ReflectionClass(YiiS::$config['components'][$id]['class']);
        $this->_components[$id] = $class->newInstanceArgs([$id, $definition]);

        return $this->_components[$id];
    }

    /**
     *
     * @param  string  $name
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     * @throws \toom1996\base\UnknownClassException
     */
    public function __get(string $name)
    {
        if ($this->has($name)) {
            return $this->get($name);
        }

        return parent::__get($name);
    }
}