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

$app->get('/nessus', function() use($app,$reportData, $config, $pdo)
{
    $users = new \Library\Users($pdo);
    $userDetails = $users->getUserDetails($_SESSION['email']);

    $reportList = $reportData->listReports($_SESSION['userId']);
    $app->render('menus/nessusIndex.phtml',array('reports' => $reportList, 'severity' => $userDetails[0]['severity']));
});

$app->get('/opendlp', function () use($app)
{
    $files = new \Library\Files();
    $app->render('menus/openDlpIndex.phtml', array('reports' => $files->getOpenDlpList($_SESSION['userId'])));
});

$app->get('/ignored/shown', function () use($app, $reportData)
{
    $app->render('menus/shown.phtml', array('vulnerabilities' => $reportData->getShownVulnerabilities($_SESSION['userId'])));
});

$app->get('/ignored/hidden', function () use($app, $reportData)
{
    $app->render('menus/hidden.phtml', array('vulnerabilities' => $reportData->getIgnoredVulnerabilities($_SESSION['userId'])));
});

$app->get('/ignored/add/:pluginId', function($pluginId) use($app, $reportData)
{
    $result = $reportData->addIgnored($_SESSION['userId'], $pluginId);
    $app->redirect('/ignored/shown?added=' . $result);
});

$app->get('/ignored/remove/:pluginId', function($pluginId) use($app, $reportData)
{
    $result = $reportData->deleteIgnored($_SESSION['userId'], $pluginId);
    $app->redirect('/ignored/hidden?removed=' . $result);
});