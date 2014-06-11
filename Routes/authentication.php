<?php
/**
 * nessus-report-parser -- authentication.php
 * User: Simon Beattie
 * Date: 11/06/2014
 * Time: 16:46
 */


$app->hook('slim.before.dispatch', function() use($app){

    $currentRoute = $app->router()->getCurrentRoute()->getName();

    if(!array_key_exists('userId', $_SESSION) && !in_array($currentRoute, ['loginGet', 'loginPost']))
    {
        $app->redirect('/login');
        return;
    }

    $app->userId = $_SESSION['userId'];

});


$app->post('/login', function() use($app, $pdo)
{
    $username = $app->request()->post('username');
    $password = hash('sha512', $app->request()->post('password'));

    $databaseModel = new \Library\DatabaseModel($pdo);

    $userId = $databaseModel->checkUser($username, $password);

    if($userId)
    {
        $_SESSION['userId'] = $userId;
        $app->redirect('/');
        return;
    }

    $app->redirect('/login?loggedIn=true');

})->setName('loginPost');


$app->get('/logout', function() use($app)
{
    session_destroy();
    $app->redirect('/');
});



$app->get('/login', function() use($app)
{
    $badLogin = $app->request()->get('loggedIn');

    if($badLogin)
    {
        echo '<strong>You have entered an incorrect username and password!</strong>';
    }

    echo '
            <html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <link rel="stylesheet" type="text/css" href="/css/main.css">
            <title>Please Login</title>
            </head>
            <div class="menu">
            <a href="/" onclick="loadingScreen()"><img src="/images/logo.png" alt="RandomStorm Limited" /></a>
            <h3>Please enter your login credentials</h3>
            username: randomstorm<br>
            password: password
            <form method="post"><input type="text" name="username" placeholder="Email Address"><input type="password" name="password" placeholder="Password"><input type="submit" value="Login"></form>
            </div>
            ';
})->setName('loginGet');


//$app->get('/reports', function() use($app)
//{
//    $this->getReportsbyUser($app->userId);
//});











