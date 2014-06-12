<?php
/**
 * nessus-report-parser -- menus.php
 * User: Simon Beattie
 * Date: 11/06/2014
 * Time: 12:40
 */

$app->get('/', function() use($app)
{
    $app->render('menus/index.phtml', array());
});

$app->get('/nessus', function() use($app,$reportData, $config)
{
    $reportList = $reportData->listReports($_SESSION['userId']);
    $app->render('menus/nessusIndex.phtml',array('reports' => $reportList, 'severity' => $config['severity']));
});

$app->get('/opendlp', function () use($app)
{
    $files = new \Library\Files();
    $app->render('menus/openDlpIndex.phtml', array('reports' => $files->getOpenDlpList()));
});
