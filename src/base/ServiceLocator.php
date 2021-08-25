<?php


namespace toom1996\base;


use toom1996\http\Goblin;

class ServiceLocator extends Component
{

    /**
     * @var
     */
    private $_components;

    /**
     *
     *
     * @param  string  $id
     *
     * @return mixed
     * @throws InvalidConfigException
     * @throws \ReflectionException
     */
    public function get(string $id)
    {
        if (isset($this->_components[$id])) {
            return $this->_components[$id];
        }

        if (isset(Goblin::$config['components'][$id])) {
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
     * @throws InvalidConfigException
     */
    public function set($id, $definition = null)
    {
        unset($this->_components[$id]);

        if (is_array($definition) || is_object($definition)) {
            if (is_object($definition)) {
                $definition = (array)$definition;
            }

            // e.g Goblin::$app->set('foo', ['class' => foo\bar, 'a' => 'b'])
            // If has class, it will be overwrite all component attribuets.
            if (isset($definition['class'])) {
                $this->_components[$id] = Goblin::createObject($definition['class'], [$id, $definition]);
            }
        }

        // e.g YiiS::$app->set('foo', ['a' => 'b'])
        if (!isset(Goblin::$config['components'][$id]['class'])) {
            throw new InvalidConfigException("Unexpected configuration type for the \"$id\" component: " . gettype($definition));
        }
        $this->_components[$id] = Goblin::createObject(Goblin::$config['components'][$id]['class'], [$id, $definition]);;

        return $this->_components[$id];
    }

    /**
     *
     * @param  string  $name
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws InvalidConfigException
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