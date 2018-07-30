<?php
$app->post('/addUser', function () use ($app) {
    $db = new util();
    $hash = new passwordHash();
    $response = array();
    $data = json_decode($app->request->getBody());
    verifyRequiredParams(array('firstname', 'lastname', 'username', 'password'), $data);
    $firstname = $data->firstname;
    $lastname = $data->lastname;
    $username = $data->username;
    $password = $hash::hash($data->password);
    $data->password = $password;
    $query = "SELECT * from users where username='$username'";
    $checkExist = $db->getOne($query);
    if ($checkExist == null) {
        $tableName = "users";
        $columnNames = array('firstname', 'lastname', 'username', 'password');
        $res = $db->insert($tableName, $columnNames, $data);
        if ($res == "success") {
            $response['status'] = "success";
            $response['message'] = "Registered successfully";
            echoResponse(200, $response);
        } else {
            $response['status'] = "error";
            $response['message'] = "Error,Try again!";
            echoResponse(201, $response);
        }

    } else {
        $response['status'] = "error";
        $response['message'] = "User already exist";
        echoResponse(201, $response);
    }
});
$app->get('/allUsers', function () {
    $db = new util();
    $response = array();
    $response["users"] = array();
    $query = "SELECT * from users";
    $companies = $db->getAll($query);
    foreach ($users as $user) {
        $tmp["_id"] = $user["_id"];
        $tmp["firstname"] = $user["firstname"];
        $tmp["lastname"] = $user["lastname"];
        $tmp["username"] = $user["username"];
        $tmp["date_added"] = $user["date_added"];
        array_push($response["users"], $tmp);
    }
    $response["status"] = "success";
    echoResponse(200, $response);
});
$app->get('/user:_id', function ($_id) {
    $db = new util();
    $response = array();
    $query = "SELECT * from users where _id='$_id'";
    $user = $db->getAll($query);
    if ($company != null) {
        $response['user'] = array();
        foreach ($user as $u) {
            $tmp["_id"] = $u["_id"];
            $tmp["firstname"] = $u["firstname"];
            $tmp["lastname"] = $u["lastname"];
            $tmp["username"] = $u["username"];
            $tmp["date_added"] = $u["date_added"];
            array_push($response["user"], $tmp);
        }
        $response["status"] = "success";
        echoResponse(200, $response);
    } else {
        $response['status'] = "error";
        $response['message'] = "Company doesnt exist";
        echoResponse(201, $response);
    }
});
$app->put('/editUser:_id', function ($_id) {
    $db = new util();
  $response = array();
  $query = "SELECT * from company where _id= '$_id'";
  $checkExist = $db->getOne($query);
  if ($checkExist != null) {
    $s = json_decode($app->request->getBody());
    $s->_id = $_id;
    verifyRequiredParams(array('name', 'email', 'website'), $s);
    $tableName = 'company';
    $where = array('_id' => $_id);
    $query = "SELECT * from company where name = '$s->name' and _id!='$_id'";
    $checkExist = $db->getOne($query);
    if ($checkExist == null) {
      $res = $db->update($tableName, $s, $where);
      if ($res === 'success') {
        $response["status"] = "success";
        $response["message"] = "Edited succesfully";;
        echoResponse(200, $response);
      } else {
        $response["status"] = "error";
        $response["message"] = "error Try again";
        echoResponse(201, $response);
      }
    } else {
      $response["status"] = "error";
      $response["message"] = "Company already exist";
      echoResponse(201, $response);
    }
  } else {
    $response["status"] = "error";
    $response["message"] = "Company doesnt exist";
  }
});
$app->delete('/user:_id', function ($_id) {
    $db = new util();
    $response = array();
    $query = "SELECT * from users where _id= '$_id'";
    $checkExist = $db->getOne($query);
    if ($checkExist != null) {
        $tableName = 'users';
        $where = array('_id' => $_id);
        $res = $db->delete($tableName, $where);
        if ($res == "success") {
            $response["status"] = "success";
            $response["message"] = "Deleted Successfully!";
            echoResponse(201, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Try again!";
            echoResponse(201, $response);
        }
    } else {
        $response["status"] = "error";
        $response["message"] = "User doesnt exist";
        echoResponse(201, $response);
    }
});
?>
