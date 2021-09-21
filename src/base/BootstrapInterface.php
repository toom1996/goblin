<?php

namespace toom1996\base;

interface BootstrapInterface
{
    public function getName(): string;
    
    public function getUsage(): array;
    
    public function bootstrap(array $params): void;
    
}