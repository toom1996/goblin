<?php


namespace toom1996;


use toom1996\base\BaseConsole;
use toom1996\base\BootstrapInterface;
use toom1996\base\Stdout;
use toom1996\helpers\Console;
use toom1996\http\Eazy;
use toom1996\http\Http;

class Application extends BaseConsole
{
    public array $extensions = [];

    public function __construct(array $config = [])
    {
        $this->registerExtension($config);
    }

    /**
     * Register extensions.
     * @param  array  $config
     *
     * @throws \ReflectionException
     */
    protected function registerExtension(array $config)
    {
        foreach ($config['extensions'] as $extension => $property) {
            $reflection = new \ReflectionClass($extension);
            if ($reflection->implementsInterface(BootstrapInterface::class)) {
                $class = $reflection->newInstanceArgs([$property]);
                $this->extensions[$class->getName()] = $class;
            }else{
                throw new \Error("extension must be instance" . BootstrapInterface::class);
            }
        }
    }

    protected function getHelpful()
    {
        Stdout::string("This is Eazy version 1.0.0.");
        Stdout::string(str_repeat("\n", 2));
        Stdout::string("The following commands are available:");
        Stdout::string("\n");
        $maxLength = 0;
        foreach ($this->extensions as $command => $class) {
            $maxLength = max($maxLength, strlen($command . "[{$class->getVersion()}]"));
        }
        foreach ($this->extensions as $command => $class){
            $description = $class->getDescription();
            $version = $class->getVersion();
            Console::stdout('  ' . $command . "[{$class->getVersion()}]");
            Console::stdout(str_repeat(' ', $maxLength + 4 - strlen($command)));
            Console::stdout(Console::wrapText($description, $maxLength + 4 + 2), Console::BOLD);
            Console::stdout("\n");
        }
    }

    public function run()
    {
        $params = $this->getParams();
        if (isset($params[0])) {
            $class = $this->extensions[$params[0]];
            if (!in_array($params[0], array_keys($this->extensions))) {
                Console::stdout("{$this->getSuggestionCommand($params[0])}");
                exit(0);
            }

            $class->bootstrap();
        }else{
            $this->getHelpful();
        }
    }

    protected function getSuggestionCommand($missingModuleName)
    {
        $shortestLevenshteinDistance = null;
        $suggestedModuleName = null;
        foreach ($this->extensions as $moduleName => $module) {
            $levenshteinDistance = levenshtein($missingModuleName, $moduleName);
            if ($shortestLevenshteinDistance === null || $levenshteinDistance <= $shortestLevenshteinDistance) {
                $shortestLevenshteinDistance = $levenshteinDistance;
                $suggestedModuleName = $moduleName;
            }
        }

        if ($suggestedModuleName !== null && $shortestLevenshteinDistance <= 5) {
            return " Did you mean '$suggestedModuleName'?";
        }

        return '';
    }

    /**
     * Set Parameters of console running.
     * @return array
     */
    protected function getParams(): array
    {
        $rawParams = [];
        if (isset($_SERVER['argv'])) {
            $rawParams = $_SERVER['argv'];
            array_shift($rawParams);
        }

        $params = [];
        foreach ($rawParams as $param) {
            if (preg_match('/^-([\w-]*\w)(=(.*))?$/', $param, $matches)) {
                $name = $matches[1];
                $params[$name] = isset($matches[3]) ? $matches[3] : true;
            } else {
                $params[] = $param;
            }
        }
        return $params;
    }
}