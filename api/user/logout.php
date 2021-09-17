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
        $response = [
            'message' => 'Token did not match the expected token!'
        ];
    } else {
        $response = [
            'message' => 'Logout successfull!'
        ];
    }

    echo json_encode($response);