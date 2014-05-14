<?php
/**
 * ReportGenerator -- config.php
 * User: Simon Beattie @si_bt
 * Date: 15/04/2014
 * Time: 12:16
 */

$url = "http://$_SERVER[SERVER_NAME]/reportAPI.php"; // Location of the reportAPI
$severity = file_get_contents(__DIR__ . "/severity"); // Minumum Severity to grab from the DB
