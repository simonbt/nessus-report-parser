<?php
/**
 * nessus-report-parser -- files.php
 * User: Simon Beattie
 * Date: 09/06/2014
 * Time: 15:41
 */

$uploadDirectory = __DIR__ . '/uploads';

include_once(__DIR__ . '/Library/Files.php');
$files = new \Library\files();
date_default_timezone_set('Europe/London');

$fileList = array_slice(scandir($uploadDirectory),2);

$fileArray = array();
foreach ( $fileList as $fileName )
{
    $fileArray[$fileName] = $files->encodeName($fileName);
}

echo '  <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <link rel="stylesheet" type="text/css" href="reports/main.css">
                <title>RandomStorm Report Generator</title>

                <script language="javascript">
                    function loadingScreen(){
                        document.body.innerHTML += \'<div class="loading">Loading&#8230;</div>\';
                    }
                </script>
            </head>
        <body>
        <div class="main">
        <div><a href="index.php" onclick="loadingScreen()"><img src="images/logo.png" alt="RandomStorm Limited" /></a></div>
        <div><p><a class="myButton" href="index.php" onclick="loadingScreen()">Return to the index</a></div>
        ';



//Checkboxes
print('
<form action="form.php" method="post">
    <h2>Stored Reports</h2>
');

print '<table>
        <tr>
            <td>Report Name</td>
            <td>Last Updated</td>
            <td>Select</td>
        </td>

';
foreach ($fileArray as $file => $hash)
{
    print'<tr>';
    print('
            <td> ' . $file . ' </td>
            <td>' . date ("d F Y H:i:s",filemtime($uploadDirectory .'/' . $file)) . '</td>
            <td><input type="checkbox" name="reports[]" value="' . $hash . '" /></td>
    ');
    print'</tr>';
}

print '</table>';

echo '
        <input type="submit" name="formSubmit" value="Delete"/>
        <input type="submit" name="formSubmit" value="Merge"/>
        <input type="submit" name="formSubmit" value="Import"/>
    </form>
</body>
</html>
';

//Upload

print('
<h2>Upload File</h2>
<form action="getfile.php" method="post" enctype="multipart/form-data">
  <input type="file" name="uploadFile" size="50" maxlength="25" />
  <input type="submit" name="upload" value="Upload!" />
</form>
</div>
');