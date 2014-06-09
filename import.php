<?php
/**
 * ReportGenerator -- import.php
 * User: Simon Beattie @si_bt
 * Date: 14/04/2014
 * Time: 10:19
 */

if (isset($argv[1])) // Check that an argument has been given, if it has assume it is a Nessus report!
{
    $xml = $argv[1];
} else {
    die('You must provide report as argument');
}

spl_autoload_register(function ($className) {
    $fileName = __DIR__ . '/' . str_replace('\\', '/', $className) . '.php';

    if (!file_exists($fileName)) {
        return false;
    }

    require($fileName);
});

try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/reports.sqlite');
} catch (PDOException $pdoError) {
    echo $pdoError->getMessage();
    exit;
}

$report = new \Library\ImportReport($pdo); // Build report Object

echo "Creating report" . PHP_EOL;

echo "Completed creating report: " . $report->createReport($xml); // Output any return from report import.
