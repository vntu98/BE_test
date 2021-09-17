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
    $response = $user->login();

    if (!$response) {
        $response = [
            'message' => 'Email or password invalid!'
        ];
    }

    echo json_encode($response);