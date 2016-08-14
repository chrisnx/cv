<?php

use Symfony\Component\Yaml\Yaml;

error_reporting(E_ERROR);

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app['cvData'] = function() {
    $dataFiles = [
        'contact_details',
        'find_me',
        'non_work_history',
        'skills',
        'work_history',
        'education',
        'about'
    ];

    $cvData = [];

    foreach($dataFiles as $dataFile) {
        $cvData[$dataFile] = Yaml::parse(file_get_contents(__DIR__."/data/$dataFile.yml"));
    }

    return $cvData;
};

$app->get('/', function () use ($app) {
    return $app['twig']->render('cv.twig', $app['cvData']);
});

$app->get('/api/v1/cv', function () use ($app) {
    //TODO: HalJson, multiple endpoints;
    return $app->json($app['cvData'], 200);
});

return $app;