<?php
/**
 * nessus-report-parser -- files.php
 * User: Simon Beattie
 * Date: 11/06/2014
 * Time: 12:41
 */

$app->get('/openDlpMenu', function() use($app)
{
    $files = new \Library\Files();
    $app->render('files/openDlpFiles.phtml', array('reports' => $files->getOpenDlpList($_SESSION['userId']), 'app' => $app));
});

$app->post('/openDlpMenu/upload', function() use($app)
{
    $forms = new \Library\Forms();

    //Sanitise
    $tempName = strip_tags($_FILES['uploadFile']['tmp_name']);
    $fileName = strip_tags($_FILES['uploadFile']['name']);

    $result = $forms->uploadOpenDLP($tempName, $fileName, $_SESSION['userId']);
    $app->redirect('/openDlpMenu?upload='.$result);
});

$app->post('/openDlpMenu/admin', function () use ($app)
{
    $type = $app->request()->post('formSubmit');
    $reports = $app->request()->post('reports');
    $forms = new \Library\Forms();
    switch ($type)
    {
        case 'Delete OpenDLP':
            $result = $forms->deleteOpenDLP($reports, $_SESSION['userId']);
            $app->redirect('/openDlpMenu?admin='.$result);
            break;
    }
});





$app->get('/nessusMenu', function() use($app)
{
    $files = new \Library\Files();
    $app->render('files/nessusFiles.phtml', array('reports' => $files->getNessusList($_SESSION['userId']), 'app' => $app));
});

$app->post('/nessusMenu/upload', function() use($app)
{
    $forms = new \Library\Forms();

    if (!array_key_exists('uploadFile', $_FILES))
    {
        $result = 'failed';
        $app->redirect('/nessusMenu?upload='.$result);
        return;
    }

    $tempName = $_FILES['uploadFile']['tmp_name'];
    $fileName = $_FILES['uploadFile']['name'];
    $userId = $_SESSION['userId'];

    $result = $forms->uploadNessus($tempName, $fileName, $userId);
    $app->redirect('/nessusMenu?upload='.$result);

});


$app->post('/nessusMenu/admin', function () use($import, $app)
{

    //Sanitise
    $type = strip_tags($app->request()->post('formSubmit'));
    $reports = $app->request()->post('reports');
    $userId = $_SESSION['userId'];

    $forms = new \Library\Forms();
    switch ($type)
    {
        case 'Delete Nessus':
            $result = $forms->deleteNessus($reports, $userId);
            $app->redirect('/nessusMenu?admin='.$result);
            break;

        case 'Merge':
            $result = $forms->merge($reports, $userId);
            if ($result == 'none')
            {
                $app->redirect('/nessusMenu?admin='.$result);
            }
            else
            {
                $app->redirect('/nessusMenu?admin='.$result);
            }
            break;

        case 'Import':
            $xml = $forms->import($reports, $userId);
            if ($xml == 'none')
            {
                $app->redirect('/nessusMenu?admin='.$xml);
            }
            elseif ($xml == 'multiple')
            {
                $app->redirect('/nessusMenu?admin='.$xml);
            }
            else
            {
            $result = $import->importNessusXML($userId, $xml);
            $app->redirect('/nessusMenu?admin='.$result);
            }
            break;

    }

});