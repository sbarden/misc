<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../db-connect.php';

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
$app['debug'] = true
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
	$style_overrides = '<style>.hero-unit {background-color: green;}</style>'
    $query = $app['db']->prepare("SELECT message FROM {$app['db.table']}");
    $thoughts = $query->execute() ? $query->fetchAll(PDO::FETCH_ASSOC) : array();

    return $app['twig']->render('index.twig', array(
        'title'    => 'Would you like to play a game?',
        'thoughts' => $thoughts,
		'style_overrides' => $style_overrides,
    ));
});

$app->run();
