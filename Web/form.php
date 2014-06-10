<?php
/**
 * nessus-report-parser -- form.php
 * User: Simon Beattie
 * Date: 09/06/2014
 * Time: 15:47
 */

include_once(__DIR__ . '/Library/Files.php');
$files = new \Library\files();
$uploadDirectory = __DIR__ . '/uploads/';

echo '  <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <link rel="stylesheet" type="text/css" href="reports/main.css">
                <title>RandomStorm Report Generator</title>

                <script language=\"javascript\">
                    function loadingScreen(){
                        document.body.innerHTML += \'<div class="loading">Loading&#8230;</div>\';
                    }
                </script>
            </head>
        <body>
        ';

if (count($_POST['reports']) == 0)
{
    echo 'You must select at least one report.';
    print '<p><a href="files.php">Return to menu</a>';
    die();
}

if ($_POST['formSubmit'] == 'Delete')
{
    if (count($_POST['reports']) > 1)
    {
        echo 'You can only delete one at a time!';
        print '<p><a href="files.php">Return to menu</a>';
    }
    else
    {
        foreach ($_POST['reports'] as $report)
        {
            echo "Deleting " . $files->decodeName($report);
            unlink(__DIR__ . '/uploads/' . $files->decodeName($report));
            print '<p>Report deleted successfully';
            print '<p><a href="files.php">Return to menu</a>';
        }
    }
}
elseif ($_POST['formSubmit'] == 'Merge')
{
    $mergeResults = $_POST['reports'];
    $toMerge = '';
    foreach ($mergeResults as $report)
    {
        $toMerge = $toMerge . ' ' .$uploadDirectory . $files->decodeName($report);

    }
    $command = 'python ' . __DIR__ . '/Library/merger.py ' . $files->decodeName($_POST['reports'][0]) . $files->rand_string(4) . '.merged '  . $toMerge . ' 2>&1';
    exec($command, $output, $return);
    if ( $return == 0)
    {
        print 'Reports merged successfully';
        print '<p><a href="files.php">Return to menu</a>';
    }
}
elseif ($_POST['formSubmit'] == 'Import')
{
    $mergeResults = $_POST['reports'];
    if (count($mergeResults) > 1)
    {
        echo "You can only import one report at a time";
        print '<p><a href="files.php">Return to menu</a>';
    }
    else
    {
        foreach ($mergeResults as $report)
        {
            $toMerge = $toMerge . ' ' .$uploadDirectory . $files->decodeName($report);

        }
        $command = 'php ' . __DIR__ . '/../import.php ' . $uploadDirectory . $files->decodeName($report);
        exec($command, $output, $return);



        if ( $return == 0)
        {
           foreach ($output as $line)
           {
               print $line .'<br>';
           }
           print '<p><a href="files.php">Return to menu</a>';
        }
    }

}
else
{
    echo "Invalid request";
}


