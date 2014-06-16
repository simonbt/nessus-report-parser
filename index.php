<?php
/**
 * nessus-report-parser -- RouteHandler.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 15:34
 */

include_once('Library/bootstrap.php');

$app = new \Slim\Slim(array(
    'templates.path' => './views'
));

session_start();

$reportData = new \Library\ReportData($pdo);
$import = new \Library\Import($pdo);

include_once('Routes/users.php');
include_once('Routes/menus.php');
include_once('Routes/reports.php');
include_once('Routes/files.php');

$app->run();
