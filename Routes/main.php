<?php
/**
 * nessus-report-parser -- main.php
 * User: Simon Beattie
 * Date: 11/06/2014
 * Time: 12:40
 */

$app->get('/', function() use($reportData, $common, $config)
{
    $reportList = $reportData->listReports();
    $common->nessusIndex($reportList, $config['severity']);
});

$app->get('/opendlp', function () use($common)
{
    $common->openDlpIndex();
});

$app->get('/files', function ()
{
    $files = new \Library\Files();
    $files->fileIndex();
});