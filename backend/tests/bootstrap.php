<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// Force test environment regardless of container environment variables
putenv('APP_ENV=test');
$_ENV['APP_ENV'] = 'test';
$_SERVER['APP_ENV'] = 'test';

// bootEnv loads .env then cascades .env.test on top
(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
