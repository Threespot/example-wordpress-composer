<?php

use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\OpenURL;

class BedrockTinkerwellDriver extends WordpressTinkerwellDriver
{
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/composer.json') &&
               file_exists($projectPath . '/web/wp-config.php');
    }

    public function bootstrap($projectPath)
    {
        $webRoot = $projectPath . '/web';
        // Ensure autoloading
        require_once $projectPath . '/vendor/autoload.php';

        // CLI-safe environment for Tinkerwell
        $isCli = php_sapi_name() === 'cli';
        if ($isCli) {
            $_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $_SERVER['SERVER_PROTOCOL'] = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
        }

        // Load WordPress
        require_once $projectPath . '/web/wp/wp-blog-header.php';
    }

    public function getAvailableVariables()
    {

        return [
            'blogInfo' => get_bloginfo(),
        ];
    }  
    
}
