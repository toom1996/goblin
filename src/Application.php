<?php


namespace toom1996;


use toom1996\http\Eazy;
use toom1996\http\Http;

class Application
{
    public array $extensions;

    public function __construct(array $config = [])
    {
        $this->registerExtension($config);

        var_dump($this->extensions);
    }

    protected function registerExtension(array $config)
    {
        var_dump($config);
        foreach ($config['extensions'] as $extension => $property) {
            $refClass = new \ReflectionClass($extension);
            $classAttrs = $refClass->get();
            var_dump($classAttrs);
            $this->extensions[] = $extension;
        }
    }
}