<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}

require __DIR__.'../../classes/Database.php';
require __DIR__.'../../classes/JwtHandler.php';

$db_connection = new Database();
$conn = $db_connection->dbConnection();

$data = json_decode(file_get_contents("php://input"));
$returnData = [];

// ПРОВЕРКА СПОСОБА ЗАПРОСА
if($_SERVER["REQUEST_METHOD"] != "POST"):
    $returnData = msg(0,404,'Page Not Found!');

// ПРОВЕРКА ЗАПОЛНЕНЫ ЛИ ПОЛЯ
elseif(!isset($data->password)
    || empty(trim($data->password))
):

    $fields = ['fields' => ['name','password']];
    $returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);

// ЕСЛИ ПУСТЫХ ПОЛЕЙ НЕТ, ТОГДА
else:
    $name = trim($data->name);
    $password = trim($data->password);


    // ОШИБКА ЕСЛИ В ПАРОЛЕ МЕНЬШЕ 8 СИМВОЛОВ
    else if(strlen($password) < 8):
        $returnData = msg(0,422,'Your password must be at least 8 characters long!');

    // ПОЛЬЗОВАТЕЛЬ МОЖЕТ ВОЙТИ
    else:
        try{

            $fetch_user_by_name = "SELECT * FROM `users` WHERE `name`=:name";
            $query_stmt = $conn->prepare($fetch_user_by_name);
            $query_stmt->bindValue(':name', $name,PDO::PARAM_STR);
            $query_stmt->execute();

            // ЕСЛИ ПОЛЬЗОВАТЕЛЬ НАЙДЕН ПО ИМЕНИ
            if($query_stmt->rowCount()):
                $row = $query_stmt->fetch(PDO::FETCH_ASSOC);
                $check_password = password_verify($password, $row['password']);

                // ПРОВЕРКА ПАРОЛЯ
                if($check_password):

                    $jwt = new JwtHandler();
                    $token = $jwt->_jwt_encode_data(
                        'http://localhost/php_auth_api/',
                        array("user_id"=> $row['id'])
                    );

                    $returnData = [
                        'success' => 1,
                        'message' => 'You have successfully logged in.',
                        'token' => $token
                    ];

                // ЕСЛИ ПАРОЛЬ НЕВЕРНЫЙ
                else:
                    $returnData = msg(0,422,'Invalid Password!');
                endif;

            // еСЛИ ПОЛЬЗОВАТЕЛЬ НЕ НАЙДЕН
            else:
                $returnData = msg(0,422,'Invalid Address!');
            endif;
        }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
        }

    endif;

endif;

echo json_encode($returnData);