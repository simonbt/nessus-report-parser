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
    $pdo = new \PDO(
        'mysql:host=' . $config['db']['hostname'] . ';dbname=' . $config['db']['database'],
        $config['db']['username'],
        $config['db']['password']
    );
} catch (\PDOException $pdoError)
{
    print $pdoError->getMessage() . PHP_EOL;
}


$app = new \Slim\Slim(array(
    'templates.path' => './views'
));

session_start();

$reportData = new \Library\ReportData($pdo);
$import = new \Library\Import($pdo);

include_once('Routes/authentication.php');
include_once('Routes/main.php');
include_once('Routes/reports.php');
include_once('Routes/files.php');

$app->run();
