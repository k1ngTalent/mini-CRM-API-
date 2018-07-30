<?php
$app->post('/addEmployee', function () use ($app) {
    $db = new util();
    $response = array();
    $data = json_decode($app->request->getBody());
    verifyRequiredParams(array('firstname', 'lastname', 'emp_num', 'company_id', 'phonenumber'), $data);
    $emp_num = $data->emp_num;
    $company_id = $data->company_id;
    $query = "SELECT * from company where _id = '$company_id'";
    if ($db->getOne($query) != null) {
        $query = "SELECT * from employees where company_id='$company_id' and emp_num='$emp_num'";
        $checkExist = $db->getOne($query);
        if ($checkExist == null) {
            $tableName = "employees";
            $columnNames = array('firstname', 'lastname', 'emp_num', 'company_id', 'phonenumber');
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
            $response['message'] = "Employee already exist in the company";
            echoResponse(201, $response);
        }
    } else {
        $response['status'] = "error";
        $response['message'] = "Company doesnt exist";
        echoResponse(201, $response);
    }

});
$app->get('/allEmployee', function () {
    $db = new util();
    $response = array();
    $response["employees"] = array();
    $query = "SELECT * from employees";
    $emps = $db->getAll($query);
    foreach ($emps as $emp) {
        $tmp["_id"] = $emp["_id"];
        $tmp["firstname"] = $emp["firstname"];
        $tmp["lastname"] = $emp["lastname"];
        $tmp["company_id"] = $emp["company_id"];
        $tmp["phone_number"] = $emp["phone_number"];
        $tmp["date_added"] = $emp["date_added"];
        array_push($response["employees"], $tmp);
    }
    $response["status"] = "success";
    echoResponse(200, $response);
});
$app->get('/employe:_id', function ($_id) {
    $db = new util();
    $response = array();
    $query = "SELECT * from employees where _id='$_id'";
    $emp = $db->getAll($query);
    if ($emp != null) {
        $response['employee'] = array();
        foreach ($emp as $e) {
            $tmp["_id"] = $e["_id"];
            $tmp["firstname"] = $e["firstname"];
            $tmp["lastname"] = $e["lastname"];
            $tmp["company_id"] = $e["company_id"];
            $tmp["phone_number"] = $e["phone_number"];
            $tmp["date_added"] = $e["date_added"];
            array_push($response["employee"], $tmp);
        }
        $response["status"] = "success";
        echoResponse(200, $response);
    } else {
        $response['status'] = "error";
        $response['message'] = "Employee doesnt exist";
        echoResponse(201, $response);
    }
});
$app->get('/compEmployees:_id', function ($_id) {
    $db = new util();
    $response = array();
    $query = "SELECT * from company where _id='$_id'";
    $query2 = "SELECT * from employees where company_id='$_id'";
    $emps = $db->getAll($query2);
    if ($db->getAll($query) != null) {
        if ($emps != null) {
            $response['employees'] = array();
            foreach ($emps as $e) {
                $tmp["_id"] = $e["_id"];
                $tmp["firstname"] = $e["firstname"];
                $tmp["lastname"] = $e["lastname"];
                $tmp["phone_number"] = $e["phone_number"];
                $tmp["date_added"] = $e["date_added"];
                array_push($response["employee"], $tmp);
            }
            $response["status"] = "success";
            echoResponse(200, $response);
        } else {
            $response['status'] = "error";
            $response['message'] = "Company has no employee yet!";
            echoResponse(201, $response);
        }

    } else {
        $response['status'] = "error";
        $response['message'] = "company doesnt exist";
        echoResponse(201, $response);
    }
});
$app->put('/editEmployee/:_id', function ($_id) use ($app) {
    $db = new util();
    $response = array();
    $data = json_decode($app->request->getBody());
    verifyRequiredParams(array('firstname', 'lastname', 'emp_num', 'company_id', 'phone_number'), $data);
    $newEmp = $data->emp_num;
    $newCompId = $data->company_id;
    $data->_id = $_id;
    $query = "SELECT * from employees where _id='$_id'";
    if ($db->getOne($query) != null) {
        $query = "SELECT * from company where _id = '$newCompId'";
        if ($db->getOne($query) != null) {
            $query = "SELECT * from employees where company_id='$newCompId' and emp_num ='$newEmp' and _id!='$_id'";
            $checkExist = $db->getOne($query);
            if ($checkExist == null) {
                $tableName = "employees";
                $where = array('_id' => $_id);
                $res = $db->update($tableName, $data, $where);
                $response['status'] = $res;
                $response['message'] = "Updated successfully";
                echoResponse(200, $response);
                // if ($res == "success") {
                //     $response['status'] = "success";
                //     $response['message'] = "Updated successfully";
                //     echoResponse(200, $response);
                // } else {
                //     $response['status'] = "error";
                //     $response['message'] = "Error,Try again!";
                //     echoResponse(201, $response);
                // }

            } else {
                $response['status'] = "error";
                $response['message'] = "Employee already exist in that company";
                echoResponse(201, $response);
            }
        } else {
            $response['status'] = "error";
            $response['message'] = "Company or Employee doesnt exist";
            echoResponse(201, $response);
        }
    } else {
        $response['status'] = "error";
        $response['message'] = "Company or Employee doesnt exist";
        echoResponse(201, $response);
    }
});
// $app->put('/editEmployee/:company_id/:emp_num', function ($company_id, $emp_num) {
//     $db = new util();
//     $response = array();
//     $data = json_decode($app->request->getBody());
//     verifyRequiredParams(array('firstname', 'lastname', 'emp_num', 'company_id', 'phonenumber'), $data);
//     $newEmp = $data->emp_num;
//     $newCompId = $data->company_id;
//     $query = "SELECT * from employees where emp_num='$emp_num' and company_id='$company_id'";
//     if ($db->getOne($query) != null) {
//         $query = "SELECT * from company where _id = '$newCompId'";
//         if ($db->getOne($query) != null) {
//             $query = "SELECT * from employees where company_id='$newCompId' and emp_num ='$newEmp'";
//             $checkExist = $db->getOne($query);
//             if ($checkExist == null) {
//                 $tableName = "employees";
//                 $where = array('emp_num' => $emp_num, 'company_id' => $company_id);
//                 $res = $db->update($tableName, $data, $where);
//                 if ($res == "success") {
//                     $response['status'] = "success";
//                     $response['message'] = "Updated successfully";
//                     echoResponse(200, $response);
//                 } else {
//                     $response['status'] = "error";
//                     $response['message'] = "Error,Try again!";
//                     echoResponse(201, $response);
//                 }

