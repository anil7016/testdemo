<?php

error_reporting(E_ALL);
require '.././libs/Slim/Slim.php';
require_once '../includes/include.php';
require_once 'dbHelper.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
    'debug' => true,
    'log.enabled' => true,
    'log.level' => \Slim\Log::DEBUG));

function api_autoloader($class_name) {
     $directory = '../class/';
     
     if (file_exists($directory . strtolower($class_name) . '.class.php')) {
         require_once $directory . strtolower($class_name) . '.class.php';
         return;
     }
     
}

//spl_autoload_register(function ($class) {
//    echo $class;
//    require_once '../class/'. $class . '.class.php';
//});

spl_autoload_register('api_autoloader');

/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = getallheaders();
    //echo '<pre>'; print_r($headers); echo '</pre>'; exit;
    $response = array();
    $app = \Slim\Slim::getInstance();
    

    //By Pass /resetpassword request from authenticate function
    $req = $app->request;
    $req->getResourceUri();
    if($req->getResourceUri() === '/resetpassword'){

    }else{
        // Verifying Authorization Header
    if (isset($headers ['Authorization'])) {
        $authenticate = new authenticate ();
        // get the api key
        $api_key = $headers ['Authorization'];
        
        if (isset($headers ['timezone'])) {
            $timezone = $headers ['timezone'];
            date_default_timezone_set($timezone);
            db::getInstance()->rawQuery("SET time_zone = '$timezone'");
        }
        
        if (isset($headers ['sourcetype'])) {
            global $sourcetype;
            $sourcetype = $headers ['sourcetype'];
        }
        
        // validating api key
        if (!$authenticate->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response ["error"] = true;
            $response ["msg"] = "Access Denied.Invalid Api key";
            echoResponse(401, $response);
            $app->stop();
        } else {
            global $user_id;
            global $userData;
            // get user primary key id
            $user_id = $authenticate->getUserId($api_key);
            $userData = $authenticate->getUser($user_id);
        }
        // Replace sql mode - To solve issue of join and insert query
        db::getInstance()->rawQuery("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'STRICT_TRANS_TABLES',''))");
        db::getInstance()->rawQuery("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'NO_ZERO_IN_DATE',''))");
        db::getInstance()->rawQuery("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'NO_ZERO_DATE',''))");
        db::getInstance()->rawQuery("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ERROR_FOR_DIVISION_BY_ZERO',''))");
        db::getInstance()->rawQuery("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        
    } else {
        // api key is missing in header
        $response ["error"] = true;
        $response ["header"] = $headers;
        $response ["msg"] = "Api key is misssing";
        echoResponse(401, $response);
        $app->stop();
    }
    }
    

    
}

