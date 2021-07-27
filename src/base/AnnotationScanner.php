<?php


namespace toom1996\base;


use toom1996\helpers\BaseFileHelper;

class AnnotationScanner
{
    /**
     * Scan directory
     * @var directory 
     */
    private $_scanDir;

    /**
     * @var string[]
     */
    private $_annotation;

    /**
     * Default scan attributes
     * @var array|string[]
     */
    private $_attributes = ['Url'];

    private $_arguments = [];

    /**
     * AnnotationScanner constructor.
     *
     * @param  array  $config
     */
    public function __construct(array $config)
    {
        $this->_scanDir = $config['basePath'];
        $this->_attributes = $config['scanner']['attributes'];
    }


    /**
     * Scan controller annotation
     * @return array
     * @throws \ReflectionException
     * @throws \toom1996\base\Exception
     */
    public function scan()
    {
        $controllers = BaseFileHelper::findFiles($this->_scanDir, ['only' => ['*Controller.php']]);
        foreach ($controllers as $controller) {
            $className = '\\' . $this->getNameSpace($controller) . '\\' . basename(str_replace('.php', '', $controller));
            $ref = new \ReflectionClass($className);
            foreach ($ref->getMethods() as $method) {
                $attr = $method->getAttributes();
                foreach ($attr as $arg) {
                    $attributesName = explode('\\',$arg->getName());
                    $attributesName = array_pop($attributesName);
                    if (in_array($attributesName, $this->_attributes )) {
                        $this->_annotation[$className][$method->getName()][$attributesName] = $arg->getArguments();
                    }
                }
            }
        }
        return $this->_annotation;
    }


    /**
     * get PHP file namesapce
     *
     * @param $file
     *
     * @return string
     * @throws Exception
     */
    private function getNameSpace($file)
    {
        $file = file_get_contents($file);
        if (!$r = preg_match('/namespace(.*);/', $file, $matches)) {
            throw new Exception('can not find namespace');
        }
        return trim($matches[1]);
    }
}