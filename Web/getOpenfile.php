<?php
/**
 * nessus-report-parser -- getOpenfile.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 13:08
 */


print'
<html>
<head>
<title>Process Uploaded File</title>
</head>
<body>
';

if ( move_uploaded_file ($_FILES['uploadFile'] ['tmp_name'],
    __DIR__ . "/opendlpUploads/{$_FILES['uploadFile'] ['name']}")  )
{  print '<p> The file has been successfully uploaded </p>';
    print '<a href="files.php">Return to menu</a>';
}
else
{
    print 'The File has failed to upload';
    print '<p><a href="files.php">Return to menu</a></p>';

}
print'
</body>
</html>
';