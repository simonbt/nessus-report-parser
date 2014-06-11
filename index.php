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


if (file_exists(__DIR__ . '/config.php'))
{
    $config = require(__DIR__ . '/config.php');
}
else
{
    die('Config.php does not exist - Please run install.sh');
}

try {
    $pdo = new PDO('sqlite:Database/reports.sqlite');
} catch (PDOException $pdoError) {
    echo $pdoError->getMessage();
    die('You may need to run install.sh to complete installation');
}

$app = new \Slim\Slim(array(
    'templates.path' => './views'
));

$reportData = new \Library\ReportData($pdo);
$reportTemplates = new \Library\ReportTemplates();
$common = new \Library\Common();
$import = new \Library\Import($pdo);


include_once('Routes/main.php');
include_once('Routes/reports.php');
include_once('Routes/files.php');

$app->run();
