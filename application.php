<?php

use Symfony\Component\Yaml\Yaml;

error_reporting(E_ERROR);

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app['cvDataSets'] = function() {
    $dataFiles = [
        'contact',
        'work',
        'volunteering',
        'skills',
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
    return $app['twig']->render('cv.twig', $app['cvDataSets']);
});


/**
 * @deprecated
 */
$app->get('/api/v1/cv', function () use ($app) {
    return $app->json($app['cvDataSets'], 200);
});

$app->get('/api/v2/', function () use ($app) {

    $halJson['_links']['self'] = ['href' => '/'];

    foreach ($app['cvDataSets'] as $dataSet => $data) {
        $halJson['_links'][$dataSet] = ['href' => sprintf('/%s', $dataSet)];
    }

    return $app->json($halJson, 200);
});

$app->get('/api/v2/{dataSet}', function ($dataSet) use ($app) {

    $halJson['_links']['self'] = ['href' => sprintf('/%s', $dataSet)];

    $halJson = array_merge($halJson, $app['cvDataSets'][$dataSet]);

    return $app->json($halJson, 200);
});

return $app;