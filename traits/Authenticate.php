<?php

use Firebase\JWT\JWT;

trait Authenticate
{
    public function attemp($email, $password)
    {
        $query = "SELECT u.email, u.name, u.address, u.phone_number
            FROM {$this->table} u
            WHERE u.email = :email
            LIMIT 0,1
        ";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind params
        $stmt->bindParam(':email', $email);

        // Excute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !$this->verifyPassword($email, $password)) {
            return false;
        }

        return $row;
    }

    protected function verifyPassword($email, $password)
    {
        return password_verify($password, $this->getPassword($email));
    }

    // Get password user
    protected function getPassword($email) {
        $query = "SELECT u.password
            FROM {$this->table} u
            WHERE u.email = :email
            LIMIT 0,1
        ";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind params
        $stmt->bindParam(':email', $email);

        // Excute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['password'];
    }

    protected function getCurrentUser()
    {
        $headers = apache_request_headers();

        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);

            try {
                $user = JWT::decode($token, $this->key, array('HS256'));

                return $user;
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    protected function authorization($email)
    {
        $user = $this->getCurrentUser();

        return $user->email === $email;
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

        return $token;
    }
}
