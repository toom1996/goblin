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
    
    private $_annotation;

    /**
     * AnnotationScanner constructor.
     *
     * @param $dir directory to be scanned
     */
    public function __construct($dir)
    {
        $this->_scanDir = $dir;
    }


    /**
     * 
     */
    public function scan()
    {
        $route = [];
        $controllers = BaseFileHelper::findFiles($this->_scanDir, ['only' => ['*Controller.php']]);
        foreach ($controllers as $controller) {
            $className = '\\' . $this->getNameSpace($controller) . '\\' . basename(str_replace('.php', '', $controller));
            $ref = new \ReflectionClass($className);
            foreach ($ref->getMethods() as $method) {
                $attr = $method->getAttributes();
                foreach ($attr as $arg) {
                    if ($arg->getName() === '')
                    var_dump($arg->getArguments());
                    var_dump($arg->getName());
                }
            }
        }
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