<?php

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Firebase\JWT\JWT;

class User {
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
        $query = "SELECT u.email, u.name, u.address, u.phone_number
            FROM {$this->table} u
            WHERE u.email = :email
            LIMIT 0,1
        ";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Prevent injection
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // Bind params
        $stmt->bindParam(':email', $this->email);

        // Excute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return false;
        }

        // Compare password
        if (!password_verify($this->password, $this->getPassword())) {
            return false;
        }

        // Set properties
        $this->name = $row['name'];
        $this->address = $row['address'];
        $this->phone_number = $row['phone_number'];

        $token = $this->generateToken();

        return $token;
    }

    // Generate token
    protected function generateToken() {
        $expires = time() + 60 * 60;
        $payload = [
            'email' => $this->email,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'expires' => $expires
        ];

        $token = JWT::encode($payload, $this->key);

        $response = [
            'token' => $token,
            'expires' => $expires
        ];

        return $response;
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
        $authorization = $this->authorization();

        if (!$authorization) {
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
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->phone_number = htmlspecialchars(strip_tags($this->phone_number));
        $this->password = password_hash(htmlspecialchars(strip_tags($this->password ?: $this->getPassword())), PASSWORD_DEFAULT);

        // Bind params
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':phone_number', $this->phone_number);
        $stmt->bindParam(':password', $this->password);

        // Excute query
        if ($stmt->execute()) {
            $response = [
                'message' => 'User updated!'
            ];

            return $response;
        }

        printf('Error: ', $stmt->error);

        return false;
    }

    // Get password user
    protected function getPassword() {
        $query = "SELECT u.password
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
        
        return $row['password'];
    }

    public function logout()
    {
        $headers = apache_request_headers();

        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
        
            try {
                $token = JWT::decode($token, $this->key, array('HS256'));

                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        
        return false;
    }

    protected function authorization()
    {
        $headers = apache_request_headers();

        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
        
            try {
                $token = JWT::decode($token, $this->key, array('HS256'));

                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        
        return false;
    }
}