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


$app->post('/admin/adduser', function() use($app, $pdo)
{

    $email = $app->request()->post('email');
    $name = $app->request()->post('name');
    $password = $app->request()->post('password');
    $priv = $app->request()->post('priv');


    $users = new \Library\Users($pdo);

    $result = $users->createUser($name, $email, $password, $priv);

    $app->redirect('/admin/adduser?result='.$result);
});

$app->get('/admin/adduser', function() use($app)
{

    echo '
            <html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <link rel="stylesheet" type="text/css" href="/css/main.css">
            <title>Add User</title>
            </head>
            <div class="menu">
            <a href="/" onclick="loadingScreen()"><img src="/images/logo.png" alt="RandomStorm Limited" /></a>
            <h3>Add New User</h3>

            <p><form method="post">
                <input type="text" name="email" placeholder="Email Address"><br><br>
                <input type="text" name="name" placeholder="Full Name"><br><br>
                <input type="password" name="password" placeholder="Password"><br>
                <input type="text" name="priv" placeholder="Privilege Level"><br>
                <input type="submit" value="Add User"></form>

                <form action="/"><input type="submit" value="Cancel"></form>
';
    $success = $app->request()->get('result');
    if ($success == 'exists')
    {
            echo '<a class="red">That user already exists, please try again</a>';

    }
    elseif (is_numeric($success))
    {
        echo '<a href="/" class="green">User successfully added with ID: ' . $success . ' - Click here to return<a>';
    }

    echo '</div>';
});


$app->post('/admin/changepass', function() use($app, $pdo)
{

    $password = $app->request()->post('oldpass');
    $newPass = $app->request()->post('newpass');
    $repeatPass = $app->request()->post('repeat');

    $users = new \Library\Users($pdo);

    $result = $users->changeUserPass($_SESSION['email'], $_SESSION['userId'],$password, $newPass, $repeatPass);

    $app->redirect('/admin/changepass?result='.$result);
});

$app->get('/admin/changepass', function() use($app)
{

    echo '
            <html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <link rel="stylesheet" type="text/css" href="/css/main.css">
            <title>Password Change</title>
            </head>
            <div class="menu">
            <a href="/" onclick="loadingScreen()"><img src="/images/logo.png" alt="RandomStorm Limited" /></a>
            <h3>Password change</h3>

            <p><form method="post">
                <input type="password" name="oldpass" placeholder="Existing Password"><br><br>
                <input type="password" name="newpass" placeholder="New Password"><br>
                <input type="password" name="repeat" placeholder="Repeat Password"><br>
                <input type="submit" value="Change"></form>

                <form action="/"><input type="submit" value="Cancel"></form>
';
    $success = $app->request()->get('result');
    switch ($success)
    {
        case 'success':
            echo '<a href="/" class="green">Password changed successfully - Click to return to the menu</a>';
            break;
        case 'match':
            echo '<a class="red">Your passwords did not match, please try again</a>';
            break;
        case 'failed':
            echo '<a class="red">The password change failed, please try again</a>';
            break;
        case 'wrongPass':
            echo '<a class="red">You have entered the wrong password, please try again</a>';
            break;
    }
echo '</div>';
});

$app->post('/login', function() use($app, $pdo)
{
    $email = $app->request()->post('username');
    $password = hash('sha512', $app->request()->post('password'));

    $users = new \Library\Users($pdo);

    $userId = $users->checkUser($email, $password);
    if($userId)
    {
        $_SESSION['userId'] = $userId['id'];
        $_SESSION['email'] = $userId['email'];
        $_SESSION['name'] = $userId['name'];

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

            <p><form method="post"><input type="text" name="username" placeholder="Email Address"><input type="password" name="password" placeholder="Password"><input type="submit" value="Login"></form>
 ';
                if($badLogin)
    {
        echo '<strong class="red">You have entered an incorrect username and password!</strong>';
    }
   echo '         </div>';

})->setName('loginGet');












