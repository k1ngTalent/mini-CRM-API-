<?php
$app->post('/login',function() use ($app){
    $s = json_decode($app->request->getBody());
    verifyRequiredParams(array('username','password'),$s);
    $response = array();
    $db = new util();
    $username = $s->username;
    $password = $s->password;
    $query = "SELECT * from users where username='$username'";
    $currentUser= $db->getOne($query);
    if($currentUser !=null){
        require_once 'pwdHash.php';
        if(passwordHash::check_password($currentUser['password'],$password)){
            $response['status']="success";
             $response['message']="Login Successful";
            $response['_id']=$currentUser['_id'];
             $response['firstname']=$currentUser['firstname'];
             $response['lastname']=$currentUser['lastname'];
             $response['username']=$currentUser['username'];
             $response['date_added']=$currentUser['date_added'];
                 if(!isset($_SESSION)){
                     session_start();
                 }
             $_SESSION['_id'] = $currentUser['_id'];
             $_SESSION['firstname'] = $currentUser['firstname'];
             $_SESSION['lastname'] = $currentUser['lastname'];
             $_SESSION['username'] = $currentUser['username'];
            echoResponse(200,$response);
        }else{

            $response['message']="Incorrect Username or password";
            $response['status']="error";
            echoResponse(201,$response);
        }
    }else{
            $response['message']="Incorrect Username or password";
            $response['status']="error";
            echoResponse(201,$response);
    }

});

$app->get('/session', function() {
    $db = new util();
    $session = $db->getSession();
    if($session){
        echoResponse(200, $session);
    }else{
        $response["status"] = "error";
        $response["message"] = "Not logged In!";
        echoResponse(201, $response);
    }
    
});

$app->get('/logout', function() {
    $db = new util();
    $session = $db->destroySession();
    $response["status"] = "info";
    $response["message"] = "Logged out successfully";
    echoResponse(200, $response);
});





 ?>