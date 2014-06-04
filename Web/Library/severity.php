<?php
/**
 * nessus-report-parser -- severity.php
 * User: Simon Beattie
 * Date: 14/05/2014
 * Time: 13:29
 */

$severity = $_POST['severity'];

if ((is_numeric($severity)) && ($severity > 0 && $severity < 10.0))
{
    file_put_contents(__DIR__ . '/../severity', round($severity, 1));
} else {
    echo "failed";
}
