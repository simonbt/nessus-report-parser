<?php
/**
 * nessus-report-parser -- reports.php
 * User: Simon Beattie
 * Date: 11/06/2014
 * Time: 12:39
 */

$app->get('/hosts/:reportId/:severity', function ($reportId, $severity) use($app, $reportData, $pdo)
{
    $users = new \Library\Users($pdo);

    //Sanitise
    $reportId = strip_tags($reportId);
    $severity = strip_tags($severity);

    $userCheck = $users->checkReportOwnership($reportId, $_SESSION['userId']);
    if (!$userCheck)
    {
        $app->render('reports/reportExists.phtml');
    }
    else
    {
        $reportData = $reportData->getHosts($reportId, $severity);
        $app->render('reports/hosts.phtml', array('reportData' => $reportData));
    }
});

$app->get('/descriptions/:reportId/:severity', function ($reportId, $severity) use($app, $reportData, $pdo)
{
    $users = new \Library\Users($pdo);

    //Sanitise
    $reportId = strip_tags($reportId);
    $severity = strip_tags($severity);

    $userCheck = $users->checkReportOwnership($reportId, $_SESSION['userId']);
    if (!$userCheck)
    {
        $app->render('reports/reportExists.phtml');
    }
    else
    {
        $data = $reportData->getDescriptions($reportId, $severity);
        $app->render('reports/descriptions.phtml', array('reportData' => $data));
    }
});

$app->get('/vulnerabilities/:reportId/:severity', function ($reportId, $severity) use($app, $reportData, $pdo)
{
    $users = new \Library\Users($pdo);

    //Sanitise
    $reportId = strip_tags($reportId);
    $severity = strip_tags($severity);

    $userCheck = $users->checkReportOwnership($reportId, $_SESSION['userId']);
    if (!$userCheck)
    {
        $app->render('reports/reportExists.phtml');
    }
    else
    {
        $data = $reportData->getVulnerabilities($reportId, $severity, $_SESSION['userId']);
        $app->render('reports/vulnerabilities.phtml', array('reportData' => $data));
    }
});

$app->get('/externals/:reportId/:severity', function ($reportId, $severity) use($app, $reportData, $pdo)
{
    $users = new \Library\Users($pdo);

    //Sanitise
    $reportId = strip_tags($reportId);
    $severity = strip_tags($severity);

    $userCheck = $users->checkReportOwnership($reportId, $_SESSION['userId']);
    if (!$userCheck)
    {
        $app->render('reports/reportExists.phtml');
    }
    else
    {
        $data = $reportData->getVulnerabilities($reportId, $severity, $_SESSION['userId']);
        $app->render('reports/externalVulnerabilities.phtml', array('reportData' => $data));
    }
});

$app->get('/pci/:reportId', function ($reportId) use($app, $reportData, $pdo)
{
    $users = new \Library\Users($pdo);

    //Sanitise
    $reportId = strip_tags($reportId);

    $userCheck = $users->checkReportOwnership($reportId, $_SESSION['userId']);
    if (!$userCheck)
    {
        $app->render('reports/reportExists.phtml');
    }
    else
    {
        $data = $reportData->getPCI($reportId);
        $app->render('reports/pci.phtml', array('reportData' => $data));
    }
});

$app->get('/opendlp/:filename', function ($filename) use($app, $reportData)
{
    //Sanitise
    $filename = strip_tags($filename);
    $userId = $_SESSION['userId'];

    $reportData = $reportData->getOpenDLP($filename, $userId);
    $app->render('reports/opendlp.phtml', array('reportData' => $reportData));
});

$app->get('/ports/:reportId/:severity', function ($reportId, $severity) use($app, $reportData, $pdo)
{
    $users = new \Library\Users($pdo);

    //Sanitise
    $reportId = strip_tags($reportId);
    $severity = strip_tags($severity);

    $userCheck = $users->checkReportOwnership($reportId, $_SESSION['userId']);
    if (!$userCheck)
    {
        $app->render('reports/reportExists.phtml');
    }
    else
    {
        $data = $reportData->getPorts($reportId, $severity, $_SESSION['userId']);
        $app->render('reports/ports.phtml', array('reportData' => $data));
    }
});

$app->get('/xml', function() use($app, $reportData)
{
    $xml = $reportData->loadXML(__DIR__ . '/../service-names-port-numbers.xml');

    echo '<pre>';
    print_r($xml);
});