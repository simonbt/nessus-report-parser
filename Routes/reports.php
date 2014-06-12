<?php
/**
 * nessus-report-parser -- reports.php
 * User: Simon Beattie
 * Date: 11/06/2014
 * Time: 12:39
 */

$app->get('/hosts/:reportId/:severity', function ($reportId, $severity) use($app, $reportData)
{
    $reportData = $reportData->getHosts($reportId, $severity);
    $app->render('reports/hosts.phtml', array('reportData' => $reportData));
});

$app->get('/descriptions/:reportId/:severity', function ($reportId, $severity) use($app, $reportData)
{
    $data = $reportData->getDescriptions($reportId, $severity);
    $app->render('reports/descriptions.phtml', array('reportData' => $data));
});

$app->get('/vulnerabilities/:reportId/:severity', function ($reportId, $severity) use($app, $reportData)
{
    $data = $reportData->getVulnerabilities($reportId, $severity);
    $app->render('reports/vulnerabilities.phtml', array('reportData' => $data));
});

$app->get('/pci/:reportId', function ($reportId) use($app, $reportData)
{
    $data = $reportData->getPCI($reportId);
    $app->render('reports/pci.phtml', array('reportData' => $data));
});

$app->get('/opendlp/:filename', function ($filename) use($app, $reportData)
{
    $reportData = $reportData->getOpenDLP($filename);
    $app->render('reports/opendlp.phtml', array('reportData' => $reportData));
});