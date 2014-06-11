<?php
/**
 * nessus-report-parser -- reports.php
 * User: Simon Beattie
 * Date: 11/06/2014
 * Time: 12:39
 */

$app->get('/hosts/:reportId/:severity', function ($reportId, $severity) use($app, $reportData, $reportTemplates)
{
    $reportData = $reportData->getHosts($reportId, $severity);
    $app->render('hosts.phtml', array('reportData' => $reportData));
});

$app->get('/descriptions/:reportId/:severity', function ($reportId, $severity) use($app, $reportData, $reportTemplates)
{
    $data = $reportData->getDescriptions($reportId, $severity);
    $app->render('descriptions.phtml', array('reportData' => $data));
});

$app->get('/test/:reportId/:severity', function ($reportId, $severity) use($app, $reportData, $reportTemplates)
{
    $data = $reportData->getVulnerabilities($reportId, $severity);
    $app->render('vulnerabilities.phtml', array('reportData' => $data));
});

$app->get('/test/:reportId', function ($reportId) use($app, $reportData, $reportTemplates)
{
    $data = $reportData->getPCI($reportId);
    $app->render('pci.phtml', array('reportData' => $data));
});

$app->get('/test/:filename', function ($filename) use($app, $reportData, $reportTemplates)
{
    $reportData = $reportData->getOpenDLP($filename);
    $app->render('opendlp.phtml', array('reportData' => $reportData));
});