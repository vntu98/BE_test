<?php
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

    include_once '../../config/Database.php';
    include_once '../../models/User.php';

    // Connect DB
    $database = new Database();
    $db = $database->connect();

    // Instance user
    $user = new User($db);

    // Get raw data
    $data = json_decode(file_get_contents("php://input"));

    // Set email & password to login
    $user->email = $data->email;
    $user->password = $data->password;

    // Get response user login
    $token = $user->login();

    if (!$token) {
        http_response_code(422);
        $response = [
            'status' => 422,
            'message' => 'Email or password invalid!'
        ];
    } else {
        http_response_code(200);
        $response = [
            'status' => 200,
            'data' => [
                'user' => [
                    'email' => $user->email,
                    'name' => $user->name,
                    'phone_number' => $user->phone_number,
                    'address' => $user->address,
                ],
                'token' => $token
            ]
        ];
    }

    echo json_encode($response);
