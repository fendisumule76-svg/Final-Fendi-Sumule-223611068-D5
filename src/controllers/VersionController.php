<?php 
namespace Src\Controllers;

use Src\Helpers\Response;

class VersionController
{
    private array $config;
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function show() : void
    {
        $version = $this->config['app']['version']?? '1.0.0';
        Response::json(['version' => $version]);
    }
}