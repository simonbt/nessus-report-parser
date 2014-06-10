<?php
/**
 * nessus-report-parser -- RouteHandler.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 15:34
 */

require 'vendor/autoload.php';
spl_autoload_register(function($className)
{
    $fileName = __DIR__ . '/' . str_replace('\\', '/', $className) . '.php';

    if(!file_exists($fileName))
    {
        return false;
    }

    require($fileName);
});

\Slim\Slim::registerAutoloader();
$config = require(__DIR__ . '/config.php');

try {
    $pdo = new PDO('sqlite:Database/reports.sqlite');
} catch (PDOException $pdoError) {
    echo $pdoError->getMessage();
    exit;
}

$app = new \Slim\Slim();
$reportData = new \Library\ReportData($pdo);
$reportTemplates = new \Library\ReportTemplates();
$common = new \Library\Common();
$files = new \Library\Files();
$forms = new \Library\Forms();
$import = new \Library\Import($pdo);

$app->get('/', function() use($reportData, $common, $config)
{
    $reportList = $reportData->listReports();
    $common->nessusIndex($reportList, $config['severity']);
});

$app->get('/opendlp', function () use($common)
{
    $common->openDlpIndex();
});

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

$app->get('/files', function () use($files)
{
    $files->fileIndex();
});

$app->post('/files/upload', function() use($forms)
{
    $tempName = $_FILES['uploadFile']['tmp_name'];
    $fileName = $_FILES['uploadFile']['name'];

    switch ($_POST['upload'])
    {
        case 'Upload Nessus XML':
            $forms->uploadNessus($tempName, $fileName);
            break;
        case 'Upload OpenDLP XML':
            $forms->uploadOpenDLP($tempName, $fileName);
            break;
    }
});

$app->post('/files/admin', function () use($forms, $import)
{

    switch ($_POST['formSubmit'])
    {
        case 'Delete Nessus':
            $forms->deleteNessus($_POST['reports']);
            break;

        case 'Delete OpenDLP':
            $forms->deleteOpenDLP($_POST['reports']);
            break;

        case 'Merge':
            $forms->merge($_POST['reports']);
            break;

        case 'Import':
            $postReport = $_POST['reports'];
            $import->importNessusXML($forms->import($postReport));
        break;
    }

});

$app->run();
