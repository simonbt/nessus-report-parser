<?php
/**
 * nessus-report-parser -- main.php
 * User: Simon Beattie
 * Date: 11/06/2014
 * Time: 12:40
 */

$app->get('/', function() use($app)
{
    $app->render('index.phtml', array());
});

$app->get('/nessus', function() use($app,$reportData, $config)
{
    $reportList = $reportData->listReports($_SESSION['userId']);
    $app->render('nessusIndex.phtml',array('reports' => $reportList, 'severity' => $config['severity']));
});

$app->get('/opendlp', function () use($app)
{
    $files = new \Library\Files();
    $app->render('openDlpIndex.phtml', array('reports' => $files->getOpenDlpList()));
});
