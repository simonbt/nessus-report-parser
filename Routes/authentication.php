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
    $users = new \Library\Users($pdo);

    $email = $app->request()->post('email');
    $name = $app->request()->post('name');
    $password = $app->request()->post('password');
    $priv = $app->request()->post('priv');

    $result = $users->createUser($name, $email, $password, $priv);
    $app->redirect('/admin/adduser?result='.$result);
});

$app->get('/admin/adduser', function() use($app)
{
    $app->render('adduser.phtml', array('app' => $app));
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
    $app->render('changePass.phtml', array('app' => $app));
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
    $app->render('login.phtml', array('app' => $app));
})->setName('loginGet');












