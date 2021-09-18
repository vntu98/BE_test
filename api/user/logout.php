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

    // Get response user login
    $result = $user->logout();

    if (!$result) {
        http_response_code(400);
        $response = [
            'status' => 400,
            'message' => 'Token did not match the expected token!'
        ];
    } else {
        http_response_code(200);
        $response = [
            'status' => 200,
            'message' => 'Logout successfull!'
        ];
    }

    echo json_encode($response);
