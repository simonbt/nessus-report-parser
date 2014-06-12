<?php
/**
 * nessus-report-parser -- files.php
 * User: Simon Beattie
 * Date: 11/06/2014
 * Time: 12:41
 */

$app->post('/files/upload', function()
{
    $forms = new \Library\Forms();
    $tempName = $_FILES['uploadFile']['tmp_name'];
    $fileName = $_FILES['uploadFile']['name'];

    switch ($_POST['upload'])
    {
        case 'Upload Nessus XML':
            $forms->uploadNessus($tempName, $fileName);
            break;
        case 'Upload OpenDLP XML':
            $forms->uploadOpenDLP($tempName, $fileName);
            break;
    }
});

$app->post('/files/admin', function () use($import)
{
    $forms = new \Library\Forms();
    switch ($_POST['formSubmit'])
    {
        case 'Delete Nessus':
            $forms->deleteNessus($_POST['reports']);
            break;

        case 'Delete OpenDLP':
            $forms->deleteOpenDLP($_POST['reports']);
            break;

        case 'Merge':
            $forms->merge($_POST['reports']);
            break;

        case 'Import':
            $postReport = $_POST['reports'];
            $import->importNessusXML($_SESSION['userId'], $forms->import($postReport));
            break;
    }

});