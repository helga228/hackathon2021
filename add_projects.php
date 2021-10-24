<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'Database.php';

// POST DATA
$data = json_decode(file_get_contents("php://input"));

if (
    isset($data->name)
    && isset($data->Date_of_completion)
    && !empty(trim($data->name))
    && !empty(trim($data->Date_of_completion))


) {
    $username = mysqli_real_escape_string($db_conn, trim($data->name));
    $user_completion = mysqli_real_escape_string($db_conn, trim($data->Date_of_completion));
    if (filter_var($user_completion, FILTER_VALIDATE_EMAIL)) {
        $insertUser = mysqli_query($db_conn, "SELECT * FROM `projects` WHERE `id` = '' AND `name` LIKE '$data' AND `Date_of_completion` = '$data");
        if ($insertUser) {
            $last_id = mysqli_insert_id($db_conn);
            echo json_encode(["success" => 1, "msg" => "User Inserted.", "id" => $last_id]);
        } else {
            echo json_encode(["success" => 0, "msg" => "User Not Inserted!"]);
        }
    } else {
        echo json_encode(["success" => 0, "msg" => "Invalid Email Address!"]);
    }
} else {
    echo json_encode(["success" => 0, "msg" => "Please fill all the required fields!"]);
}