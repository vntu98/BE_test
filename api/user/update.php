<?php
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: PUT');
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

    // Set email to update
    $user->email = $data->email;

    // Get user
    $hasUser = $user->show();

    if (!$hasUser) {
        http_response_code(404);
        $response = [
            'message' => 'User not found!'
        ];
    } else {
        http_response_code(200);
        $user->name = $data->name ?? $user->name;
        $user->address = $data->address ?? $user->address;
        $user->phone_number = $data->phone_number ?? $user->phone_number;
        $user->password = $data->password ?? '';

        $response = $user->update();
    }

    echo json_encode($response);
