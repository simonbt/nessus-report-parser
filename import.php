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
    $pdo = new PDO('sqlite:reports.sqlite');
} catch (PDOException $pdoError) {
    echo $pdoError->getMessage();
    exit;
}

$report = new \Library\ImportReport($pdo); // Build report Object

echo "Creating report" . PHP_EOL;

/**
 * @review I would suggest checking that the file existis and is readable at this point. It's easier and nicer to handle
 * errors at this low level that interacts directly with the CLI, than it is to throw an exception in the ImportReport lib
 */
print_r($report->createReport($xml)); // Output any return from report import.