//             } else {
//                 $response['status'] = "error";
//                 $response['message'] = "Employee already exist in that company";
//                 echoResponse(201, $response);
//             }
//         } else {
//             $response['status'] = "error";
//             $response['message'] = "Company or Employee doesnt exist";
//             echoResponse(201, $response);
//         }
//     } else {
//         $response['status'] = "error";
//         $response['message'] = "Company or Employee doesnt exist";
//         echoResponse(201, $response);
//     }
// });
$app->delete('/employee/:company_id/:emp_num', function ($company_id, $emp_num) {
    $db = new util();
    $response = array();
    $query = "SELECT * from employees where emp_num= '$emp_num'";
    $checkExist = $db->getOne($query);
    if ($checkExist != null) {
        $query = "SELECT * from employees where emp_num= '$emp_num' and company_id='$company_id'";
        if ($db->getOne($query) != null) {
            $tableName = 'employees';
            $where = array('emp_num' => $emp_num, 'company_id' => $company_id);
            $res = $db->delete($tableName, $where);
            if ($res == "success") {
                $response["status"] = "success";
                $response["message"] = "Deleted Successfully!";
                echoResponse(200, $response);
            } else {
                $response["status"] = "error";
                $response["message"] = "Try again!";
                echoResponse(201, $response);
            }
        } else {
            $response["status"] = "error";
            $response["message"] = "Employee or Company doesnt exist";
            echoResponse(201, $response);
        }
    } else {
        $response["status"] = "error";
        $response["message"] = "Employee or Company doesnt exist";
        echoResponse(201, $response);
    }
});
?>
