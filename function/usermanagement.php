<?php

namespace Classes;

require_once "../conn/Database.php";

class UserManagement
{
    private $con;

    public function __construct($db)
    {
        $this->con = $db;
    }

    // ✅ CLEAN INPUT HANDLER
    private function input(array $data): array
    {
        return [
            'username' => $data['username'] ?? '',
            'password' => $data['password'] ?? '',
            'position' => $data['position'] ?? 'Staff',

            'firstname' => $data['firstname'] ?? '',
            'middlename' => $data['middlename'] ?? '',
            'lastname' => $data['lastname'] ?? '',
            'age' => (int) ($data['age'] ?? 0),

            'street' => $data['street'] ?? '',
            'barangay' => $data['barangay'] ?? '',
            'city' => $data['city'] ?? '',
            'province' => $data['province'] ?? '',
            'country' => $data['country'] ?? '',

            'email' => $data['email'] ?? '',
            'contactnumber' => $data['contactnumber'] ?? '',

            'void_pin' => $data['void_pin'] ?? null,
        ];
    }

    // 🔥 ADD USER (TRANSACTION SAFE.)
   public function addUser(array $data): array
{
    $d = $this->input($data);

    try {
        $this->con->beginTransaction();

        // Check username
        $stmt = $this->con->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$d['username']]);

        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Username already exists'];
        }

        // ✅ Get void_password from form (manual input)
        $voidRaw = !empty($data['void_password']) ? $data['void_password'] : null;

        // Insert user
        $this->con->prepare("
            INSERT INTO users (username, password, position, failed_attempts, void_password)
            VALUES (?, ?, ?, 0, ?)
        ")->execute([
            $d['username'],
            password_hash($d['password'], PASSWORD_BCRYPT), // hashed login password
            $d['position'],
            $voidRaw // plain void password
        ]);

        $userId = $this->con->lastInsertId();

        // Insert info
        $this->con->prepare("
            INSERT INTO users_info
            (user_id, firstname, middlename, lastname, age, street, barangay, city, province, country, email, contactnumber)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ")->execute([
            $userId,
            $d['firstname'],
            $d['middlename'],
            $d['lastname'],
            $d['age'],
            $d['street'],
            $d['barangay'],
            $d['city'],
            $d['province'],
            $d['country'],
            $d['email'],
            $d['contactnumber']
        ]);

        $this->con->commit();

        return [
            'success' => true,
            'message' => 'User added successfully'
        ];

    } catch (\Throwable $e) {
        $this->con->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

    // 🔥 UPDATE USER (MERGED VERSION - no duplicate methods)
    public function updateUser(int $userId, array $data): array
{
    if (!$userId) {
        return ['success' => false, 'message' => 'Invalid user ID'];
    }

    $d = $this->input($data);

    // =========================
    // VALIDATION
    // =========================
    if (
        !$d['firstname'] ||
        !$d['lastname'] ||
        !$d['email'] ||
        !$d['username']
    ) {
        return ['success' => false, 'message' => 'Required fields missing'];
    }

    try {
        $this->con->beginTransaction();

        // =========================
        // CHECK DUPLICATE USERNAME
        // =========================
        $stmt = $this->con->prepare("
            SELECT id FROM users 
            WHERE username = ? AND id != ?
        ");
        $stmt->execute([
            trim($d['username']),
            $userId
        ]);

        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Username already exists'];
        }

        // =========================
        // USERS TABLE UPDATE
        // =========================
        $fields = ['position = ?', 'username = ?'];
        $params = [
            $d['position'],
            trim($d['username'])
        ];

        // ✅ HASHED password
        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // ✅ PLAIN void password (as requested)
        if (!empty($data['void_password'])) {
            $fields[] = 'void_password = ?';
            $params[] = $data['void_password'];
        }

        $params[] = $userId;

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $this->con->prepare($sql)->execute($params);

        // =========================
        // USERS INFO UPDATE
        // =========================
        $this->con->prepare("
            UPDATE users_info SET 
                firstname=?, 
                middlename=?, 
                lastname=?, 
                age=?, 
                email=?, 
                contactnumber=?, 
                street=?, 
                barangay=?, 
                city=?, 
                province=?, 
                country=?
            WHERE user_id=?
        ")->execute([
            $d['firstname'],
            $d['middlename'] ?? null,
            $d['lastname'],
            $d['age'] ?? null,
            $d['email'],
            $d['contactnumber'],
            $d['street'] ?? null,
            $d['barangay'] ?? null,
            $d['city'] ?? null,
            $d['province'] ?? null,
            $d['country'] ?? null,
            $userId
        ]);

        $this->con->commit();

        return ['success' => true, 'message' => 'User updated successfully'];

    } catch (\Throwable $e) {
        $this->con->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
    public function updateUserSystem(int $userId, array $data): array
    {
        if (!$userId) {
            return ['success' => false, 'message' => 'Invalid user ID'];
        }

        $d = $this->input($data);

        if (!$d['firstname'] || !$d['lastname'] || !$d['email']) {
            return ['success' => false, 'message' => 'Required fields missing'];
        }

        try {
            $this->con->beginTransaction();

            // =========================
            // UPDATE USERS TABLE
            // =========================
            if (!empty($data['password'])) {

                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

                $stmt = $this->con->prepare("
        UPDATE users 
        SET username = ?, password = ?, void_password = ?
        WHERE id = ?
    ");

                $stmt->execute([
                    $d['username'],
                    $hashedPassword,
                    $data['void_password'], // always included
                    $userId
                ]);

            } else {

                $stmt = $this->con->prepare("
        UPDATE users 
        SET username = ?, void_password = ?
        WHERE id = ?
    ");

                $stmt->execute([
                    $d['username'],
                    $data['void_password'], // ✅ FIXED
                    $userId
                ]);
            }

            // =========================
            // UPDATE USERS INFO TABLE
            // =========================
            $stmt = $this->con->prepare("
            UPDATE users_info SET 
            firstname=?, middlename=?, lastname=?, age=?, email=?, contactnumber=?, 
            street=?, barangay=?, city=?, province=?, country=?
            WHERE user_id=?
        ");

            $stmt->execute([
                $d['firstname'],
                $d['middlename'],
                $d['lastname'],
                $d['age'] ?? null,
                $d['email'],
                $d['contactnumber'],
                $d['street'] ?? null,
                $d['barangay'] ?? null,
                $d['city'] ?? null,
                $d['province'] ?? null,
                $d['country'] ?? null,
                $userId
            ]);

            $this->con->commit();

            return ['success' => true, 'message' => 'User updated successfully'];

        } catch (\Throwable $e) {
            $this->con->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    // 🔥 DELETE USER
    public function deleteUser(int $userId): array
    {
        if (!$userId) {
            return ['success' => false, 'message' => 'Invalid ID'];
        }

        $ok = $this->con->prepare("DELETE FROM users WHERE id = ?")
            ->execute([$userId]);

        return $ok
            ? ['success' => true, 'message' => 'Deleted successfully']
            : ['success' => false, 'message' => 'Delete failed'];
    }

    public function setUserStatus(int $userId, string $status): array
    {
        if (!$userId || !in_array($status, ['active', 'disabled'], true)) {
            return ['success' => false, 'message' => 'Invalid account status request'];
        }

        $stmt = $this->con->prepare("UPDATE users SET status = ? WHERE id = ?");
        $ok = $stmt->execute([$status, $userId]);

        return $ok
            ? ['success' => true, 'message' => $status === 'active' ? 'Account enabled successfully' : 'Account disabled successfully']
            : ['success' => false, 'message' => 'Unable to update account status'];
    }

    // 🔍 GET ALL
    public function getAllUsers(): array
    {
        return $this->con
            ->query("SELECT u.*, ui.* FROM users u 
                     LEFT JOIN users_info ui ON u.id = ui.user_id")
            ->fetchAll();
    }

    // 🔍 GET ONE
    public function getUserById(int $id)
    {
        $stmt = $this->con->prepare("
            SELECT u.*, ui.* FROM users u
            LEFT JOIN users_info ui ON u.id = ui.user_id
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    // 🔐 LOGIN SUPPORT
    public function getUserByUsername(string $username)
    {
        $stmt = $this->con->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function updateFailedAttempts(int $userId, int $attempts): bool
    {
        return $this->con->prepare("
            UPDATE users SET failed_attempts = ?, last_attempt = NOW()
            WHERE id = ?
        ")->execute([$attempts, $userId]);
    }
    public function getPositionBadge($position)
    {
        return match ($position) {
            'Admin' => 'bg-danger',
            'Owner' => 'bg-dark',
            'Manager' => 'bg-warning text-dark',
            'Staff' => 'bg-primary',
            default => 'bg-secondary'
        };
    }

}