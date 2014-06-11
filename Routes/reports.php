<?php
/**
 * nessus-report-parser -- reports.php
 * User: Simon Beattie
 * Date: 11/06/2014
 * Time: 12:39
 */

$app->get('/hosts/:reportId/:severity', function ($reportId, $severity) use($reportData, $reportTemplates)
{
    $reportData = $reportData->getHosts($reportId, $severity);
    $reportTemplates->hosts($reportData);
});

$app->get('/descriptions/:reportId/:severity', function ($reportId, $severity) use($reportData, $reportTemplates)
{
    $reportData = $reportData->getDescriptions($reportId, $severity);
    $reportTemplates->descriptions($reportData);
});

$app->get('/vulnerabilities/:reportId/:severity', function ($reportId, $severity) use($reportData, $reportTemplates)
{
    $reportData = $reportData->getVulnerabilities($reportId, $severity);
    $reportTemplates->vulnerabilities($reportData);
});

$app->get('/pci/:reportId', function ($reportId) use($reportData, $reportTemplates)
{
    $reportData = $reportData->getPCI($reportId);
    $reportTemplates->pci($reportData);
});

$app->get('/opendlp/:filename', function ($filename) use($reportData, $reportTemplates)
{
    $reportData = $reportData->getOpenDLP($filename);
    $reportTemplates->openDLP($reportData);
});