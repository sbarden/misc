<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../db-connect.php';

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

// Setup the application
$app = new Application();
$app->register(new TwigServiceProvider, array(
    'twig.path' => __DIR__ . '/templates',
));

// Setup the database
$app['db.table'] = DB_TABLE;
$app['db.dsn'] = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST;
$app['db'] = $app->share(function ($app) {
    return new PDO($app['db.dsn'], DB_USER, DB_PASSWORD);
});

// Handle the index page
$app->match('/', function () use ($app) {
    $query = $app['db']->prepare("SELECT message, author FROM {$app['db.table']}");
    $thoughts = $query->execute() ? $query->fetchAll(PDO::FETCH_ASSOC) : array();

    return $app['twig']->render('index.twig', array(
        'title'    => 'What would you like to play?',
        'thoughts' => $thoughts,
    ));
});

$app->run();