$db = new db(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$app->get('/helloworld', 'authenticate', function () use($app) {
    $response ["error"] = false;
    $response ['msg'] = "Welcome to TMS";
    echoResponse(200, $response);
});
// ---------------- login & reset password section----------------------//
$app->post('/authenticate', function () use($app) {
   
    
    $user = new users ();
   
    $data = json_decode($app->request->getBody(), TRUE);
   
    $result = $user->authenticate($data);
    echoResponse($result ['status'], $result);
});
$app->post('/resetpassword','authenticate', function () use($app) {
    $user = new users ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $user->resetPassword($data);
    echoResponse($result ['status'], $result);
});
$app->post('/resetpassword1', function () use($app) {
    $user = new users ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $user->resetPassword1($data);
    echoResponse($result ['status'], $result);
});

$app->put('/userUpdate_Byid/:id','authenticate', function ($id) use($app) {
    $user = new users ();
    $data = json_decode($app->request->getBody());
    $result = $user->userUpdate_Byid($data);
    echoResponse($result ['status'], $result);
});

//------------------communication profile start----------------//
$app->post('/saveuserprofileinternal','authenticate', function () use($app) {
    $user = new users ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $user->saveuserprofile($data);
    echoResponse($result ['status'], $result);
});
$app->put('/saveuserprofileinternal/:id','authenticate', function ($id) use($app) {
    $user = new users ();
    $data = json_decode($app->request->getBody());
    $result = $user->saveuserprofileinternalupdate($id, (array) $data);
    echoResponse($result ['status'], $result);
});
$app->post('/saveuserprofileexternelS','authenticate', function () use($app) {
    $user = new users ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $user->saveuserprofileexternelS($data);
    echoResponse($result ['status'], $result);
});
$app->put('/saveuserprofileexternel/:id','authenticate', function ($id) use($app) {
    $user = new users ();
    $data = json_decode($app->request->getBody());
    $result = $user->saveuserprofileexternelupdate($id, (array) $data);
    echoResponse($result ['status'], $result);
});
$app->get('/getProfile/:id','authenticate', function ($id) use($app) {
    $user = new users ();
    $result = $user->getUserByField($id);
    if (isset($result) && !empty($result)) {
        echoResponse(200, $result [0]);
    } else {
        echoResponse(200, $result);
    }
});
$app->put('/saveuserprofile/:id','authenticate', function ($id) use($app) {
    $user = new users ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $user->updateprofile($id, $data);
    echoResponse($result ['status'], $result);
});
$app->post('/ContactAdd','authenticate', function () use($app) {
    $user = new users ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $user->ContactAdd($data);
    echoResponse($result ['status'], $result);
});
$app->get('/userProfileNumber/:id','authenticate', function ($id) use($app) {
    $user = new users ();
    $result = $user->userProfileNumberGet($id);
    echoResponse(200, $result);
});
$app->get('/getAlluserGroup','authenticate', function () {
    $user = new users ();
    $result = $user->getAlluserGroup();
    echoResponse(200, $result);
});

// ---------------------additional info section -------------------------//
$app->post('/additionalinfo','authenticate', function () use($app) {
    $profile = new profile ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $profile->saveInfo($data);
    echoResponse($result ['status'], $result);
});
$app->get('/getAddinfo/:id/:type','authenticate', function ($id, $type) use($app) {
    $profile = new profile ();
    $result = $profile->getAddtionalInfo($id, $type);
    echoResponse(200, $result);
});
$app->put('/additionalinfo/:id','authenticate', function ($id) use($app) {
    $profile = new profile ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $profile->updateAddtionalInfo($id, $data);
    echoResponse($result ['status'], $result);
});
// ---------------------payment section -------------------------//
$app->post('/paymentsave','authenticate', function () use($app) {
    $payment = new payment ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $payment->save($data);
    echoResponse($result ['status'], $result);
});
$app->get('/getuserpayment/:id/:type','authenticate', function ($id, $type) use($app) {
    $payment = new payment ();
    $result = $payment->getPaymentById($id, $type);
    echoResponse(200, $result);
});
$app->post('/paymentsave/:id/:type','authenticate', function ($id, $type) use($app) {
    $payment = new payment ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $payment->updatePayment($id, $type, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/getuserpaymentdata/:id','authenticate', function ($id) use($app) {
    $payment = new payment ();
    $result = $payment->getuserpaymentdata($id);
    echoResponse(200, $result);
});
$app->get('/getClientpayment/:id','authenticate', function ($id) use($app) {
    $payment = new payment ();
    $result = $payment->getClientpayment($id);
    echoResponse(200, $result);
});
$app->post('/paymentdirectUpdate/:id/:type','authenticate', function ($id, $type) use($app) {
    $payment = new payment ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $payment->paymentdirectUpdate($id, $type, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/getVatcount/:countryCode/:vat', function ($countryCode, $vat) use($app) {
    $payment = new payment ();
    $result = $payment->getVatcount($countryCode, $vat);
    echoResponse(200, $result);
});

$app->get('/getUserDataById/:id','authenticate', function ($id) use($app) {
    $user = new users ();
    $result = $user->getUserDataById($id);
    echoResponse(200, $result);
});
// ---------------------Task section -------------------------//
$app->post('/tasksave','authenticate', function () use($app) {
    $task = new task ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $task->save($data);
    echoResponse($result ['status'], $result);
});
$app->get('/getTask/:id','authenticate', function ($id) use($app) {
    $task = new task ();
    $result = $task->getTaskById($id);
    echoResponse(200, $result);
});
$app->get('/usertask/:id/:type','authenticate', function ($id, $type) use($app) {
    $task = new task ();
    $result = $task->getTaskByUser($id, $type);
    echoResponse($result ['status'], $result);
});
$app->put('/tasksave/:id','authenticate', function ($id) use($app) {
    $task = new task ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $task->updateTask($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/deleteTask/:id','authenticate', function ($id) use($app) {
    $task = new task ();
    $result = $task->deleteTask($id);
    echoResponse($result ['status'], $result);
});
$app->get('/usertaskodueDategate/:id','authenticate', function ($id) use($app) {
    $task = new task ();
    $result = $task->usertaskodueDategate($id);
    echoResponse(200, $result);
});
$app->get('/projectNumberget/:id','authenticate', function ($id) use($app) {
    $task = new task ();
    $result = $task->projectNumberget($id);
    echoResponse(200, $result);
});
$app->get('/projectTaskContactPer/:id','authenticate', function ($id) use($app) {
    $task = new task ();
    $result = $task->projectTaskContactPer($id);
    echoResponse(200, $result);
});
// ------------------ user section -------------------------//
$app->post('/checkusername','authenticate', function () use($app) {
    $user = new users ();
    $result = $user->checkusername($app->request->getBody());
    echoResponse($result ['status'], $result);
});
$app->post('/checkemailaddress','authenticate', function () use($app) {
    $user = new users ();
    $result = $user->checkemailaddress($app->request->getBody());
    echoResponse($result ['status'], $result);
});
$app->get('/users','authenticate', function () use($app) {
    $user = new users ();
    $result = $user->userlist();
    echoResponse($result ['status'], $result);
});
$app->get('/user/:type','authenticate', function ($type) use($app) {
    $user = new users ();
    $result = $user->userwithType($type);
    echoResponse($result ['status'], $result);
});
$app->get('/userQaSpecialist/:type','authenticate', function ($type) use($app) {
    $user = new users ();
    $result = $user->userQaSpecialist($type);
    echoResponse($result ['status'], $result);
});
$app->get('/userManager/:type','authenticate', function ($type) use($app) {
    $user = new users ();
    $result = $user->userManager($type);
    echoResponse($result ['status'], $result);
});
$app->get('/userCoordinator/:type','authenticate', function ($type) use($app) {
    $user = new users ();
    $result = $user->userCoordinator($type);
    echoResponse($result ['status'], $result);
});
$app->delete('/deleteUser/:id/:image','authenticate', function ($id, $image) use($app) {
    $user = new users ();
    $result = $user->deleteUser($id, $image);
    echoResponse($result ['status'], $result);
});
$app->get('/userGetmessage/:id','authenticate', function ($id) use($app) {
    $user = new users ();
    $result = $user->userlist();
    echoResponse($result ['status'], $result);
});
$app->get('/clientlistindirectGet/:id','authenticate', function ($id) use($app) {
    $user = new users ();
    $result = $user->clientlistindirectGet($id);
    echoResponse($result ['status'], $result);
});
$app->get('/userExternalGet/:id','authenticate', function ($id) use($app) {
    $user = new users ();
    $result = $user->userExternalGet($id);
    echoResponse($result ['status'], $result);
});
$app->get('/messageUserOneget/:id','authenticate', function ($id) use($app) {
    $user = new users ();
    $result = $user->messageUserOneget($id);
    echoResponse($result ['status'], $result);
});
$app->get('/viewExternalget/:id','authenticate', function ($id) use($app) {
    $user = new users ();
    $result = $user->viewExternalget($id);
    echoResponse(200, $result);
});
$app->get('/cityTimeZoneget/:id','authenticate', function ($id) use($app) {
    $user = new users ();
    $result = $user->cityTimeZoneget($id);
    echoResponse(200, $result);
});

$app->post('/getTimeZoneByLatLong','authenticate', function () use($app) {
    $user = new users ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $user->getTimeZoneByLatLong($data);
    echoResponse(200, $result);
});
// ------------------user type section ---------------------//
$app->get('/usertype','authenticate', function () {
    $usertype = new usertype ();
    $result = $usertype->getAll();
    echoResponse(200, $result);
});
$app->get('/defaultUser/:id','authenticate', function ($id) {
    $usertype = new usertype ();
    $result = $usertype->getDefaultType($id);
    echoResponse(200, $result);
});
$app->get('/defaultUser1','authenticate', function () {
    $usertype = new usertype ();
    $result = $usertype->getDefaultType1();
    echoResponse(200, $result);
});
$app->get('/defaultUser2','authenticate', function () {
    $usertype = new usertype ();
    $result = $usertype->getDefaultType2();
    echoResponse(200, $result);
});

$app->get('/usertype/:id','authenticate', function ($id) use($app) {
    $usertype = new usertype ();
    $result = $usertype->getTypeById($id);
    echoResponse(200, $result);
});
$app->get('/typebyresource/:id','authenticate', function ($id) use($app) {
    $usertype = new usertype ();
    $result = $usertype->getTypeByResource($id);
    echoResponse(200, $result);
});

$app->post('/internalResourceCheck','authenticate', function () use($app) {
    $user = new users ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $user->internalResourceCheck($data);
    echoResponse(200, $result);
});
$app->post('/saveusertype','authenticate', function () use($app) {
    $usertype = new usertype ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $usertype->save($data);
    echoResponse($result ['status'], $result);
});
$app->post('/updateusertype/:id','authenticate', function ($id) use($app) {
    $usertype = new usertype ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $usertype->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/deleteType/:id','authenticate', function ($id) use($app) {
    $usertype = new usertype ();
    $result = $usertype->delete($id);
    echoResponse($result ['status'], $result);
});

// ----------------client section -----------------------//
$app->get('/clients', 'authenticate',function () {
    $client = new client ();
    $result = $client->clientlist();
    echoResponse(200, $result);
});

$app->post('/clientIndirectCheck', function () use($app) {
    $client = new client ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->clientIndirectCheckList($data);
    echoResponse(200, $result);
});

$app->post('/clientBasicIdCheck', 'authenticate',function () use($app) {
    $client = new client ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->clientBasicIdCheck($data);
    echoResponse(200, $result);
});
// $app->get ( '/clientIndirectdataCheck', function () {
// $client = new client ();
// $result = $client->clientIndirectdataCheck();
// echoResponse ( 200, $result );
// } );

$app->get('/clientlistindirect_show','authenticate', function () {
    $client = new client ();
    $result = $client->clientlistindirect_show();
    echoResponse(200, $result);
});

$app->get('/client/:id','authenticate', function ($id) use($app) {
    $client = new client ();
    $result = $client->getClientId($id);
    echoResponse(200, $result);
});

$app->put('/updateClientId/:id','authenticate', function ($id) use($app) {
    $client = new client ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->updateClientId($data);
    echoResponse($result ['status'], $result);
});

$app->put('/basicClientUpdated/:id','authenticate', function ($id) use($app) {
    $client = new client ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->basicClientUpdated($data);
    echoResponse($result ['status'], $result);
});

$app->put('/clientdata/:id','authenticate', function ($id) use($app) {
    $client = new client ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->clientIddata($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/client_indirect_update/:id', 'authenticate',function ($id) use($app) {
    $client = new client ();
    $result = $client->client_indirect_update($id);
    echoResponse(200, $result);
});
$app->get('/clientdirect_login_details/:id','authenticate', function ($id) use($app) {
    $client = new client ();
    $result = $client->clientdirect_login_details($id);
    // print_r($result);exit;
    echoResponse(200, $result);
});
$app->get('/clientdirect_login_getone/:id', 'authenticate',function ($id) use($app) {
    $client = new client ();
    $result = $client->clientdirect_login_getone($id);
    // print_r($result);exit;
    echoResponse(200, $result);
});
$app->post('/clientsave','authenticate', function () use($app) {
    $client = new client ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->saveClient($data);
    echoResponse($result ['status'], $result);
});
$app->post('/directclientlogin','authenticate', function () use($app) {
    $client = new client ();
    $data = json_decode($app->request->getBody(), TRUE);
    // print_r($data);exit;
    $result = $client->saveClientdirectlogin($data);
    echoResponse($result ['status'], $result);
});
$app->post('/clientsaveindirect','authenticate', function () use($app) {
    $client = new client ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->clientsaveindirect($data);
    echoResponse($result ['status'], $result);
});
$app->get('/IndirectCustomerFileCheck','authenticate', function () use($app) {
    $client = new client ();
    $result = $client->IndirectCustomerFileCheck();
    // print_r($result);exit;
    echoResponse(200, $result);
});
// $app->post ( '/clientsaveindirect', function () use($app) {
// $client = new client ();
// $data = json_decode ( $app->request->getBody (), TRUE );
// $result = $client->clientsaveindirect ( $data );
// echoResponse ( $result ['status'], $result );
// } );
$app->put('/clientupdateindirect/:id','authenticate', function ($id) use($app) {

    $client = new client ();
    // echo $id;exit;
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->indirect_clientupdate($id, $data);
    echoResponse($result ['status'], $result);
});

$app->put('/IndirectClientUpdated/:id','authenticate', function ($id) use($app) {
    $client = new client ();
    // echo $id;exit;
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->indirect_UpdateClient_id($data);
    echoResponse($result ['status'], $result);
});

$app->put('/clientIndirectdata/:id','authenticate', function ($id) use($app) {

    $client = new client ();
    // echo $id;exit;
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->clientIndirectData($id, $data);
    echoResponse($result ['status'], $result);
});

$app->put('/update_directclientlogin/:id','authenticate', function ($id) use($app) {
    $client = new client ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->update_directclientlogin($id, $data);
    echoResponse($result ['status'], $result);
});

$app->put('/clientsave/:id', 'authenticate',function ($id) use($app) {
    $client = new client ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->updateClient($id, $data);
    echoResponse($result ['status'], $result);
});
$app->post('/checkclient', 'authenticate',function () use($app) {
    $client = new client ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->checkemailaddress($data);
    echoResponse($result ['status'], $result);
});
$app->delete('/clientdelete/:id/:image', function ($id, $image) {
    $client = new client ();
    $result = $client->deleteClient($id, $image);
    echoResponse(200, $result);
});
$app->delete('/deleteClient/:id','authenticate', function ($id) {
    $client = new client ();
    $result = $client->deleteClientdata($id);
    echoResponse($result ['status'], $result);
});
$app->delete('/deleteClientindirect/:id','authenticate', function ($id) {
    $client = new client ();
    $result = $client->deleteClientindirect($id);
    echoResponse($result ['status'], $result);
});

$app->get('/clientMessageGet/:id','authenticate', function ($id) use($app) {
    $client = new client ();
    $result = $client->clientMessageGet($id);
    // print_r($result);exit;
    echoResponse(200, $result);
});
$app->get('/clientIndirectMessageGet/:id','authenticate', function ($id) use($app) {
    $client = new client ();
    $result = $client->clientIndirectMessageGet($id);
    // print_r($result);exit;
    echoResponse(200, $result);
});
$app->get('/viewdirectdataget/:id','authenticate', function ($id) use($app) {
    $client = new client ();
    $result = $client->viewdirectdataget($id);
    echoResponse(200, $result);
});
$app->post('/clientFilemanager', 'authenticate',function () use($app) {
    $client = new client ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $client->clientFilemanager($data);
    echoResponse($result ['status'], $result);
});
$app->get('/clientProfileNumber/:id','authenticate', function ($id) use($app) {
    $client = new client ();
    $result = $client->clientProfileNumberGet($id);
    echoResponse(200, $result);
});
// -----------------client contact section -----------------//
$app->get('/editcontact/:id','authenticate', function ($id) use($app) {
    $contact = new contact ();
    $result = $contact->getContactById($id);
    echoResponse(200, $result);
});
$app->get('/contact/:id','authenticate', function ($id) use($app) {
    $contact = new contact ();
    $result = $contact->getContactByClientId($id);
    echoResponse(200, $result);
});
$app->post('/contactsave', 'authenticate',function () use($app) {
    $contact = new contact ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $contact->saveContact($data);
    echoResponse($result ['status'], $result);
});
$app->post('/contactsave/:id','authenticate', function ($id) use($app) {
    $contact = new contact ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $contact->updateContact($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/contactdelete/:id','authenticate', function ($id) {
    $contact = new contact ();
    $result = $contact->deleteContact($id);
    echoResponse($result ['status'], $result);
});
$app->post('/contactExternalsave/:id','authenticate', function ($id) use($app) {

    $contact = new contact ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $contact->contactExternalsave($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/contactExternalget/:id','authenticate', function ($id) use($app) {
    $contact = new contact ();
    $result = $contact->contactExternalget($id);
    echoResponse(200, $result);
});

$app->get('/contactExternalEdit/:id','authenticate', function ($id) use($app) {
    $contact = new contact ();
    $result = $contact->contactExternalEdit($id);
    echoResponse(200, $result);
});
$app->post('/contactExternalUpdate/:id','authenticate', function ($id) use($app) {
    $contact = new contact ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $contact->contactExternalUpdate($id, $data);
    echoResponse($result ['status'], $result);
});

$app->delete('/deleteExternalContact/:id','authenticate', function ($id) {
    $contact = new contact ();
    $result = $contact->deleteExternalContact($id);
    echoResponse($result ['status'], $result);
});
$app->get('/viewcontactdirectEdit/:id','authenticate', function ($id) use($app) {
    $contact = new contact ();
    $result = $contact->viewcontactdirectEdit($id);
    echoResponse(200, $result);
});
// ---------------- task type section --------------------//
$app->get('/tasktypeactive', 'authenticate',function () use($app) {
    $tasktype = new tasktype ();
    $result = $tasktype->getActive();
    echoResponse(200, $result);
});
$app->get('/tasktype', 'authenticate',function () use($app) {
    $tasktype = new tasktype ();
    $result = $tasktype->getAll();
    echoResponse(200, $result);
});
$app->get('/tasktype/:id','authenticate', function ($id) use($app) {
    $tasktype = new tasktype ();
    $result = $tasktype->getTypeById($id);
    echoResponse(200, $result);
});
$app->post('/tasktype','authenticate', function () use($app) {
    $tasktype = new tasktype ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $tasktype->save($data);
    echoResponse($result ['status'], $result);
});
$app->put('/tasktype/:id', 'authenticate',function ($id) use($app) {
    $tasktype = new tasktype ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $tasktype->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/tasktype/:id','authenticate', function ($id) {
    $tasktype = new tasktype ();
    $result = $tasktype->delete($id);
    echoResponse($result ['status'], $result);
});

// ------------------ user status -----------------------//
$app->get('/userstatusactive','authenticate', function () use($app) {
    $userstatus = new userstatus ();
    $result = $userstatus->getActive();
    echoResponse(200, $result);
});
$app->get('/clientstatusactive', 'authenticate',function () use($app) {
    $userstatus = new userstatus ();
    $result = $userstatus->getclientActive();
    echoResponse(200, $result);
});
$app->get('/statustype/:type','authenticate', function ($type) use($app) {
    $userstatus = new userstatus ();
    $result = $userstatus->getAll($type);
    echoResponse(200, $result);
});
$app->get('/userstatus/:id','authenticate', function ($id) use($app) {
    $userstatus = new userstatus ();
    $result = $userstatus->getTypeById($id);
    echoResponse(200, $result);
});
$app->post('/userstatus','authenticate', function () use($app) {
    $userstatus = new userstatus ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $userstatus->save($data);
    echoResponse($result ['status'], $result);
});
$app->put('/userstatus/:id','authenticate', function ($id) use($app) {
    $userstatus = new userstatus ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $userstatus->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/userstatus/:id','authenticate', function ($id) {
    $userstatus = new userstatus ();
    $result = $userstatus->delete($id);
    echoResponse($result ['status'], $result);
});

// ------------------ Project Type -----------------------//
$app->get('/prtypeactive','authenticate', function () use($app) {
    $projecttype = new projecttype ();
    $result = $projecttype->getActive();
    echoResponse(200, $result);
});
$app->get('/prtype','authenticate', function () use($app) {
    $projecttype = new projecttype ();
    $result = $projecttype->getAll();
    echoResponse(200, $result);
});
$app->get('/prtype/:id','authenticate', function ($id) use($app) {
    $projecttype = new projecttype ();
    $result = $projecttype->getTypeById($id);
    echoResponse(200, $result);
});
$app->post('/prtype','authenticate', function () use($app) {
    $projecttype = new projecttype ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $projecttype->save($data);
    echoResponse($result ['status'], $result);
});
$app->put('/prtype/:id','authenticate', function ($id) use($app) {
    $projecttype = new projecttype ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $projecttype->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/prtype/:id','authenticate', function ($id) {
    $projecttype = new projecttype ();
    $result = $projecttype->delete($id);
    echoResponse($result ['status'], $result);
});
//--------------------project status--------------------------//
$app->post('/prStatus','authenticate', function () use($app) {
    $projectstatus = new projectstatus ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $projectstatus->save($data);
    echoResponse($result ['status'], $result);
});
$app->get('/prstatus','authenticate', function () use($app) {
    $projectstatus = new projectstatus ();
    $result = $projectstatus->getAll();
    echoResponse(200, $result);
});
$app->get('/prStatus/:id','authenticate', function ($id) use($app) {
    $projectstatus = new projectstatus ();
    $result = $projectstatus->getTypeById($id);
    echoResponse(200, $result);
});
$app->put('/prStatus/:id','authenticate', function ($id) use($app) {
    $projectstatus = new projectstatus ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $projectstatus->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/prStatus/:id','authenticate', function ($id) {
    $projectstatus = new projectstatus ();
    $result = $projectstatus->delete($id);
    echoResponse($result ['status'], $result);
});
$app->get('/proStatusget','authenticate', function () use($app) {
    $projectstatus = new projectstatus ();
    $result = $projectstatus->proStatusget();
    echoResponse(200, $result);
});
$app->get('/proStatusgetOne','authenticate', function () use($app) {
    $projectstatus = new projectstatus ();
    $result = $projectstatus->proStatusgetOne();
    echoResponse(200, $result);
});
// ------------------currency section -----------------------//
$app->get('/currencyactive','authenticate', function () use($app) {
    $currency = new currency ();
    $result = $currency->getActive();
    echoResponse(200, $result);
});
$app->get('/currency','authenticate', function () use($app) {
    $currency = new currency ();
    $result = $currency->getAll();
    echoResponse(200, $result);
});
$app->get('/currency/:id','authenticate', function ($id) use($app) {
    $currency = new currency ();
    $result = $currency->getTypeById($id);
    echoResponse(200, $result);
});
$app->post('/currency','authenticate', function () use($app) {
    $currency = new currency ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $currency->save($data);
    echoResponse($result ['status'], $result);
});
$app->put('/currency/:id','authenticate', function ($id) use($app) {
    $currency = new currency ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $currency->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/currency/:id','authenticate', function ($id) {
    $currency = new currency ();
    $result = $currency->delete($id);
    echoResponse($result ['status'], $result);
});
$app->put('/currencyUpdate/:id','authenticate', function ($id) use($app) {
    $currency = new currency ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $currency->currencyUpdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/currencyCodeGet/','authenticate', function() {
    $currency = new currency ();
    $result = $currency->currencyCodeGet();
    echoResponse(200, $result);
});
$app->get('/currencyrateAutomaticaly/','authenticate', function() {
    $currency = new currency ();
    $result = $currency->currencyrateAutomaticaly();
    echoResponse(200, $result);
});
$app->get('/currencyrateAll/','authenticate', function() {
    $currency = new currency ();
    $result = $currency->currencyrateAll();
    echoResponse(200, $result);
});
$app->get('/CurrencyDatadefault','authenticate', function() {
    $currency = new currency ();
    $result = $currency->defaultCurrencyGet();
    echoResponse(200, $result);
});
// 2------------------property section -----------------------//
$app->get('/propertyactive/:priority','authenticate', function ($priority) use($app) {
    $property = new property ();
    $result = $property->getActive($priority);
    echoResponse(200, $result);
});
$app->get('/property','authenticate', function () use($app) {
    $property = new property ();
    $result = $property->getAll();
    echoResponse(200, $result);
});
$app->get('/property/:id','authenticate', function ($id) use($app) {
    $property = new property ();
    $result = $property->getTypeById($id);
    echoResponse(200, $result);
});
$app->get('/propertybypriority/:priority','authenticate', function ($priority) use($app) {
    $property = new property ();
    $result = $property->getByPriority($priority);
    echoResponse(200, $result);
});
$app->post('/property','authenticate', function () use($app) {
    $property = new property ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $property->save($data);
    echoResponse($result ['status'], $result);
});
$app->put('/property/:id','authenticate', function ($id) use($app) {
    $property = new property ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $property->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/property/:id','authenticate', function ($id) {
    $property = new property ();
    $result = $property->delete($id);
    echoResponse($result ['status'], $result);
});
$app->get('/generalPropertiesView/:id','authenticate', function ($id) use($app) {
    $property = new property ();
    $result = $property->generalPropertiesView($id);
    echoResponse(200, $result);
});
// ------------------properties values section -----------------------//
$app->get('/propertyvalues/:id','authenticate', function ($id) use($app) {
    $value = new value ();
    $result = $value->getValueByProperty($id);
    echoResponse(200, $result);
});
$app->get('/searchByCreteria/:property_id/:user_id/:type', function ($property_id, $user_id, $type) use($app) {
    $property = new property ();
    $result = $property->searchResult($property_id, $user_id, $type);
    echoResponse(200, $result);
});
$app->post('/values', 'authenticate',function () use($app) {
    $value = new value ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $value->save($data);
    echoResponse($result ['status'], $result);
});
$app->put('/values/:id', 'authenticate',function ($id) use($app) {
    $value = new value ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $value->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/values/:id', 'authenticate',function ($id) {
    $value = new value ();
    $result = $value->delete($id);
    echoResponse($result ['status'], $result);
});

// -------------user/client property --------------------//

$app->get('/getUserProperty/:id/:type','authenticate', function ($id, $type) use($app) {
    $property = new property ();
    $result = $property->getUserProperty($id, $type);
    echoResponse(200, $result);
});
$app->get('/getUserProperty/:id', 'authenticate',function ($id) use($app) {
    $property = new property ();
    $result = $property->userPropertyById($id);
    echoResponse(200, $result);
});
$app->post('/userProperty','authenticate', function () use($app) {
    $property = new property ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $property->addUserProperty($data);
    echoResponse(200, $result);
});
$app->put('/userProperty/:id','authenticate', function ($id) use($app) {
    $property = new property ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $property->updateUserProperty($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/deleteUserProperty/:id','authenticate', function ($id) use($app) {
    $property = new property ();
    $result = $property->deleteUserProperty($id);
    echoResponse($result ['status'], $result);
});
// -----------event creation ------------------------//

$app->get('/userevents/:id','authenticate', function ($id) use($app) {
    $event = new event ();
    $result = $event->getAllEvent($id);
    // echo $result;exit;
    echoResponse(200, $result);
});
$app->get('/userevents/', 'authenticate',function ($id) use($app) {
    $event = new event ();
    $result = $event->getAllEvent($id);
    // echo $result;exit;
    echoResponse(200, $result);
});
$app->post('/events','authenticate', function () use($app) {
    $event = new event ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $event->save($data);
    echoResponse(200, $result);
});
$app->put('/events/:id','authenticate', function ($id) use($app) {
    $event = new event ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $event->updateEvent($id, $data);

    echoResponse(200, $result);
});
$app->get('/events/:id', 'authenticate',function ($id) use($app) {
    $event = new event ();
    $result = $event->getAllEvent($id);
    echoResponse(200, $result);
});
$app->delete('/deleteEvent/:id','authenticate', function ($id) use($app) {
    $event = new event ();
    $result = $event->deleteEvent($id);
    echoResponse($result ['status'], $result);
});

// -----------jobevent creation ------------------------//

$app->get('/Jobuserevents/:id','authenticate', function ($id) use($app) {
    $event = new event ();
    $result = $event->getAlljobEvent($id);
    // echo $result;exit;
    echoResponse(200, $result);
});
// $app->get('/userevents/', function ($id) use($app) {
//     $event = new event ();
//     $result = $event->getAllEvent($id);
//     // echo $result;exit;
//     echoResponse(200, $result);
// });
$app->post('/jobevents', 'authenticate',function () use($app) {
    $event = new event ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $event->jobeventssave($data);
    echoResponse(200, $result);
});
$app->put('/jobevents/:id','authenticate', function ($id) use($app) {
    $event = new event ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $event->updatejobevents($id, $data);

    echoResponse(200, $result);
});
$app->get('/jobevents/:id', 'authenticate',function ($id) use($app) {
    $event = new event ();
    $result = $event->getAlljobEvent($id);
    echoResponse(200, $result);
});
$app->delete('/deletejobEvent/:id', 'authenticate',function ($id) use($app) {
    $event = new event ();
    $result = $event->deletejobEvent($id);
    echoResponse($result ['status'], $result);
});

// -------------working hour ----------------------//
$app->post('/workinghour', 'authenticate',function () use($app) {
    $workinghour = new workinghour ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $workinghour->save($data);
    echoResponse(200, $result);
});
$app->get('/workinghour/:user_id', 'authenticate',function ($user_id) use($app) {
    $workinghour = new workinghour ();
    $result = $workinghour->getworkingByUser($user_id);
    echoResponse(200, $result);
});
$app->put('/workinghour/:id', 'authenticate',function ($id) use($app) {
    $workinghour = new workinghour ();
    $data = json_decode($app->request->getBody(), TRUE);

    $result = $workinghour->update($id, $data);
    echoResponse($result ['status'], $result);
});
// -------------job working hour ----------------------//
$app->post('/jobworkinghour', 'authenticate',function () use($app) {
    $workinghour = new jobworkinghour ();
    $data = json_decode($app->request->getBody(), TRUE);
    // print_r($data);exit;
    $result = $workinghour->save($data);
    echoResponse(200, $result);
});
$app->get('/jobworkinghour/:order_id', 'authenticate',function ($order_id) use($app) {
    $workinghour = new jobworkinghour ();
    $result = $workinghour->getworkingByUser($order_id);
    echoResponse(200, $result);
});
$app->put('/jobworkinghour/:id', 'authenticate',function ($id) use($app) {
    $workinghour = new jobworkinghour ();
    $data = json_decode($app->request->getBody(), TRUE);

    $result = $workinghour->update($id, $data);
    echoResponse($result ['status'], $result);
});
// ------------------order section -------------------------------//
$app->post('/order', 'authenticate',function () use($app) {
    $order = new order ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $order->save($data);
    echoResponse(200, $result);
});
$app->get('/order/:orderId/:userId', 'authenticate',function ($orderId, $userId) use($app) {
    $order = new order ();
    $result = $order->getByOrderId($orderId, $userId);
    echoResponse(200, $result);
});

//get General Using order_id
$app->get('/getGeneralByOrderId/:id', 'authenticate',function ($id) use($app) {
    $general = new general();
    $result = $general->getGeneralByOrderId($id);
    echoResponse(200, $result);
});

$app->get('/orderGet/:id', 'authenticate',function ($id) use($app) {
    $order = new order ();
    $result = $order->getOrder($id);
    echoResponse(200, $result);
});
$app->delete('/deleteOrder/:id', 'authenticate',function ($id) {
    $order = new order ();
    $result = $order->delete($id);
    echoResponse($result ['status'], $result);
});
$app->get('/orderdataget/:id', 'authenticate',function ($id) use($app) {
    $order = new order ();
    $result = $order->orderdataget($id);
    echoResponse(200, $result);
});
$app->get('/orderupdatedataget/:id/:oldid','authenticate', function ($id, $oldid) use($app) {
    $order = new order ();
    $result = $order->orderupdatedataget($id, $oldid);
    echoResponse(200, $result);
});
$app->get('/orderNumberget/:id', 'authenticate',function ($id) use($app) {
    $order = new order ();
    $result = $order->orderNumberget($id);
    echoResponse(200, $result);
});
$app->get('/orderNumbergetupdate/:id','authenticate', function ($id) use($app) {
    $order = new order ();
    $result = $order->orderNumbergetupdate($id);
    echoResponse(200, $result);
});
$app->put('/order/:id', 'authenticate',function ($id) use($app) {
    $order = new order ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $order->orderupdate($id, $data);
    echoResponse(200, $result);
});
$app->get('/jobitemsGet/:id', 'authenticate',function ($id) use($app) {
    $order = new order ();
    $result = $order->jobitemsGet($id);
    echoResponse(200, $result);
});
$app->get('/orderItemCheck/:id','authenticate', function ($id) use($app) {
    $order = new order ();
    $result = $order->orderItemCheck($id);
    echoResponse(200, $result);
});
$app->put('/orderfileSave/:id', 'authenticate',function ($id) use($app) {
    $order = new order ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $order->orderfileSave($id, $data);
    echoResponse(200, $result);
});
$app->get('/jobItemQuantityget/:id/:itemId','authenticate', function ($id, $itemId) use($app) {
    $order = new order ();
    $result = $order->jobItemQuantityget($id, $itemId);
    echoResponse(200, $result);
});
$app->get('/orderEditedByUpdate/:id','authenticate', function ($id) use($app) {
    $order = new order ();
    $result = $order->orderEditedByUpdate($id);
    echoResponse(200, $result);
});
$app->get('/getOrderSingle/:id','authenticate', function ($id) use($app) {
    $order = new order ();
    $result = $order->getOrderSingle($id);
    echoResponse(200, $result);
});
// ------------------ project team section ------------------------//
$app->post('/team', 'authenticate',function () use($app) {
    $team = new team ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $team->save($data);
    echoResponse(200, $result);
});
$app->get('/team/:id', 'authenticate',function ($id) use($app) {
    $team = new team ();
    $result = $team->getByOrder($id);
    echoResponse(200, $result);
});
$app->put('/team/:id', 'authenticate',function ($id) use($app) {
    $team = new team ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $team->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/projectTeamget/:id','authenticate', function ($id) use($app) {
    $team = new team ();
    $result = $team->projectTeamget($id);
    echoResponse(200, $result);
});

// ------------------ project customer section ------------------------//
$app->post('/customer','authenticate', function () use($app) {
    $customer = new customer ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $customer->save($data);
    echoResponse(200, $result);
});
$app->get('/customer/:id', 'authenticate',function ($id) use($app) {
    $customer = new customer ();
    $result = $customer->getByorderID($id);
    echoResponse(200, $result);
});
$app->get('/viewProjectCustomerDetail/:id','authenticate', function ($id) use($app) {
    $customer = new customer ();
    $result = $customer->viewProjectCustomerDetail($id);
    echoResponse(200, $result);
});
$app->put('/customer/:id', 'authenticate',function ($id) use($app) {
    $customer = new customer ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $customer->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/customerContactPerson/:contactPerid/:orderId', 'authenticate',function ($contactPerid, $orderId) use($app) {
    $customer = new customer ();
    $result = $customer->customerContactPerson($contactPerid, $orderId);
    echoResponse(200, $result);
});
$app->get('/clientContactEmail/:clientId/:orderId','authenticate', function ($clientId, $orderId) use($app) {
    $customer = new customer ();
    $result = $customer->clientContactEmail($clientId, $orderId);
    echoResponse(200, $result);
});
$app->get('/customergroupeMail/:groupId/:orderId','authenticate', function ($groupId, $orderId) use($app) {
    $customer = new customer ();
    $result = $customer->customergroupeMail($groupId, $orderId);
    echoResponse(200, $result);
});

// ------------------ project general section ------------------------//
$app->post('/general','authenticate', function () use($app) {
    $general = new general ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $general->save($data);
    echoResponse(200, $result);
});
$app->get('/general/:id/:client_id','authenticate', function ($id, $client_id) use($app) {
    $general = new general ();
    $result = $general->getByorderID($id, $client_id);
    echoResponse(200, $result);
});
$app->get('/generalVieData/:id/:client_id','authenticate', function ($id, $client_id) use($app) {
    $general = new general ();
    $result = $general->generalVieData($id, $client_id);
    echoResponse(200, $result);
});
$app->put('/general/:id', 'authenticate',function ($id) use($app) {
    $general = new general ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $general->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->post('/generalEmailsend', 'authenticate',function () use($app) {
    $general = new general ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $general->generalEmailsend($data);
    echoResponse(200, $result);
});
$app->get('/generalDatawithoutClient/:id', 'authenticate',function ($id) use($app) {
    $general = new general ();
    $result = $general->generalDatawithoutClient($id);
    echoResponse(200, $result);
});
$app->get('/generalfolder/:id','authenticate', function ($id) use($app) {
    $general = new general ();
    $result = $general->generalfolder($id);
    echoResponse(200, $result);
});
// ------------------ project language section ------------------------//
$app->post('/prolanguage', 'authenticate',function () use($app) {
    $prolang = new prolang ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $prolang->save($data);
    echoResponse($result ['status'], $result);
});
$app->get('/prolanguage/:id','authenticate', function ($id) use($app) {
    $prolang = new prolang ();
    $result = $prolang->getByOrder($id);
    echoResponse(200, $result);
});
$app->put('/prolanguage/:id', 'authenticate',function ($id) use($app) {
    $prolang = new prolang ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $prolang->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/prolanguagegetOne/:id', 'authenticate',function ($id) use($app) {
    $prolang = new prolang ();
    $result = $prolang->prolanguagegetOne($id);
    echoResponse(200, $result);
});
$app->delete('/proLangdelete/:id','authenticate', function ($id) use($app) {
    $prolang = new prolang ();
    $result = $prolang->proLangdelete($id);
    echoResponse(200, $result);
});
// ------------------ language section ------------------------//
$app->get('/activelanguage', 'authenticate',function () use($app) {
    $language = new language ();
    $result = $language->getActive();
    echoResponse(200, $result);
});
$app->get('/language', 'authenticate',function () use($app) {
    $language = new language ();
    $result = $language->getAll();
    echoResponse(200, $result);
});
$app->get('/language/:id', 'authenticate',function ($id) use($app) {
    $language = new language ();
    $result = $language->getTypeById($id);
    echoResponse(200, $result);
});
$app->post('/language', 'authenticate',function () use($app) {
    $language = new language ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $language->save($data);
    echoResponse($result ['status'], $result);
});
$app->put('/language/:id', 'authenticate',function ($id) use($app) {
    $language = new language ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $language->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/language/:id','authenticate', function ($id) {
    $language = new language ();
    $result = $language->delete($id);
    echoResponse($result ['status'], $result);
});
$app->get('/activelanguageGet','authenticate', function () use($app) {
    $language = new language ();
    $result = $language->activelanguageGet();
    echoResponse(200, $result);
});
//----------------- Language Translation -----------------//
$app->post('/languageSave','authenticate', function () use($app) {
    $language = new language ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $language->languagesave($data);
    echoResponse($result ['status'], $result);
});
$app->get('/languagesGet','authenticate', function () {
    $language = new language ();
    $result = $language->languagesGetAll();
    echoResponse(200, $result);
});
$app->get('/LangsgetOne/:id','authenticate', function ($id) {
    $language = new language ();
    $result = $language->langsgetOne($id);
    echoResponse(200, $result);
});
$app->put('/langsupdate/:id','authenticate', function ($id) use($app) {
    $language = new language ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $language->langsupdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/deleteLangs/:id','authenticate', function ($id) {
    $language = new language ();
    $result = $language->deletelangs($id);
    echoResponse(200, $result);
});

// ---------------- product section----------------------//
$app->get('/products', 'authenticate',function () {
    $product = new product ();
    $result = $product->getAllProduct();
    echoResponse($result ['status'], $result);
});
$app->post('/products','authenticate', function () use($app) {
    $product = new product ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $product->AddProduct($data);
    echoResponse($result ['status'], $result);
});
$app->put('/products/:id','authenticate', function ($id) use($app) {
    $product = new product ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $product->updateProduct($id, $data);
    echoResponse($result ['status'], $result);
});

$app->delete('/products/:id','authenticate', function ($id) {
    $product = new product ();
    $result = $product->deleteProduct($id);
    echoResponse($result ['status'], $result);
});
// ---------------- tax status Section ------------------//
$app->get('/taxList', 'authenticate',function () {
    $tax = new tax ();
    $result = $tax->getAlltaxList();
    echoResponse(200, $result);
});
$app->get('/getTaxName/:id','authenticate', function ($id) {
    $tax = new tax ();
    $result = $tax->getTaxById($id);;
    echoResponse(200, $result);
});
$app->post('/taxStatus','authenticate', function () use($app) {
    $tax = new tax ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $tax->save($data);
    echoResponse($result ['status'], $result);
});
$app->get('/taxStatusget', 'authenticate',function () {
    $tax = new tax ();
    $result = $tax->getAlltax();
    echoResponse(200, $result);
});
$app->get('/taxstatusU/:id','authenticate', function ($id) use($app) {
    $tax = new tax ();
    $result = $tax->gettaxstatusU($id);
    echoResponse(200, $result);
});
$app->put('/taxstatus/:id','authenticate', function ($id) use($app) {
    $tax = new tax ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $tax->updateTaxStatus($id, $data);
    echoResponse($result ['status'], $result);
});

$app->delete('/taxStatusDelete/:id','authenticate', function ($id) {
    $tax = new tax ();
    $result = $tax->deleteTax($id);
    echoResponse($result ['status'], $result);
});

$app->get('/itemTaxget','authenticate', function() {
    $tax = new tax();
    $result = $tax->itemAlltaxget();
    echoResponse(200, $result);
});

// ---------------- Jobs Section ------------------//
$app->post('/Jobs','authenticate', function () use($app) {
    $job = new jobs ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $job->save($data);
    echoResponse($result ['status'], $result);
});
$app->post('/saveJobInstruction','authenticate', function () use($app) {
    $job = new jobs ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $job->saveJobInstruction($data);
    echoResponse(200, $result);
});
$app->put('/updateJobInstruction/:id','authenticate', function ($id) use($app) {
    $job = new jobs ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $job->updateJobInstruction($data, $id);
    echoResponse(200, $result);
});
$app->get('/Jobsget','authenticate', function () {
    $job = new jobs ();
    $result = $job->getAlljob();
    echoResponse(200, $result);
});
$app->get('/jobsget/:id','authenticate', function ($id) use($app) {
    $job = new jobs ();
    $result = $job->getjobOne($id);
    echoResponse(200, $result);
});
$app->put('/Jobs/:id','authenticate', function ($id) use($app) {
    $job = new jobs ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $job->updateJobs($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/jobsDelete/:id','authenticate', function ($id) {
    $job = new jobs ();
    $result = $job->deletejob($id);
    echoResponse($result ['status'], $result);
});
$app->get('/Jobsummeryget','authenticate', function () {
    $job = new jobs ();
    $result = $job->Jobsummeryget();
    echoResponse(200, $result);
});
$app->delete('/DeleteJobInstruction/:id','authenticate', function ($id) {
    $job = new jobs ();
    $result = $job->DeleteJobInstruction($id);
    echoResponse($result ['status'], $result);
});
$app->get('/selectWorkInstruction/:id','authenticate', function ($id) {
    $job = new jobs ();
    $result = $job->selectWorkInstruction($id);
    echoResponse(200, $result);
});
$app->get('/selectWorkInstructs','authenticate', function () {
    $job = new jobs ();
    $result = $job->selectWorkInstructs();
    echoResponse(200, $result);
});
// ---------------- Item-status Section ------------------//
$app->post('/itemStatus','authenticate', function () use($app) {
    $item = new item_status ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $item->save($data);
    echoResponse($result ['status'], $result);
});
$app->get('/ItemStatusget', 'authenticate',function () {
    $item = new item_status ();
    $result = $item->getAllItem();
    echoResponse(200, $result);
});
$app->get('/ItemStatusget/:id','authenticate', function ($id) use($app) {
    $item = new item_status ();
    $result = $item->getitemOne($id);
    echoResponse(200, $result);
});
$app->put('/itemStatus/:id','authenticate', function ($id) use($app) {
    $item = new item_status ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $item->updateItem($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/ItemsDelete/:id','authenticate', function ($id) {
    $item = new item_status ();
    $result = $item->deleteItem($id);
    echoResponse($result ['status'], $result);
});

// ---------------- Item-status Section ------------------//
$app->put('/sendMessage/:id','authenticate', function ($id) use($app) {
    $mail = new message ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $mail->send_email($id, $data);
    echoResponse($result ['status'], $result);
});
$app->put('/sendClientMessage/:id','authenticate', function ($id) use($app) {
    $mail = new messageClient ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $mail->sendClientMessage($id, $data);
    echoResponse($result ['status'], $result);
});
$app->put('/sendClientIndirectMessage/:id','authenticate', function ($id) use($app) {
    $mail = new messageClient ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $mail->sendClientIndirectMessage($id, $data);
    echoResponse($result ['status'], $result);
});
// ------------------- Price List -------------------------//
$app->post('/priceListSave','authenticate', function () use($app) {
    $price = new pricelist ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $price->priceListSave($data);
    echoResponse($result ['status'], $result);
});
$app->get('/PriceListDirectget/:id','authenticate', function ($id) {
    $price = new pricelist ();
    $result = $price->PriceListDirectAllget($id);
    echoResponse(200, $result);
});
$app->get('/PriceListDirectEditgetone/:id','authenticate', function ($id) {
    $price = new pricelist ();
    $result = $price->PriceListDirectEditgetone($id);
    echoResponse(200, $result);
});
$app->put('/priceListUpdate/:id', 'authenticate',function ($id) use($app) {
    $price = new pricelist ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $price->priceListUpdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/deletePricelist/:id','authenticate', function ($id) {
    $price = new pricelist ();
    $result = $price->deletePricelist($id);
    echoResponse($result ['status'], $result);
});
$app->get('/PriceListExternalget/:id','authenticate', function ($id) {
    $price = new pricelist ();
    $result = $price->PriceListExternalAllget($id);
    echoResponse(200, $result);
});
$app->get('/PriceListExternalEditgetone/:id','authenticate', function ($id) {
    $price = new pricelist ();
    $result = $price->PriceListExternalEditgetone($id);
    echoResponse(200, $result);
});

$app->post('/priceExternalListSave', 'authenticate',function () use($app) {
    $price = new pricelist ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $price->priceExternalListSave($data);
    echoResponse($result ['status'], $result);
});

$app->put('/priceExternalListUpdate/:id','authenticate', function ($id) use($app) {
    $price = new pricelist ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $price->priceExternalListUpdate($id, $data);
    echoResponse($result ['status'], $result);
});

//--------------------customer price list----------------------//
$app->post('/customerpriceSave','authenticate', function() use($app) {
    $cusPrice = new Customerpricelist ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $cusPrice->customerpriceSave($data);
    echoResponse($result ['status'], $result);
});
$app->get('/customerpriceGetOne/:id','authenticate', function($id) {
    $cusPrice = new Customerpricelist ();
    $result = $cusPrice->customerpriceGetOne($id);
    echoResponse(200, $result);
});
$app->put('/customerpriceUpdate/:id','authenticate', function($id) use($app) {
    $cusPrice = new Customerpricelist ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $cusPrice->customerpriceUpdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/customerpriceAll/:id','authenticate', function($id) {
    $cusPrice = new Customerpricelist ();
    $result = $cusPrice->customerpriceAll($id);
    echoResponse(200, $result);
});
$app->get('/customerpriceListCopy/:id','authenticate', function($id) {
    $cusPrice = new Customerpricelist ();
    $result = $cusPrice->customerpriceListCopy($id);
    echoResponse(200, $result);
});
// -------------------sevices status --------------------------//

$app->post('/serviceSave','authenticate', function () use($app) {
    $service = new service ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $service->serviceSave($data);
    echoResponse($result ['status'], $result);
});
$app->get('/serviceget','authenticate', function () {
    $service = new service ();
    $result = $service->serviceget();
    echoResponse(200, $result);
});
$app->get('/servicegetdata','authenticate', function () {
    $service = new service ();
    $result = $service->servicegetdata();
    echoResponse(200, $result);
});
$app->get('/servicegetone/:id', 'authenticate',function ($id) {
    $service = new service ();
    $result = $service->servicegetone($id);
    echoResponse(200, $result);
});
$app->put('/serviceUpdate/:id','authenticate', function ($id) use($app) {
    $service = new service ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $service->serviceUpdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/serviceDelete/:id','authenticate', function ($id) {
    $service = new service ();
    $result = $service->serviceDelete($id);
    echoResponse($result ['status'], $result);
});
$app->get('/serviceJobrepresentationGet','authenticate', function () {
    $service = new service ();
    $result = $service->serviceJobrepresentationGet();
    echoResponse(200, $result);
});

// ------------------ price1 ----------------------------//
$app->post('/price1Save','authenticate', function () use($app) {
    $price = new price ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $price->price1Save($data);
    echoResponse($result ['status'], $result);
});
$app->get('/masterPriceitemget/:id', 'authenticate',function ($id) {
    $price = new price ();
    $result = $price->masterPriceitemget($id);
    echoResponse(200, $result);
});
$app->get('/masterPriceitemgetFromPriceList','authenticate', function () {
    $price = new price ();
    $result = $price->masterPriceitemgetFromPriceList();
    echoResponse(200, $result);
});
$app->get('/childPriceitemget','authenticate', function () {
    $price = new price ();
    $result = $price->childPriceitemget();
    echoResponse(200, $result);
});
$app->get('/childPriceitemAmountget/:id', 'authenticate',function ($id) {
    $price = new price ();
    $result = $price->childPriceitemAmountget($id);
    echoResponse(200, $result);
});
$app->get('/childPriceactiveget','authenticate', function () {
    $price = new price ();
    $result = $price->childPriceactiveget();
    echoResponse(200, $result);
});
$app->get('/price1getone/:id','authenticate', function ($id) {
    $price = new price ();
    $result = $price->price1getone($id);
    echoResponse(200, $result);
});
$app->put('/price1Update/:id','authenticate', function ($id) use($app) {
    $price = new price ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $price->price1Update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/price1Delete/:id','authenticate', function ($id) {
    $price = new price ();
    $result = $price->price1Delete($id);
    echoResponse($result ['status'], $result);
});

//--------------------Rounding price------------------//
$app->post('/roundingPriceSave','authenticate', function() use($app) {
    $rounding = new Rounding ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $rounding->Save($data);
    echoResponse($result ['status'], $result);
});
$app->get('/roundingPriceGetAll','authenticate', function() {
    $rounding = new Rounding ();
    $result = $rounding->roundingPriceGetAll();
    echoResponse(200, $result);
});
$app->get('/roundingPriceGetOne/:id','authenticate', function($id) {
    $rounding = new Rounding ();
    $result = $rounding->roundingPriceGetOne($id);
    echoResponse(200, $result);
});
$app->put('/roundingPriceUpdate/:id','authenticate', function($id) use($app) {
    $rounding = new Rounding ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $rounding->roundingPriceUpdate($id, $data);
    echoResponse(200, $result);
});
$app->delete('/deleteRoundingprice/:id','authenticate', function ($id) {
    $rounding = new Rounding ();
    $result = $rounding->deleteRoundingprice($id);
    echoResponse($result ['status'], $result);
});
// ----------------------price2----------------------//
$app->get('/price2Servicelist','authenticate', function () {
    $price = new price ();
    $result = $price->price2Servicelist();
    echoResponse(200, $result);
});
$app->post('/price2Save', 'authenticate',function () use($app) {
    $price = new price ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $price->price2Save($data);
    echoResponse($result ['status'], $result);
});
$app->get('/price2get', 'authenticate',function () {
    $price = new price ();
    $result = $price->price2get();
    echoResponse(200, $result);
});
$app->get('/price2getone/:id','authenticate', function ($id) {
    $price = new price ();
    $result = $price->price2getone($id);
    echoResponse(200, $result);
});
$app->put('/price2Update/:id','authenticate', function ($id) use($app) {
    $price = new price ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $price->price2Update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/price2Delete/:id','authenticate', function ($id) {
    $price = new price ();
    $result = $price->price2Delete($id);
    echoResponse($result ['status'], $result);
});
// --------------------- items -------------------------//
$app->post('/ItemSave','authenticate', function () use($app) {
    $item = new item ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $item->ItemSave($data);
    echoResponse($result ['status'], $result);
});
$app->get('/itemsGet/:id','authenticate', function ($id) {
    $item = new item ();
    $result = $item->itemsGet($id);
    echoResponse(200, $result);
});
$app->get('/itemsgetone/:id','authenticate', function ($id) {
    $item = new item ();
    $result = $item->itemsgetone($id);
    echoResponse(200, $result);
});
$app->put('/ItemUpdate/:id', 'authenticate',function ($id) use($app) {
    $item = new item ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $item->ItemUpdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/itemDelete/:id/:orderId', 'authenticate',function ($id, $orderId) {
    $item = new item ();
    $result = $item->itemDelete($id, $orderId);
    echoResponse(200, $result);
});

$app->get('/contactPerson/:id', 'authenticate',function ($id) {
    $item = new item ();
    $result = $item->contactPersonget($id);
    echoResponse(200, $result);
});

$app->get('/itemsNumberGet/:id', 'authenticate',function ($id) {
    $item = new item ();
    $result = $item->itemsNumberGet($id);
    echoResponse(200, $result);
});

$app->post('/saveDirectory', 'authenticate',function () use($app) {
    $item = new item ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $item->saveDirectory($data);
    echoResponse($result ['status'], $result);
});

$app->get('/currencyGet', 'authenticate',function () {
    $currency = new currency ();
    $result = $currency->getAll();
    echoResponse(200, $result);
});

$app->delete('/itemFilemanager/:id', 'authenticate',function($id) {
    $item = new item ();
    $result = $item->itemFilemanagerCheck($id);
    echoResponse(200, $result);
});

$app->get('/orderCurrencyMatch/:id','authenticate', function ($id) {
    $item = new item ();
    $result = $item->orderCurrencyMatch($id);
    echoResponse(200, $result);
});
$app->get('/itemCurrency/:id','authenticate', function ($id) {
    $item = new item ();
    $result = $item->itemCurrencyGet($id);
    echoResponse(200, $result);
});
$app->get('/getFilestotal/:id', 'authenticate',function ($id) {
    $item = new item ();
    $result = $item->getFilestotal($id);
    echoResponse(200, $result);
});
// ---------All Languages List ----------//
$app->get('/allLanguages', 'authenticate',function () {
    $currency = new item ();
    $result = $currency->languagesGet();
    echoResponse(200, $result);
});
// ---------------------- master price unit ------------------//
$app->post('/masterpricesave','authenticate', function () use($app) {
    $masterPrice = new masterprice ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $masterPrice->savemasterPrice($data);
    echoResponse($result ['status'], $result);
});

$app->get('/masterPriceGet','authenticate', function () {
    $masterPrice = new masterprice ();
    $result = $masterPrice->masterPriceGetAll();
    echoResponse(200, $result);
});
$app->get('/masterPriceGetdata','authenticate', function () {
    $masterPrice = new masterprice ();
    $result = $masterPrice->masterPriceGetdata();
    echoResponse(200, $result);
});
$app->get('/masterpriceGetOne/:id', 'authenticate',function ($id) {
    $masterPrice = new masterprice ();
    $result = $masterPrice->masterpriceGetOne($id);
    echoResponse(200, $result);
});

$app->put('/masterpriceupdate/:id', 'authenticate',function ($id) use($app) {
    $masterPrice = new masterprice ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $masterPrice->masterpriceupdate($id, $data);
    echoResponse($result ['status'], $result);
});

$app->delete('/masterPriceDelete/:id','authenticate', function ($id) {
    $masterPrice = new masterprice ();
    $result = $masterPrice->masterPriceDelete($id);
    echoResponse($result ['status'], $result);
});
// ----------------------Child Price Unit-------------------//

$app->get('/mastergetPrice','authenticate', function () {
    $masterPrice = new masterprice ();
    $result = $masterPrice->mastergetPrice();
    echoResponse(200, $result);
});
$app->post('/childpricesave','authenticate', function () use($app) {
    $childprice = new childprice ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $childprice->childpricesave($data);
    echoResponse($result ['status'], $result);
});
$app->get('/childPriceGet','authenticate', function () {
    $childprice = new childprice ();
    $result = $childprice->childPriceGetAll();
    echoResponse(200, $result);
});
$app->get('/childpriceGetOne/:id','authenticate', function ($id) {
    $childprice = new childprice ();
    $result = $childprice->childpriceGetOne($id);
    echoResponse(200, $result);
});
$app->get('/getAllChildPricesByMasterPrice/:id','authenticate', function ($masterId) {
    $childprice = new childprice ();
    $result = $childprice->getAllChildPricesByMasterPrice($masterId);
    echoResponse(200, $result);
});
$app->put('/childpriceupdate/:id','authenticate', function ($id) use($app) {
    $childprice = new childprice ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $childprice->childpriceupdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/childPriceDelete/:id','authenticate', function ($id) {
    $childprice = new childprice ();
    $result = $childprice->childPriceDelete($id);
    echoResponse($result ['status'], $result);
});
$app->get('/childpriceDataget','authenticate', function () {
    $childprice = new childprice ();
    $result = $childprice->childpriceDataget();
    echoResponse(200, $result);
});
// -------------------customer group---------------------------//
$app->post('/customerGroupsave', 'authenticate',function () use($app) {
    $customer = new customergroup ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $customer->customerGroupsave($data);
    echoResponse($result ['status'], $result);
});
$app->get('/customerGroupGet', 'authenticate',function () {
    $customer = new customergroup ();
    $result = $customer->customerGroupGetAll();
    echoResponse(200, $result);
});
$app->get('/customerGroupGetOne/:id','authenticate', function ($id) {
    $customer = new customergroup ();
    $result = $customer->customerGroupGetOne($id);
    echoResponse(200, $result);
});
$app->put('/customerGroupUpdate/:id', 'authenticate',function ($id) use($app) {
    $customer = new customergroup ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $customer->customerGroupUpdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/customerGroupDelete/:id','authenticate', function ($id) {
    $customer = new customergroup ();
    $result = $customer->customerGroupDelete($id);
    echoResponse($result ['status'], $result);
});
$app->get('/customergroupGetdata','authenticate', function () {
    $customer = new customergroup ();
    $result = $customer->customergroupGetdata();
    echoResponse(200, $result);
});

// ---------------------Job chain---------------------------//
$app->get('/jobchainstatusGet','authenticate', function () {
    $jobchain = new jobchain ();
    $result = $jobchain->jobchainstatusGet();
    echoResponse(200, $result);
});
$app->post('/jobChansaveJobs', 'authenticate',function () use($app) {
    $jobchain = new jobchain ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $jobchain->jobChansaveJobs($data);
    echoResponse($result ['status'], $result);
});

$app->put('/sortedjobListSave/:id','authenticate', function ($id) use($app) {
    $jobchain = new jobchain ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $jobchain->sortedjobListSave($id, $data);
    echoResponse($result ['status'], $result);
});
$app->post('/jobChanupdeateJobs', 'authenticate',function () use($app) {
    $jobchain = new jobchain ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $jobchain->jobChanupdeateJobs($data);
    echoResponse($result ['status'], $result);
});
$app->get('/jobnumberGet/:id', 'authenticate',function ($id) {
    $jobchain = new jobchain ();
    $result = $jobchain->jobnumberGet($id);
    echoResponse(200, $result);
});
$app->get('/jobChinjobsGet/:id','authenticate', function ($id) {
    $jobchain = new jobchain ();
    $result = $jobchain->jobChinjobsGet($id);
    echoResponse(200, $result);
});
$app->post('/jobChainsave','authenticate', function () use($app) {
    $jobchain = new jobchain ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $jobchain->jobChainsave($data);
    echoResponse($result ['status'], $result);
});
$app->put('/jobChainupdate/:id','authenticate', function ($id) use($app) {
    $jobchain = new jobchain ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $jobchain->jobChainupdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/jobchainnameGet','authenticate', function () {
    $jobchain = new jobchain ();
    $result = $jobchain->jobchainnameGet();
    echoResponse(200, $result);
});
$app->get('/jobChainList','authenticate', function () {
    $jobchain = new jobchain ();
    $result = $jobchain->jobChainList();
    echoResponse(200, $result);
});
$app->get('/jobchainnameduplicateGet','authenticate', function () {
    $jobchain = new jobchain ();
    $result = $jobchain->jobchainnameduplicateGet();
    echoResponse(200, $result);
});
$app->get('/jobChingetOne/:id', 'authenticate',function ($id) {
    $jobchain = new jobchain ();
    $result = $jobchain->jobChingetOne($id);
    echoResponse(200, $result);
});
$app->get('/jobChinnewgetOne/:id','authenticate', function ($id) {
    $jobchain = new jobchain ();
    $result = $jobchain->jobChinnewgetOne($id);
    echoResponse(200, $result);
});
$app->get('/jobChinfolllowsget/:id','authenticate', function ($id) {
    $jobchain = new jobchain ();
    $result = $jobchain->jobChinfolllowsget($id);
    echoResponse(200, $result);
});
$app->get('/masterjobChaindelete','authenticate', function () {
    $jobchain = new jobchain ();
    $result = $jobchain->masterjobChaindelete();
    echoResponse(200, $result);
});
$app->get('/masterJobchainget/:id', 'authenticate',function ($orderId) {
    $jobchain = new jobchain ();
    $result = $jobchain->masterJobchainget($orderId);
    echoResponse(200, $result);
});
$app->get('/chainDuplicateSave/:id','authenticate', function ($id) {
    $jobchain = new jobchain ();
    $result = $jobchain->chainDuplicateSave($id);
    echoResponse(200, $result);
});
$app->get('/masterjobGet/:id','authenticate', function ($id) {
    $jobchain = new jobchain ();
    $result = $jobchain->masterjobGet($id);
    echoResponse(200, $result);
});
// -----------------------address type------------------------//
$app->get('/AddressTypeget','authenticate', function () {
    $user = new users ();
    $result = $user->AddressTypeget();
    echoResponse(200, $result);
});
//------------------------Currency convert--------------------//
$app->post('/currencyConverts','authenticate', function() use($app) {
    $user = new users ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $user->currencyConverts($data);
    echoResponse(200, $result);
});
// -----------------------Center Save-------------------------//
$app->post('/centersave','authenticate', function () use($app) {
    $center = new center ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $center->centersave($data);
    echoResponse($result ['status'], $result);
});
$app->get('/centerDateget','authenticate', function () {
    $center = new center ();
    $result = $center->centerDategetAll();
    echoResponse(200, $result);
});
$app->get('/centergetOne/:id','authenticate', function ($id) {
    $center = new center ();
    $result = $center->centergetOne($id);
    echoResponse(200, $result);
});
$app->put('/centerupdate/:id','authenticate', function ($id) use($app) {
    $center = new center ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $center->centerupdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/deleteCenter/:id','authenticate', function ($id) {
    $center = new center ();
    $result = $center->deleteCenter($id);
    echoResponse(200, $result);
});
$app->get('/centerClientGet','authenticate', function () {
    $center = new center ();
    $result = $center->centerClientGet();
    echoResponse(200, $result);
});
$app->get('/abbrivationMatch/:name','authenticate', function ($name) {
    $center = new center ();
    $result = $center->abbrivationMatch($name);
    echoResponse(200, $result);
});
// ------------------Email Sign Section-------------------//
$app->post('/emailSignsave','authenticate', function () use($app) {
    $sign = new emailsign ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $sign->emailSignsave($data);
    echoResponse($result ['status'], $result);
});
$app->get('/emailSignget', 'authenticate',function () {
    $sign = new emailsign ();
    $result = $sign->emailSignget();
    echoResponse(200, $result);
});

$app->get('/emailSigngetone/:id','authenticate', function ($id) use($app) {
    $sign = new emailsign ();
    $result = $sign->emailSigngetone($id);
    echoResponse(200, $result);
});
$app->put('/emailSignupdate/:id', 'authenticate',function ($id) use($app) {
    $sign = new emailsign ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $sign->emailSignupdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/deleteSign/:id/:image','authenticate', function ($id, $image) {
    $sign = new emailsign ();
    $result = $sign->deleteSign($id, $image);
    echoResponse(200, $result);
});
$app->get('/emailSigngetdata','authenticate', function () {
    $sign = new emailsign ();
    $result = $sign->emailSigngetdata();
    echoResponse(200, $result);
});


$app->post('/saveEmailTemplate','authenticate', function () use($app) {
    $sign = new emailsign ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $sign->saveEmailTemplate($data);
    echoResponse($result ['status'], $result);
});
$app->get('/emailTemplateGetAll/','authenticate', function () use($app) {
    $sign = new emailsign ();
    $result = $sign->emailTemplateGetAll();
    echoResponse(200, $result);
});

$app->get('/emailTemplateGetOne/:id','authenticate', function ($id) use($app) {
    $sign = new emailsign ();
    $result = $sign->emailTemplateGetOne($id);
    echoResponse(200, $result);
});

$app->put('/emailTemplateUpdate/:id', 'authenticate',function ($id) use($app) {
    $sign = new emailsign ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $sign->emailTemplateUpdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/deleteEmailTemplate/:id','authenticate', function ($id) {
    $sign = new emailsign ();
    $result = $sign->deleteEmailTemplate($id);
    echoResponse($result ['status'], $result);
});
$app->get('/getEmailTemplatePlaceHolder/:id', 'authenticate',function ($id) {
    $sign = new emailsign ();
    $result = $sign->emailTemplatePlaceHolder($id);
    echoResponse(200, $result);
});
// -------------------jobs detail section------------------------//
$app->put('/itemupdateJobs/:id', 'authenticate',function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $itemsJob->itemupdateJobs($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/jobpertitemGet/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobpertitemGet($id);
    echoResponse(200, $result);
});
$app->get('/jobpertjobGet/:id/:pid', 'authenticate',function ($id, $pid) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobpertjobGet($id, $pid);
    echoResponse(200, $result);
});
$app->get('/jobpertjobChainGet/:id/:orderId/:chainId','authenticate', function ($id, $orderId, $chainId) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobpertjobChainGet($id, $orderId, $chainId);
    echoResponse(200, $result);
});
$app->post('/jobSummarySave','authenticate', function () use($app) {
    $itemsJob = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $itemsJob->jobSummarySave($data);
    echoResponse($result ['status'], $result);
});
$app->get('/jobsummeryGet/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobsummeryGet($id);
    echoResponse(200, $result);
});
$app->get('/jobitemsidget/:itemId/:id', 'authenticate',function ($itemId, $id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobitemsidget($id, $itemId);
    echoResponse(200, $result);
});
$app->delete('/jobitemDelete/:id','authenticate', function ($id) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobitemDelete($id);
    echoResponse(200, $result);
});
$app->get('/jobChainitemsGet/:id', 'authenticate',function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobChainitemsGet($id);
    echoResponse(200, $result);
});
$app->get('/jobChainsummerychain/:id', 'authenticate',function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobChainsummerychain($id);
    echoResponse(200, $result);
});
$app->get('/jobChainitemsDayGet/:orderid/:jobItemId', 'authenticate',function ($orderid, $jobItemId) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobChainitemsDayGet($orderid, $jobItemId);
    echoResponse(200, $result);
});
$app->get('/projectjobChainGet/:id', 'authenticate',function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->projectjobChainGet($id);
    echoResponse(200, $result);
});
$app->get('/projectjobGet/:id', 'authenticate',function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->projectjobGet($id);
    echoResponse(200, $result);
});
$app->post('/projobChainsave', 'authenticate',function () use($app) {
    $itemsJob = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $itemsJob->projobChainsave($data);
    echoResponse($result ['status'], $result);
});
$app->delete('/deleteJobChain/:id','authenticate', function ($id) {
    $jobchain = new jobchain ();
    $result = $jobchain->deleteJobChain($id);
    echoResponse(200, $result);
});
$app->get('/JobsummeryResourceGet','authenticate', function () {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->JobsummeryResourceGet();
    echoResponse(200, $result);
});
$app->get('/JobsummerycontactGet','authenticate', function () {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->JobsummerycontactGet();
    echoResponse(200, $result);
});
$app->get('/JobsummeryitemstatusGet','authenticate', function () {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->JobsummeryitemstatusGet();
    echoResponse(200, $result);
});
$app->put('/jobSummeryitemUpdate/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $itemsJob->jobSummeryitemUpdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/jobSummeryitemCheckEdit','authenticate', function () {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobSummeryitemCheckEdit();
    echoResponse(200, $result);
});
$app->get('/jobitemsContactPersonGet/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobitemsContactPersonGet($id);
    echoResponse(200, $result);
});
$app->get('/jobselectContactName/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobselectContactName($id);
    echoResponse(200, $result);
});
$app->put('/jobselectContactNameupdate/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $itemsJob->jobselectContactNameupdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->put('/jobSummeryitemCopy/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $itemsJob->jobSummeryitemCopy($id, $data);
    echoResponse($result ['status'], $result);
});
$app->put('/jobselectUserEmail/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $itemsJob->jobselectUserEmail($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/jobchainitemsDelete/:id', 'authenticate',function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobchainitemsDelete($id);
    echoResponse(200, $result);
});
$app->get('/itemOverviewGet/:id', 'authenticate',function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->itemOverviewGet($id);
    echoResponse(200, $result);
});
$app->get('/jobDetailLanguageGet/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobDetailLanguageGet($id);
    echoResponse(200, $result);
});
$app->get('/jobSummeryDetailsGet/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobSummeryDetailsGet($id);
    echoResponse(200, $result);
});
$app->get('/jobdetailItemStatusGet','authenticate', function () {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobdetailItemStatusGet();
    echoResponse(200, $result);
});
$app->get('/scoopdetailItemStatusGet','authenticate', function () {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->scoopdetailItemStatusGet();
    echoResponse(200, $result);
});
$app->get('/jobdetailresourceGet','authenticate', function () {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobdetailresourceGet();
    echoResponse(200, $result);
});
$app->get('/jobSummeryDetailsAutoChangeon/:Orderid/:autoId','authenticate', function ($orderId, $autoId) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobSummeryDetailsAutoChangeon($orderId, $autoId);
    echoResponse(200, $result);
});
$app->get('/jobSummeryDetailsAutoChangeoff/:Orderid/:autoId','authenticate', function ($orderId, $autoId) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobSummeryDetailsAutoChangeoff($orderId, $autoId);
    echoResponse(200, $result);
});
$app->put('/jobSummeryDetailsUpdate/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $itemsJob->jobSummeryDetailsUpdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->put('/jobSummeryJobDetailsUpdate/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $itemsJob->jobSummeryJobDetailsUpdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/jobsummeryResourceMail/:resourceName/:summeryId','authenticate', function ($resourceName, $summeryId) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobsummeryResourceMail($resourceName, $summeryId);
    echoResponse(200, $result);
});
$app->get('/itemsjobStatusGet/:ItemId/:orderId','authenticate', function ($ItemId, $orderId) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->itemsjobStatusGet($ItemId, $orderId);
    echoResponse(200, $result);
});
$app->get('/autostatusUpdate/:ItemId/:Status/:orderId', 'authenticate',function ($ItemId, $status, $orderId) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->autostatusUpdate($ItemId, $status, $orderId);
    echoResponse(200, $result);
});
$app->get('/autostatusChecked/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->autostatusChecked($id);
    echoResponse(200, $result);
});
$app->get('/resourceRedirect/:resource','authenticate', function ($resource) use($app) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->resourceRedirect($resource);
    echoResponse(200, $result);
});
$app->get('/jobItemIconsetdata/:id/:orderId','authenticate', function ($id, $orderId) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobItemIconsetdata($id, $orderId);
    echoResponse(200, $result);
});
$app->get('/jobDetailchange/:id','authenticate', function ($id) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->jobDetailchangeGet($id);
    echoResponse(200, $result);
});

