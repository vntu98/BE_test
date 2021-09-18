<?php
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once '../../config/Database.php';
    include_once '../../models/User.php';

    // Connect DB
    $database = new Database();
    $db = $database->connect();

    // Instance user
    $user = new User($db);

    // Get email
    $user->email = $_GET['email'] ?? die();

    // Get user
    $result = $user->show();

    if (!$result) {
        http_response_code(404);
        $response = [
            'status' => 404,
            'message' => 'User not found'
        ];
    } else {
        http_response_code(200);
        $response = [
            'status' => 200,
            'data' => [
                'email' => $user->email,
                'name' => $user->name,
                'address' => $user->address,
                'phone_number' => $user->phone_number
            ]
        ];
    }

    echo json_encode($response);
