<?php
$app->post('/addCompany', function () use ($app) {
  $response = array();
  $db = new util();
  if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['website'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $website = $_POST['website'];

    $target_dir = 'logo';
    $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));
    $image_target_file = $target_dir . "/" . $name . "." . $file_ext;
    $image_path = "logo/" . $name . "." . $file_ext;
    $expensions = array("jpeg", "jpg", "png");
    $query = "SELECT * from company where name= '$name'";
    $checkExist = $db->getOne($query);
    if ($checkExist == null) {
      if (!in_array($file_ext, $expensions) === false) {
        if (move_uploaded_file($_FILES["image"]['tmp_name'], $image_target_file)) {
          $query = "INSERT INTO company(name,email,website,logoUrl) 
          VALUES('$name', '$email', '$website','$image_path')";
          $res = $db->forQue($query);
          if ($res == 1) {
            $response['status'] = "success";
            $response['message'] = "Company added succesfully!";
            echoResponse(200, $response);
          } else {
            $response['status'] = "error";
            $response['message'] = "Error,Try again!";
            echoResponse(201, $response);
          }
        } else {
          $response["status"] = "error";
          $response["message"] = "Eroor,Trying uploading logo again!";
          echoResponse(201, $response);
          exit();
        }
      } else {
        $response["status"] = "error";
        $response["message"] = "Only jpg, jpeg and png files are supported";
        echoResponse(201, $response);
      }
    } else {
      $response["status"] = "error";
      $response["message"] = "Company with that name already exists";
      echoResponse(201, $response);
    }
  } else {
    $response["status"] = "error";
    $response["message"] = "Required Parameters missing";
    echoResponse(201, $response);
  }

});
$app->get('/allCompanies', function () {
  $db = new util();
  $response = array();
  $response["companies"] = array();
  $query = "SELECT * from company";
  $companies = $db->getAll($query);
  foreach ($companies as $company) {
    $tmp["_id"] = $company["_id"];
    $tmp["name"] = $company["name"];
    $tmp["email"] = $company["email"];
    $tmp["website"] = $company["website"];
    $tmp["logoUrl"] = $company["logoUrl"];
    array_push($response["companies"], $tmp);
  }
  $response["status"] = "success";
  echoResponse(200, $response);
});
$app->get('/company/:_id', function ($_id) {
  $db = new util();
  $response = array();
  $query = "SELECT * from company where _id='$_id'";
  $company = $db->getAll($query);
  if ($company != null) {
    $response['company'] = array();
    foreach ($company as $c) {
      $tmp["_id"] = $c["_id"];
      $tmp["name"] = $c["name"];
      $tmp["email"] = $c["email"];
      $tmp["website"] = $c["website"];
      $tmp["logoUrl"] = $c["logoUrl"];
      array_push($response["company"], $tmp);
    }
    $response["status"] = "success";
    echoResponse(200, $response);
  } else {
    $response['status'] = "error";
    $response['message'] = "Company doesnt exist";
    echoResponse(201, $response);
  }
});
$app->put('/editCompany/:_id', function ($_id) use ($app) {
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
    echoResponse(201, $response);
  }


});
$app->post('/changeLogo/:_id', function ($_id) use ($app) {
  $db = new util();
  $response = array();
  $query = "SELECT * from company where _id= '$_id'";
  $checkExist = $db->getOne($query);
  if ($checkExist != null) {
    $name = $checkExist['name'];
    $target_dir = 'logo';
    $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));
    $image_target_file = $target_dir . "/" . $name . "." . $file_ext;
    $image_path = "logo/" . $name . "." . $file_ext;
    $expensions = array("jpeg", "jpg", "png");
    if (!in_array($file_ext, $expensions) === false) {
      if (move_uploaded_file($_FILES["image"]['tmp_name'], $image_target_file)) {
        $query = "UPDATE company set logoUrl='$image_path' where name='$name' and _id='$_id'";
        if ($db->forQue($query) == 1) {
          $response["status"] = "success";
          $response["message"] = "Logo updated successfully!";
          echoResponse(201, $response);
        } else {
          $response["status"] = "error";
          $response["message"] = "Error!,Try Uploading Again!";
          echoResponse(201, $response);
        }
      } else {
        $response["status"] = "error";
        $response["message"] = "Error!,Try Uploading Again!";
        echoResponse(201, $response);
      }
    } else {
      $response["status"] = "error";
      $response["message"] = "Only jpg, jpeg and png files are supported";
      echoResponse(201, $response);
    }
  } else {
    $response["status"] = "error";
    $response["message"] = "Company doesnt exist";
    echoResponse(201, $response);
  }
});
$app->delete('/company/:_id', function ($_id) {
  $db = new util();
  $response = array();
  $query = "SELECT * from company where _id= '$_id'";
  $checkExist = $db->getOne($query);
  if ($checkExist != null) {
    $tableName = 'company';
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
    $response["message"] = "Company doesnt exist";
    echoResponse(201, $response);
  }
})
?>