$app->get('/select2Jobdata','authenticate', function () {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->getAll();
    echoResponse(200, $result);
});
$app->get('/getExternalUserJobs/:id','authenticate', function ($id) {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->getExternalUserJobs($id);
    echoResponse(200, $result);
});
$app->get('/select2JobCodeGet','authenticate', function () {
    $itemsJob = new jobs_detail ();
    $result = $itemsJob->select2JobCodeGet();
    echoResponse(200, $result);
});

$app->get('/clientDirectMessageEmailIdGet','authenticate', function () {
    $client = new client ();
    $result = $client->clientDirectMessageEmailIdGet();
    echoResponse(200, $result);
});
$app->get('/emailSignCheck','authenticate', function () {
    $emailsign = new emailsign ();
    $result = $emailsign->emailSignCheck();
    echoResponse(200, $result);
});
$app->put('/acceptJobStatus/:id','authenticate', function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $itemsJob->acceptJobStatus($id, $data);
    echoResponse($result ['status'], $result);
});
$app->put('/rejectJobStatus/:id', 'authenticate',function ($id) use($app) {
    $itemsJob = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $itemsJob->rejectJobStatus($id, $data);
    echoResponse($result ['status'], $result);
});
//-----------------Contect per Message------------------//
$app->get('/contactPerMessage/:id','authenticate', function ($id) {
    $msg = new contactPerMsg ();
    $result = $msg->contactPerMessage($id);
    echoResponse(200, $result);
});
$app->post('/sendcontactPerMsg','authenticate', function () use($app) {
    $msg = new contactPerMsg ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $msg->sendcontactPerMsg($data);
    echoResponse($result ['status'], $result);
});
//---------------Project Team Message-----------------//
$app->get('/projectTeamMsg/:id','authenticate', function ($id) {
    $msg = new contactPerMsg ();
    $result = $msg->projectTeamMsg($id);
    echoResponse(200, $result);
});
$app->post('/sendprojectTeamMsg', 'authenticate',function () use($app) {
    $msg = new contactPerMsg ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $msg->sendprojectTeamMsg($data);
    echoResponse($result ['status'], $result);
});
//--------------General Message---------------------//
$app->get('/generalMsg', 'authenticate',function () use($app) {
    $msg = new contactPerMsg ();
    $result = $msg->generalMsg();
    echoResponse(200, $result);
});
$app->post('/sendgeneralMsg', 'authenticate',function () use($app) {
    $msg = new contactPerMsg ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $msg->sendgeneralMsg($data);
    echoResponse($result ['status'], $result);
});
//------------Job Resourrce Message---------------//
$app->get('/jobResourceMsg/:id','authenticate', function ($id) {
    $msg = new contactPerMsg ();
    $result = $msg->jobResourceMsg($id);
    echoResponse(200, $result);
});
$app->get('/contact_personGet/:id','authenticate', function ($id) {
    $msg = new contactPerMsg ();
    $result = $msg->contact_personGet($id);
    echoResponse(200, $result);
});
$app->post('/sendjobResourceMsg','authenticate', function () use($app) {
    $msg = new contactPerMsg ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $msg->sendjobResourceMsg($data);
    echoResponse($result ['status'], $result);
});
//-----------------Filemanager-------------------//
$app->get('/fileManagerGet', 'authenticate',function () {
    $filemanager = new filemanager ();
    $result = $filemanager->fileManagerGet();
    echoResponse(200, $result);
});
$app->get('/filefolderGet/:id/:root/:externalResourceUserId','authenticate', function ($id, $root,$externalResourceUserId) {
    $filemanager = new filemanager ();
    $result = $filemanager->filefolderGet($id, $root,$externalResourceUserId);
    echoResponse(200, $result);
});
$app->get('/filefolderstget/:id/:root','authenticate', function ($id, $root) {
    $filemanager = new filemanager ();
    $result = $filemanager->filefolderstget($id, $root);
    echoResponse(200, $result);
});
$app->get('/higherfolderGet/:id/:exteruserId','authenticate', function ($id,$exteruserId) {
    $filemanager = new filemanager ();
    $result = $filemanager->higherfolderGet($id,$exteruserId);
    echoResponse(200, $result);
});
$app->put('/fileManagerUpdate/:id', 'authenticate',function ($id) use($app) {
    $filemanager = new filemanager ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $filemanager->fileManagerUpdate($id, $data);
    echoResponse($result ['status'], $result);
});
$app->put('/isParent/:id','authenticate', function ($id) use($app) {
    $filemanager = new filemanager ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $filemanager->isParent($id, $data);
    echoResponse(200, $result);
});
$app->put('/getRootFolder/:id', 'authenticate',function ($id) use($app) {
    $filemanager = new filemanager ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $filemanager->getRootFolder($id, $data);
    echoResponse(200, $result);
});
$app->put('/fileManagerPaste/:id','authenticate', function ($id) use($app) {
    $filemanager = new filemanager ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $filemanager->fileManagerPaste($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/filemanagerfolderDelete/:id/:image','authenticate', function ($id, $image) {
    $filemanager = new filemanager ();
    $result = $filemanager->filemanagerfolderDelete($id, $image);
    echoResponse(200, $result);
});
$app->put('/filemanagerfolderDeleteMultiple/:id','authenticate', function ($id) use($app) {
    $filemanager = new filemanager ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $filemanager->filemanagerfolderDeleteMultiple($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/fileparentNameGet/:id','authenticate', function ($id) {
    $filemanager = new filemanager ();
    $result = $filemanager->fileparentNameGet($id);
    echoResponse(200, $result);
});
$app->get('/Newfoldermake/:id/:name/:role','authenticate', function ($id, $name, $role) {
    $filemanager = new filemanager ();
    $result = $filemanager->Newfoldermake($id, $name, $role);
    echoResponse(200, $result);
});
$app->post('/fileAdd', 'authenticate',function () use($app) {
    $filemanager = new filemanager ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $filemanager->fileAdd($data);
    echoResponse(200, $result);
});
$app->post('/fileAddScoop', 'authenticate',function () use($app) {
    $filemanager = new filemanager ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $filemanager->fileAddScoop($data);
    echoResponse(200, $result);
});
$app->put('/fileSave/:id','authenticate', function ($id) use($app) {
    $filemanager = new filemanager ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $filemanager->fileSave($id, $data);
    echoResponse(200, $result);
});
$app->get('/filefrontget/:id','authenticate', function ($id) {
    $filemanager = new filemanager ();
    $result = $filemanager->filefrontget($id);
    echoResponse(200, $result);
});
$app->get('/filefrontroot/:id/:root','authenticate', function ($id, $root) {
    $filemanager = new filemanager ();
    $result = $filemanager->filefrontroot($id, $root);
    echoResponse(200, $result);
});
$app->get('/jobfilefrontroot/:orderid/:jobid/:root','authenticate', function ($orderid, $jobid, $root) {
    $filemanager = new filemanager ();
    $result = $filemanager->jobfilefrontroot($orderid, $jobid, $root);
    echoResponse(200, $result);
});
$app->get('/jobfileuserfrontroot/:jobid/:root','authenticate', function ($jobid, $root) {
    $filemanager = new filemanager ();
    $result = $filemanager->jobfileuserfrontroot($jobid, $root);
    echoResponse(200, $result);
});
$app->get('/jobfileuserrootSt/:stid/:root','authenticate', function ($stid, $root) {
    $filemanager = new filemanager ();
    $result = $filemanager->jobfileuserrootSt($stid, $root);
    echoResponse(200, $result);
});
$app->get('/filemanagerApproveSend/:aid/:rid', 'authenticate',function ($aid, $rid) {
    $filemanager = new filemanager ();
    $result = $filemanager->filemanagerApproveSend($aid, $rid);
    echoResponse(200, $result);
});
$app->get('/Clientfilefront/:client/:status','authenticate', function ($client, $status) {
    $filemanager = new filemanager ();
    $result = $filemanager->Clientfilefront($client, $status);
    echoResponse(200, $result);
});
$app->get('/IndirectClientfilefront/:client','authenticate', function ($client) {
    $filemanager = new filemanager ();
    $result = $filemanager->IndirectClientfilefront($client);
    echoResponse(200, $result);
});
$app->get('/Userfilefront/:user','authenticate',function ($user) {
    $filemanager = new filemanager ();
    $result = $filemanager->Userfilefront($user);
    echoResponse(200, $result);
});
$app->get('/Itemfilefront/:orderId/:itemId', 'authenticate',function ($orderId, $itemId) {
    $filemanager = new filemanager ();
    $result = $filemanager->Itemfilefront($orderId, $itemId);
    echoResponse(200, $result);
});
$app->get('/filemanagerfolderDownload/:id/','authenticate', function ($id) {
    $filemanager = new filemanager ();
    $result = $filemanager->filemanagerfolderDownload($id);
    echoResponse(200, $result);
});

//----------------order status search---------------------//
$app->get('/statusorderReportFind', 'authenticate',function () use($app) {
    $statusOrder = new orderstatussearch ();
    //$data = json_decode($app->request->getBody(), TRUE);
    $result = $statusOrder->statusorderReportFind();
    echoResponse(200, $result);
});
$app->get('/ordersearchProjectStatusUpdate/:id/:status', 'authenticate',function ($id, $status) {
    $statusOrder = new orderstatussearch ();
    $result = $statusOrder->ordersearchProjectStatusUpdate($id, $status);
    echoResponse(200, $result);
});
$app->get('/ordersearchItemStatusUpdate/:id/:status', 'authenticate',function ($id, $status) {
    $statusOrder = new orderstatussearch ();
    $result = $statusOrder->ordersearchItemStatusUpdate($id, $status);
    echoResponse(200, $result);
});

//----------------Job status search---------------------//
$app->get('/statusJobReportFind','authenticate', function () use($app) {
    $statusJob = new jobstatussearch ();
    //$data = json_decode($app->request->getBody(), TRUE);
    $result = $statusJob->statusJobReportFind();
    echoResponse(200, $result);
});
$app->get('/advanceSearchResource','authenticate', function () use($app) {
    $statusJob = new jobstatussearch ();
    $result = $statusJob->advanceSearchResource();
    echoResponse(200, $result);
});
$app->get('/jobsearchStatusUpdate/:id/:status','authenticate', function ($id, $status) use($app) {
    $statusJob = new jobstatussearch ();
    $result = $statusJob->jobsearchStatusUpdate($id, $status);
    echoResponse(200, $result);
});
$app->delete('/jobsearchStatusDelete/:id','authenticate', function ($id) use($app) {
    $statusJob = new jobstatussearch ();
    $result = $statusJob->jobsearchStatusDelete($id);
    echoResponse(200, $result);
});
//---------------freelance job manage------------------//
$app->get('/freelanceJob/:id','authenticate', function ($id) use($app) {
    $freelanceJob = new freelanceJob ();
    $result = $freelanceJob->freelanceJobget($id);
    echoResponse(200, $result);
});
//------------crone job for job status----------------//
$app->get('/autostatusSet','authenticate', function () use($app) {
    $freelanceJob = new freelanceJob ();
    $result = $freelanceJob->autostatusSet();
    echoResponse(200, $result);
});
//-----------Dashboard manage order------------------//
$app->get('/dashboardOrderGet', function () use ($app) {
    $dashboard = new dashboard ();
    $result = $dashboard->OrderGet();
    echoResponse(200, $result);
});
$app->get('/userActivityGetOne/:id', 'authenticate',function ($id) use ($app) {
    $dashboard = new dashboard ();
    $result = $dashboard->userActivityGetOne($id);
    echoResponse(200, $result);
});
$app->post('/userActivityGet','authenticate', function () use ($app) {
    $dashboard = new dashboard ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $dashboard->userActivityGet($data);
    echoResponse(200, $result);
});
$app->post('/jobActivityGet','authenticate', function () use ($app) {
    $dashboard = new dashboard ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $dashboard->jobActivityGet($data);
    echoResponse(200, $result);
});
$app->get('/taskJobget/:id', 'authenticate',function ($id) use ($app) {
    $dashboard = new dashboard ();
    $result = $dashboard->taskJobget($id);
    echoResponse(200, $result);
});
$app->get('/holidayGet/:id',function ($id) use ($app) {
    $dashboard = new dashboard ();
    $result = $dashboard->holidayGet($id);
    echoResponse(200, $result);
});
$app->get('/getCountry','authenticate', function () use ($app) {
    $dashboard = new dashboard ();
    $result = $dashboard->getCountry();
    echoResponse(200, $result);
});
$app->get('/dashboardProjectsOrderGet/:id', function ($id) use ($app) {
    $dashboard = new dashboard ();
    $result = $dashboard->ProjectsOrderGet($id);
    echoResponse(200, $result);
});

//---------------Resource asserts------------------//
$app->post('/resourceAssets', 'authenticate',function () use($app) {
    $assets = new asserts ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $assets->resourceAssetsSave($data);
    echoResponse(200, $result);
});
$app->get('/resourceAssets/:id','authenticate', function ($id) use ($app) {
    $assets = new asserts ();
    $result = $assets->resourceAssetsGet($id);
    echoResponse(200, $result);
});

$app->get('/resourceAssetsByIuserId/:id','authenticate', function ($id) use ($app) {
    $assets = new asserts ();
    $result = $assets->resourceAssetsByIuserId($id);
    echoResponse(200, $result);
});
$app->get('/resourceAssetsGetOne/:id/:jobId','authenticate', function ($id, $jobId) use ($app) {
    $assets = new asserts ();
    $result = $assets->resourceAssetsGetOne($id, $jobId);
    echoResponse(200, $result);
});
//---------------Discussion board----------------------//
$app->post('/discussionOrder','authenticate', function () use($app) {

    $discuss = new discussion();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $discuss->discussionOrder($data);
    echoResponse(200, $result);
});
$app->get('/discussionOrder/:id','authenticate', function($id) {
    $discuss = new discussion ();
    $result = $discuss->discussionOrderGet($id);
    echoResponse(200, $result);
});
$app->delete('/discussionOrder/:id/:data', 'authenticate',function($id,$data){
    $discuss = new discussion ();
    $result = $discuss->discussionOrderDelete($id,$data);
    echoResponse(200, $result);
});
$app->put('/discussionOrder/:id','authenticate', function ($id) use($app) {
    $discuss = new discussion ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $discuss->discussionOrderUpdate($data, $id);
    echoResponse(200, $result);
});
//---------Comment Read Unread--------- //
$app->put('/discussionCommentread/:orderId','authenticate', function ($orderId) use($app) {
    $discuss = new discussion ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $discuss->discussionCommentread($data, $orderId);
    echoResponse(200, $result);
});
//---------Emoji text--------- //
$app->get('/emojitext','authenticate', function () use($app) {
    $discuss = new discussion ();
    $result = $discuss->discussionEmojitext();
    echoResponse(200, $result);
});
//-----------------Knowledge Category---------------------//
$app->post('/KcategorySave', 'authenticate',function () use($app) {
    $category = new Category ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $category->categorySave($data);
    echoResponse(200, $result);
});
$app->get('/KcategorygetAll', 'authenticate',function() {
    $category = new Category ();
    $result = $category->KcategorygetAll();
    echoResponse(200, $result);
});
$app->get('/KcategorygetOne/:id', 'authenticate',function($id) {
    $category = new Category ();
    $result = $category->KcategorygetOne($id);
    echoResponse(200, $result);
});
$app->put('/KcategorySave/:id','authenticate', function ($id) use($app) {
    $category = new Category ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $category->categoryUpdate($id, $data);
    echoResponse(200, $result);
});
$app->delete('/KcategoryDelete/:id','authenticate', function ($id) {
    $category = new Category ();
    $result = $category->KcategoryDelete($id);
    echoResponse(200, $result);
});
$app->get('/categoryIconGet/:id','authenticate', function($id) {
    $category = new Category ();
    $result = $category->categoryIconGet($id);
    echoResponse(200, $result);
});
//-----------------knowledge articles--------------------//
$app->post('/knowledgeArticleSave','authenticate', function () use($app) {
    $articles = new Articles ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $articles->knowledgeArticleSave($data);
    echoResponse(200, $result);
});
$app->get('/knowledgeArticlegetAll','authenticate', function() {
    $articles = new Articles ();
    $result = $articles->knowledgeArticlegetAll();
    echoResponse(200, $result);
});
$app->get('/knowledgeArticlegetOne/:id','authenticate', function($id) {
    $articles = new Articles ();
    $result = $articles->knowledgeArticlegetOne($id);
    echoResponse(200, $result);
});
$app->delete('/knowledgeArticleDelete/:id','authenticate', function ($id) {
    $articles = new Articles ();
    $result = $articles->knowledgeArticleDelete($id);
    echoResponse(200, $result);
});
$app->put('/knowledgeArticleSave/:id','authenticate', function ($id) use($app) {
    $articles = new Articles ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $articles->knowledgeArticleUpdate($id, $data);
    echoResponse(200, $result);
});
$app->post('/knowledgeArticleImage', function () use($app) {
    // Allowed extentions.
    $allowedExts = array("gif", "jpeg", "jpg", "png", "blob");
    // Get filename.
    $temp = explode(".", $_FILES["file"]["name"]);
    // Get extension.
    $extension = end($temp);
    // Generate new random name.
    $name = sha1(microtime()) . "." . $extension;
    $Upath = substr(getcwd(), 0, -6);
    // Save file in the uploads folder.
    move_uploaded_file($_FILES["file"]["tmp_name"], $Upath . "uploads/upload_image/" . $name);
    // Generate response.
    // $response = new StdClass;
    // $response->link = "/uploads/" . $name;
    $response = ['link' => "/uploads/upload_image/" . $name];
    echo stripslashes(json_encode($response));
    exit;
    echoResponse(200, $result);
});
$app->post('/knowledgeArticlefile', function () use($app) {
    // Allowed extentions.
    //$allowedExts = array("txt", "pdf", "doc");
    // Get filename.
    $temp = explode(".", $_FILES["file"]["name"]);
    // Get extension.
    $extension = end($temp);
    // Generate new random name.
    $name = sha1(microtime()) . "." . $extension;
    $Upath = substr(getcwd(), 0, -6);
    // Save file in the uploads folder.
    move_uploaded_file($_FILES["file"]["tmp_name"], $Upath . "uploads/upload_image/" . $name);
    // Generate response.
    // $response = new StdClass;
    // $response->link = "/uploads/" . $name;
    $response = ['link' => "/uploads/upload_image/" . $name];
    echo stripslashes(json_encode($response));
    exit;
    echoResponse(200, $result);
});
$app->post('/knowledgeDeleteArticleImage', function () use($app) {
    // Get src.
    $src = $_POST["src"];
    $path = substr(getcwd(), 0, -11);
    // Check if file exists.
    if (file_exists($path . $src)) {
        // Delete file.
        unlink($path . $src);
    }
     exit;
    echoResponse(200, $result);
});
//---------------knowledge News--------------------//
$app->post('/knowledgeNewsSave', 'authenticate',function () use($app) {
    $news = new Knowledgenews ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $news->knowledgeNewsSave($data);
    echoResponse(200, $result);
});
$app->get('/knowledgeNewsgetAll','authenticate', function() {
    $news = new Knowledgenews ();
    $result = $news->knowledgeNewsgetAll();
    echoResponse(200, $result);
});
$app->get('/knowledgeNewsgetOne/:id', 'authenticate',function($id) {
    $news = new Knowledgenews ();
    $result = $news->knowledgeNewsgetOne($id);
    echoResponse(200, $result);
});
$app->delete('/knowledgeNewsDelete/:id','authenticate', function ($id) {
    $news = new Knowledgenews ();
    $result = $news->knowledgeNewsDelete($id);
    echoResponse(200, $result);
});
$app->put('/knowledgeNewsUpdate/:id','authenticate', function ($id) use($app) {
    $news = new Knowledgenews ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $news->knowledgeNewsUpdate($id, $data);
    echoResponse(200, $result);
});
//--------------------Knowledge Event---------------------//
$app->post('/knowledgeEventSave','authenticate', function () use($app) {
    $calendar = new Knowledgecalendar ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $calendar->knowledgeEventSave($data);
    echoResponse(200, $result);
});
$app->get('/knowledgeEventGetAll','authenticate', function() {
    $calendar = new Knowledgecalendar ();
    $result = $calendar->knowledgeEventGetAll();
    echoResponse(200, $result);
});
$app->get('/knowledgeEventGetOne/:id','authenticate', function($id) {
    $calendar = new Knowledgecalendar ();
    $result = $calendar->knowledgeEventGetOne($id);
    echoResponse(200, $result);
});
$app->put('/knowledgeEventUpdate/:id','authenticate', function ($id) use($app) {
    $calendar = new Knowledgecalendar ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $calendar->knowledgeEventUpdate($id, $data);
    echoResponse(200, $result);
});
$app->delete('/knowledgeEventDelete/:id', 'authenticate',function ($id) {
    $calendar = new Knowledgecalendar ();
    $result = $calendar->knowledgeEventDelete($id);
    echoResponse(200, $result);
});
//knowledge funpic manage
$app->get('/funpicGet/:id', 'authenticate',function($id) {
    $pic = new Knowledgefunpic ();
    $result = $pic->funpicGet($id);
    echoResponse(200, $result);
});

//freelancer project detail manage
$app->get('/projectGet','authenticate', function() {
    $project = new projectDetail ();
    $result = $project->projectGet();
    echoResponse(200, $result);
});
$app->get('/projectgetOne/:id','authenticate', function($id) {
    $project = new projectDetail ();
    $result = $project->projectgetOne($id);
    echoResponse(200, $result);
});
$app->get('/projectJobapprovedGet/:id','authenticate', function($id) {
    $project = new projectDetail ();
    $result = $project->projectJobapprovedGet($id);
    echoResponse(200, $result);
});
$app->get('/jobGetOnOrderId/:id','authenticate', function($id) {
    $project = new projectDetail ();
    $result = $project->jobGetOnOrderId($id);
    echoResponse(200, $result);
});
//------------Project Invoice Manage-----------------//
$app->get('/projectinvoiceGet/:id','authenticate', function($id) {
    $invoice = new projectInvoice ();
    $result = $invoice->projectinvoiceGet($id);
    echoResponse(200, $result);
});
$app->get('/projectinvoiceGetOne/:id/:invoiceStatus', 'authenticate',function($id, $invoiceStatus) {
    $invoice = new projectInvoice ();
    $result = $invoice->projectinvoiceGetOne($id, $invoiceStatus);
    echoResponse(200, $result);
});

//-----------------log master manage----------------//
$app->post('/saveLog','authenticate', function () use($app) {
    $log = new Log_master ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $log->saveLog($data);
    echoResponse(200, $result);
});

$app->get('/recentActivityGet/:id','authenticate', function($id) {
    $log = new Log_master ();
    $result = $log->recentActivityGet($id);
    echoResponse(200, $result);
});

//-----------------invoice due master manage----------------//
$app->post('/saveInvoicePeriod','authenticate', function () use($app) {
    $invoice = new Invoice_due ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $invoice->save($data);
    echoResponse(200, $result);
});
$app->get('/getAllInvoicePeriod', 'authenticate',function() {
    $invoice = new Invoice_due ();
    $result = $invoice->getAllInvoicePeriod();
    echoResponse(200, $result);
});
$app->get('/getOneInvoicePeriod/:id', 'authenticate',function($id) {
    $invoice = new Invoice_due ();
    $result = $invoice->getOneInvoicePeriod($id);
    echoResponse(200, $result);
});
$app->put('/updateInvoicePeriod/:id','authenticate', function ($id) use($app) {
    $invoice = new Invoice_due ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $invoice->updateInvoicePeriod($id, $data);
    echoResponse(200, $result);
});
$app->delete('/deleteInvoicePeriod/:id','authenticate', function ($id) {
    $invoice = new Invoice_due ();
    $result = $invoice->deleteInvoicePeriod($id);
    echoResponse(200, $result);
});
//-------------------Freelancer Invoice manage----------------//
$app->post('/invoiceCreate','authenticate', function()  use($app) {
    $invoice = new Freelance_invoice ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $invoice->invoiceCreate($data);
    echoResponse(200, $result);
});
$app->post('/invoiceSave', 'authenticate',function () use($app) {
    $invoice = new Freelance_invoice ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $invoice->saveInvoice($data);
    echoResponse(200, $result);
});
$app->get('/viewAllInvoice/:type/:userId', 'authenticate',function($type,$userId) {
    $invoice = new Freelance_invoice ();
    $result = $invoice->viewAllInvoice($type,$userId);
    echoResponse(200, $result);
});
$app->get('/viewAllInvoice1/:type','authenticate', function($type) {
    $invoice = new Freelance_invoice ();
    $result = $invoice->viewAllInvoice1($type);
    echoResponse(200, $result);
});

$app->get('/getAllInvoiceByUserId/:type/:userId','authenticate', function($type,$userId) {
    $invoice = new Freelance_invoice ();
    $result = $invoice->getAllInvoiceByUserId($type,$userId);
    echoResponse(200, $result);
});
$app->get('/invoiceViewOne/:id', 'authenticate',function($id) {
    $invoice = new Freelance_invoice ();
    $result = $invoice->invoiceViewOne($id);
    echoResponse(200, $result);
});
$app->get('/invoiceUpdate/:id','authenticate', function($id) {
    $invoice = new Freelance_invoice ();
    $result = $invoice->invoiceUpdate($id);
    echoResponse(200, $result);
});
$app->get('/getInvoicePartPayments/:id','authenticate', function($id) {
    $invoice = new Freelance_invoice ();
    $result = $invoice->getInvoicePartPayments($id);
    echoResponse(200, $result);
});
$app->put('/invoiceStatusChange/:id','authenticate', function ($id) use($app) {
    $invoice = new Freelance_invoice ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $invoice->invoiceStatusChange($data, $id);
    echoResponse(200, $result);
});
$app->put('/updateJobSummeryItemStatus/:id','authenticate', function ($id) use($app) {
    $invoice = new Freelance_invoice ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $invoice->updateJobSummeryItemStatus($data, $id);
    echoResponse(200, $result);
});
$app->get('/getFreelanceStatement/:id','authenticate', function($id) {
    $stmt = new Freelance_invoice ();
    $result = $stmt->getFreelanceStatement($id);
    echoResponse(200, $result);
});
function echoResponse($status_code, $response) {
    global $app;
    $app->status($status_code);
    $app->contentType('application/json');
    echo json_encode($response, JSON_NUMERIC_CHECK);
}
$app->get('/getReportedImages', 'authenticate',function () use($app) {
    $getImage = new Knowledgefunpic();
    $result = $getImage->getReportedImages();
    echoResponse(200, $result);
});
$app->delete('/UpdateImageStatus/:imgName/:status','authenticate', function ($imgName,$status) use($app) {
    $deleteIMage = new Knowledgefunpic();
    $result = $deleteIMage->UpdateImageStatus($imgName,$status);
    echoResponse(200, $result);
});
//-------------------Resource Postion----------------//
$app->post('/userPosition','authenticate', function () use($app) {
    $userposition = new userposition ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $userposition->save($data);
    echoResponse($result ['status'], $result);
});
$app->get('/GetuserPosition', 'authenticate',function () use($app) {
    $userposition = new userposition ();
    $result = $userposition->GetuserPosition();
    echoResponse(200, $result);
});
$app->get('/GetuserPosition/:id','authenticate', function ($id) use($app) {
    $userposition = new userposition ();
    $result = $userposition->getTypeById($id);
    echoResponse(200, $result);
});
$app->put('/userPosition/:id','authenticate', function ($id) use($app) {
    $userposition = new userposition ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $userposition->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/userPosition/:id','authenticate', function ($id) {
    $userposition = new userposition ();
    $result = $userposition->delete($id);
    echoResponse($result ['status'], $result);
});
$app->get('/select2GetAll/:id','authenticate', function ($id) {
    $userposition = new userposition ();
    $result = $userposition->select2GetAll($id);
    echoResponse(200, $result);
});

$app->get('/getVatDataNew/:vatNumber', 'authenticate',function ($vatNumber) use($app) {
    $users = new users();
    $result = $users->getVat($vatNumber);
    echoResponse(200, $result);
});
$app->get('/getAllcontact', 'authenticate',function () use($app) {
    $contact = new contact();
    $result = $contact->getAllcontact();
    echoResponse(200, $result);
});

$app->get('/checkContactClientId/:id','authenticate', function ($id) use($app) {
    $contact = new contact();
    $result = $contact->checkContactClientId($id);
    echoResponse(200, $result);
});

/* Getting Client Curruncy using client's Contact person ID(iContactId)*/
$app->get('/getClientCurrency/:id','authenticate', function ($id) use($app) {
    $client = new client();
    $result = $client->getClientCurrency($id);
    echoResponse(200, $result);
});
$app->get('/getUserType/:id','authenticate', function ($id) use($app) {
    $users = new users ();
    $result = $users->getUserType($id);
    echoResponse(200, $result);
});
$app->get('/getUserById/:id','authenticate', function ($id) use($app) {
    $users = new users ();
    $result = $users->getUserById($id);
    echoResponse(200, $result);
});

/*getIndirectClient By Id*/
$app->get('/getIndirectClient/:id','authenticate', function ($id) use($app) {
    $users = new client ();
    $result = $users->getIndirectClient($id);
    echoResponse(200, $result);
});
$app->get('/getJobsFromTmsSummeryView', function () {
    $jobList = new jobs_detail ();
    $result = $jobList->getJobsFromTmsSummeryView();
    echoResponse(200, $result);
});

/* Assign PO number to job*/
$app->put('/assignPoToJob/:id', 'authenticate',function ($id) use($app) {
    $jobs_detail = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $jobs_detail->assignPoToJob($data, $id);
    echoResponse(200, $result);
});

$app->get('/checkRootFromFiles/:id/:externalResourceUserId','authenticate', function ($id,$externalResourceUserId) use($app) {
    $filemanager = new filemanager ();
    $result = $filemanager->checkRootFromFiles($id,$externalResourceUserId);
    echoResponse(200, $result);
});

$app->get('/checkDefaultFolderProjectExternal/:id/','authenticate', function ($id) use($app) {
    $filemanager = new filemanager ();
    $result = $filemanager->checkDefaultFolderProjectExternal($id);
    echoResponse(200, $result);
});

$app->post('/AddNumberOfItems','authenticate', function () use($app) {
    $item = new item ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $item->AddNumberOfItems($data);
    echoResponse(200, $result);
});
$app->put('/moveJob/:id', 'authenticate',function ($id) use($app) {
    $jobs_detail = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $jobs_detail->moveJob($data, $id);
    echoResponse(200, $result);
});
$app->put('/projectStartStatus/:id', 'authenticate',function ($id) use($app) {
    $general = new general ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $general->projectStartStatus($data, $id);
    echoResponse(200, $result);
});

$app->put('/changeUserStatus/:id','authenticate', function ($id) use($app) {
    $userClass = new users ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $userClass->changeUserStatus($id, $data);
    echoResponse($result ['status'], $result);
});
$app->get('/getMemoQLanguage', 'authenticate',function () use($app) {
    $language = new language ();
    $result = $language->getMemoQLanguage();
    echoResponse(200, $result);
});

$app->get('/currencyExchange','authenticate', function () use($app) {
    $currency = new currency ();
    $result = $currency->currencyExchange();
    echoResponse(200, $result);
});

$app->post('/saveFvMenu','authenticate', function () use($app) {
    $users = new users ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $users->saveFvMenu($data);
    echoResponse(200, $result);
});

$app->get('/getFvMenu/:id', function ($id) use($app) {
    $users = new users ();
    $result = $users->getFvMenu($id);
    echoResponse(200, $result);
});

$app->post('/saveDateFormat','authenticate', function () use($app) {
    $dtFormat = new dateformat ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $dtFormat->save($data);
    echoResponse(200, $result);
});
$app->get('/getdateFormatById/:id','authenticate', function ($id) use($app) {
    $dtFormat = new dateformat ();
    $result = $dtFormat->getById($id);
    echoResponse(200, $result);
});

//byUserID
$app->get('/getAllFormat/:id','authenticate', function ($id) use($app) {
    $dtFormat = new dateformat ();
    $result = $dtFormat->getAllFormat($id);
    echoResponse(200, $result);
});

$app->put('/updateDateFormat/:id','authenticate', function ($id) use($app) {
    $dtFormat = new dateformat ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $dtFormat->updateDateFormat($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/deletedateFormat/:id','authenticate', function ($id) {
    $dtFormat = new dateformat ();
    $result = $dtFormat->delete($id);
    echoResponse($result ['status'], $result);
});
$app->get('/getdateFormatByIuserId/:id','authenticate', function ($id) use($app) {
    $dtFormat = new dateformat ();
    $result = $dtFormat->getdateFormatByIuserId($id);
    echoResponse(200, $result);
});
$app->post('/saveDecimalSeparator','authenticate', function () use($app) {
    $separator = new decimaleparator ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $separator->save($data);
    echoResponse(200, $result);
});
$app->get('/getAllDecimalSeperator','authenticate', function () use($app) {
    $separator = new decimaleparator ();
    $result = $separator->getAllDecimalSeperator();
    echoResponse(200, $result);
});
$app->get('/getSeparatorById/:id','authenticate', function ($id) use($app) {
    $separator = new decimaleparator ();
    $result = $separator->getById($id);
    echoResponse(200, $result);
});

$app->put('/updateDecimalSeparator/:id','authenticate', function ($id) use($app) {
    $separator = new decimaleparator ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $separator->updateDecimalSeparator($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/deleteSeparator/:id','authenticate', function ($id) {
    $separator = new decimaleparator ();
    $result = $separator->delete($id);
    echoResponse($result ['status'], $result);
});
$app->get('/getDecimalSeperatorByIuserId/:id','authenticate', function ($id) use($app) {
    $separator = new decimaleparator ();
    $result = $separator->getDecimalSeperatorByIuserId($id);
    echoResponse(200, $result);
});

$app->get('/getClientIndirectClient/:id','authenticate', function ($id) use($app) {
    $customer = new customer ();
    $result = $customer->getClientIndirectClient($id);
    echoResponse(200, $result);
});


$app->post('/saveSortedJobsData','authenticate', function () use($app) {
    $jobs_detail = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $jobs_detail->saveSortedJobsData($data);
    echoResponse(200, $result);
});

$app->post('/saveSortedJobsData','authenticate', function () use($app) {
    $jobs_detail = new jobs_detail ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $jobs_detail->saveSortedJobsData($data);
    echoResponse(200, $result);
});
$app->get('/getsaveSortedJobsData/:id','authenticate', function ($id) use($app) {
    $jobs_detail = new jobs_detail ();
    $result = $jobs_detail->getsaveSortedJobsData($id);
    echoResponse(200, $result);
});

$app->get('/getUserUsingId/:id','authenticate', function ($id) use($app) {
    $users = new users ();
    $result = $users->getUserUsingId($id);
    echoResponse(200, $result);
});
$app->post('/sendMailTest', function () use($app) {
    $users = new users ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $users->mailTest($data);
    echoResponse(200, $result);
});
$app->post('/activateAccount', function () use($app) {
    $users = new users ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $users->activateAccount($data);
    echoResponse(200, $result);
});

$app->post('/filterStatement', function () use($app) {
    $stmt = new Freelance_invoice ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $stmt->filterStatement($data);
    echoResponse(200, $result);
});
$app->get('/getInvoiceNumber','authenticate', function () use($app) {
    $stmt = new Freelance_invoice ();
    $result = $stmt->getInvoiceNumber();
    echoResponse(200, $result);
});
// ------------------ Work Instruct -----------------------//
$app->get('/workinstructactive','authenticate', function () use($app) {
    $workinstruct = new workinstruct ();
    $result = $workinstruct->getActive();
    echoResponse(200, $result);
});
$app->get('/workinstructs','authenticate', function () use($app) {
    $workinstruct = new workinstruct ();
    $result = $workinstruct->getAll();
    echoResponse(200, $result);
});
$app->get('/workinstructs/:id','authenticate', function ($id) use($app) {
    $workinstruct = new workinstruct ();
    $result = $workinstruct->getTypeById($id);
    echoResponse(200, $result);
});
$app->post('/workinstructs','authenticate', function () use($app) {
    $workinstruct = new workinstruct ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $workinstruct->save($data);
    echoResponse($result ['status'], $result);
});
$app->put('/workinstructs/:id','authenticate', function ($id) use($app) {
    $workinstruct = new workinstruct ();
    $data = json_decode($app->request->getBody(), TRUE);
    $result = $workinstruct->update($id, $data);
    echoResponse($result ['status'], $result);
});
$app->delete('/workinstructs/:id','authenticate', function ($id) {
    $workinstruct = new workinstruct ();
    $result = $workinstruct->delete($id);
    echoResponse($result ['status'], $result);
});
// End Work instructs //
$app->run();
?>
