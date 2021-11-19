<?php

error_reporting(E_ALL);

require '.././libs/Slim/Slim.php';

require_once '../includes/include.php';

require_once 'dbHelper.php';

\Slim\Slim::registerAutoloader();



$app = new \Slim\Slim(array(

    'debug' => true,

    'log.enabled' => true,

    'log.level' => \Slim\Log::DEBUG

    ));



function api_autoloader($class_name) {

    $directory = '../class/';

    if (file_exists($directory . strtolower($class_name) . '.class.php')) {

        require_once $directory . strtolower($class_name) . '.class.php';

        return;

    }

}



spl_autoload_register('api_autoloader');



$db = new db(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$app->get('/helloworld', function () use($app) {

    $response ["error"] = false;

    $response ['msg'] = "Welcome to TMS";

    echoResponse(200, $response);

});



//--------------------Category get-------------------------//

$app->get('/categoryGet', function() use($app) {

    $category = new Category();

    $result = $category->categoryGet();

    echoResponse(200, $result);

});

$app->get('/categoryDetail/:id', function($id) use($app) {

    $category = new Category();

    $result = $category->categoryDetail($id);

    echoResponse(200, $result);

});

$app->get('/categoryArticle/:id', function($id) use($app) {

    $category = new Category();

    $result = $category->categoryArticle($id);

    echoResponse(200, $result);

});

$app->get('/searchAllResult/:id', function($id) use($app) {

    $category = new Category();

    $result = $category->searchAllResult($id);

    echoResponse(200, $result);

});

$app->get('/searchResult', function() use($app) {

    $category = new Category();

    $result = $category->searchResult();

    echoResponse(200, $result);

});

$app->get('/searchMemberResult/:id', function($id) use($app) {

    $category = new Category();

    $result = $category->searchMemberResult($id);

    echoResponse(200, $result);

});

//---------------Dashboard Manage---------------------//

$app->get('/userBirthDate', function() use($app) {

    $dashboard = new dashboard();

    $result = $dashboard->userBirthDate();

    echoResponse(200, $result);

});

//--------------------News Manage--------------------//

$app->get('/newsGetAll', function() use($app) {

    $news = new news();

    $result = $news->newsGetAll();

    echoResponse(200, $result);

});

$app->get('/newsGetOne/:id', function($id) use($app) {

    $news = new news();

    $result = $news->newsGetOne($id);

    echoResponse(200, $result);

});

$app->get('/newsGetLatest', function() use($app) {

    $news = new news();

    $result = $news->newsGetLatest();

    echoResponse(200, $result);

});

//-------------------Calendar Event-----------------//

$app->get('/knowledgeEventGetAll', function() use($app) {

    $dashboard = new dashboard();

    $result = $dashboard->knowledgeEventGetAll();

    echoResponse(200, $result);

});

$app->get('/holidayGet/:id', function ($id) use ($app) {

    $dashboard = new dashboard ();

    $result = $dashboard->holidayGet($id);

    echoResponse(200, $result);

});

//-----------------Funny pic manage----------------//

$app->post('/reportimagesave', function () use($app) {

    $pic = new Funnypic();

    $data = json_decode($app->request->getBody(), TRUE);

    $result = $pic->reportsave($data);

    echoResponse($result ['status'], $result);

});

$app->post('/pictureSave', function () use($app) {

    $pic = new Funnypic();

    $data = json_decode($app->request->getBody(), TRUE);

    $result = $pic->pictureSave($data);

    echoResponse($result ['status'], $result);

});

$app->get('/pictureGet/:id/:limit', function($id, $limit) use($app) {
    $pic = new Funnypic();

    $result = $pic->pictureGet($id, $limit);

    echoResponse(200, $result);

});



$app->get('/chkUserLike/:pickId/:userId', function($pickId, $userId) use($app) {

    $pic = new Funnypic();

    $result = $pic->chkUserLike($pickId, $userId);

    echoResponse(200, $result);

});

$app->post('/pictureLikeSave', function () use($app) {

    $pic = new Funnypic();

    $data = json_decode($app->request->getBody(), TRUE);

    $result = $pic->pictureLikeSave($data);

    echoResponse($result ['status'], $result);

});

//----------------Funny pic comment---------------//

$app->post('/picCommentSave', function () use($app) {

    $pic = new Funnypic();

    $data = json_decode($app->request->getBody(), TRUE);

    $result = $pic->picCommentSave($data);

    echoResponse($result ['status'], $result);

});

$app->get('/pictureComentEdit/:id', function($id) {

    $pic = new Funnypic();

    $result = $pic->pictureComentEdit($id);

    echoResponse(200, $result);

});



$app->post('/picCommentUpdate', function () use($app) {

    $pic = new Funnypic();

    $data = json_decode($app->request->getBody(), TRUE);

    $result = $pic->picCommentUpdate($data);

    echoResponse(200, $result);

});



function echoResponse($status_code, $response) {

    global $app;

    $app->status($status_code);

    $app->contentType('application/json');

    echo json_encode($response, JSON_NUMERIC_CHECK);

}

$app->run();

?>