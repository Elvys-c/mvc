<?php

require __DIR__.'/../vendor/autoload.php';

use App\Utils\View;
use WilliamCosta\DotEnv\Environment;
use WilliamCosta\DatabaseManager\Database;
use App\Http\Middleware\Queue as MiddlewareQueue;

// Carrega Variáveis de Ambiente
Environment::load(__DIR__.'/../');

// Define as configurações de banco de dados
Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_PORT')
);

// Define a constante de URL
define('URL', getenv('URL'));

// Definie o valor padrão das variáveis
View::init([
    'URL' => URL
]);

// Define o mapeamento do middlewares
MiddlewareQueue::setMap([
    'maintenance' => App\Http\Middleware\Maintenance::class
]);

// Define o mapeamento do middlewares padrões (executados em todas as rotas)
MiddlewareQueue::setDefault([
    'maintenance'
]);
