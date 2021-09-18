<?php

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/traits/Authenticate.php';

class User {
    use Authenticate;

    private $conn;

    private $key = '_token';

    private $table = 'users';

    // User Properties
    public $email;
    public $password;
    public $name;
    public $address;
    public $phone_number;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Login user
    public function login()
    {
        // Prevent injection
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // Attempt credential
        $user = $this->attemp($this->email, $this->password);

        // Set properties
        $this->name = $user['name'];
        $this->address = $user['address'];
        $this->phone_number = $user['phone_number'];

        $token = $this->generateToken();

        return $token;
    }

    // Get user
    public function show()
    {
        $query = "SELECT u.email, u.name, u.address, u.phone_number
            FROM {$this->table} u
            WHERE u.email = :email
            LIMIT 0,1
        ";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind params
        $stmt->bindParam(':email', $this->email);

        // Excute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        // Set properties
        $this->name = $row['name'];
        $this->address = $row['address'];
        $this->phone_number = $row['phone_number'];

        return true;
    }

    // Update user
    public function update()
    {
        $this->email = htmlspecialchars(strip_tags($this->email));
        $isLogin = $this->getCurrentUser();
        $authorization = $this->authorization($this->email);

        if (!$isLogin || !$authorization) {
            http_response_code(403);
            $response = [
                'message' => 'Unauthorization!'
            ];

            return $response;
        }

        $query = "UPDATE {$this->table}
            SET
                name = :name,
                address = :address,
                phone_number = :phone_number,
                password = :password
            WHERE
                email = :email
        ";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Prevent injection
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->phone_number = htmlspecialchars(strip_tags($this->phone_number));
        $this->password = $this->password ?
            password_hash(htmlspecialchars(strip_tags($this->password)), PASSWORD_DEFAULT) :
            $this->getPassword($this->email);

        // Bind params
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':phone_number', $this->phone_number);
        $stmt->bindParam(':password', $this->password);

        // Excute query
        if ($stmt->execute()) {
            http_response_code(200);
            $response = [
                'message' => 'User updated!'
            ];

            return $response;
        }

        printf('Error: ', $stmt->error);

        return false;
    }

    public function logout()
    {
        if ($this->getCurrentUser()) {
            return true;
        }

        return false;
    }
}
