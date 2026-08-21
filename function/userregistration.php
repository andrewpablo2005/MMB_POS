<?php

namespace Classes;
// PDO DB
require_once "../conn/Database.php";

class UserRegistration
{
    // Users table properties
    public int $id;
    public string $username;
    public string $password;
    public string $position;
    public int $failed_attempts = 0;
    public ?string $last_attempt = null;

    // Users_info table properties
    public string $firstname;
    public string $middlename;
    public string $lastname;
    public int $age;
    public string $street;
    public string $barangay;
    public string $city;
    public string $province;
    public string $country;
    public string $email;
    public string $contactnumber;

    private $con;
    private string $response;

    public function __construct($db)
    {
        $this->con = $db;
    }

    public function getPost()
    {

        $this->username = $_POST['username'];
        $this->password = $_POST['password'];
        $this->position = $_POST['position'];

        // 🔥 USERS_INFO TABLE (SAFE)
        $this->firstname = $_POST['firstname'];
        $this->middlename = $_POST['middlename'];
        $this->lastname = $_POST['lastname'];
        $this->age = (int) ($_POST['age']);

        $this->street = $_POST['street'];
        $this->barangay = $_POST['barangay'];
        $this->city = $_POST['city'];
        $this->province = $_POST['province'];
        $this->country = $_POST['country'];

        $this->email = $_POST['email'];
        $this->contactnumber = $_POST['contactnumber'];
    }

public function pre_addUser()
{
    if (isset($_POST['pre_addUser'])) {
        $this->getPost();

        // Check if username already exists in both users and pre_approved_users tables
        $stmt = $this->con->prepare("
            SELECT id FROM users WHERE username = ?
            UNION
            SELECT id FROM pre_approved_users WHERE username = ?
        ");
        $stmt->execute([$this->username, $this->username]);
        if ($stmt->fetch()) {
            $this->response = "Username already exists";
            echo "<script>alert('Username already exists. Please choose a different username.');</script>";
            return false;
        }

        // Hash password
        $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);

        // Insert into users table
        $stmt = $this->con->prepare("INSERT INTO pre_approved_users (username, password, position) VALUES (?, ?, ?)");
        $stmtUser = $stmt->execute([
            $this->username,
            $hashedPassword,
            $this->position
        ]);

        if (!$stmtUser) {
            $this->response = "Failed to create user";
            return false;
        }

        // Get the last inserted user id
        $pre_user_id = $this->con->lastInsertId();

        // Insert into users_info table.
        $stmt = $this->con->prepare("INSERT INTO pre_approved_users_info (pre_user_id, firstname, middlename, lastname, age, street, barangay, city, province, country, email, contactnumber) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmtInfo = $stmt->execute([
            $pre_user_id,
            $this->firstname,
            $this->middlename,
            $this->lastname,
            $this->age,
            $this->street,
            $this->barangay,
            $this->city,
            $this->province,
            $this->country,
            $this->email,
            $this->contactnumber
        ]);

        if ($stmtInfo) {
            $this->response = "Success";
            return true;
        } else {
            // Delete the user if info insert failed
            $stmt = $this->con->prepare("DELETE FROM pre_approved_users WHERE id = ?");
            $stmt->execute([$pre_user_id]);
            $this->response = "Failed to add user information";
            return false;
        }
    }
    return false;
}

    public function getAllPreUsers($limit = 50)
    {
        if (!$this->con) {
            return [];
        }

        $stmt = $this->con->prepare("
        SELECT 
            u.id,
            u.username,
            u.position,

            i.firstname,
            i.middlename,
            i.lastname,
            i.age,
            i.city,
            i.province,
            i.email,
            i.contactnumber

        FROM pre_approved_users u
        LEFT JOIN pre_approved_users_info i 
        ON u.id = i.pre_user_id

        ORDER BY u.id DESC
        LIMIT :limit
    ");

        $stmt->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function approve()
    {
        if (!isset($_GET['id']))
            return;

        $id = $_GET['id'];

        try {
            $this->con->beginTransaction();

            // =========================
            // GET USER DATA (JOIN)
            // =========================
            $stmt = $this->con->prepare("
            SELECT u.*, i.*
            FROM pre_approved_users u
            INNER JOIN pre_approved_users_info i
            ON u.id = i.pre_user_id
            WHERE u.id = ?
        ");
            $stmt->execute([$id]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                throw new \Exception("User not found");
            }

            // =========================
            // INSERT INTO users
            // =========================
            $stmt = $this->con->prepare("
            INSERT INTO users (username, password, position)
            VALUES (?, ?, ?)
        ");
            $stmt->execute([
                $user['username'],
                $user['password'],
                $user['position']
            ]);

            $user_id = $this->con->lastInsertId();

            // =========================
            // INSERT INTO users_info
            // =========================
            $stmt = $this->con->prepare("
            INSERT INTO users_info
            (user_id, firstname, middlename, lastname, age, street, barangay, city, province, country, email, contactnumber)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

            $stmt->execute([
                $user_id,
                $user['firstname'],
                $user['middlename'],
                $user['lastname'],
                $user['age'],
                $user['street'],
                $user['barangay'],
                $user['city'],
                $user['province'],
                $user['country'],
                $user['email'],
                $user['contactnumber']
            ]);

            // =========================
            // DELETE FROM PRE TABLES
            // =========================
            $this->con->prepare("DELETE FROM pre_approved_users WHERE id=?")->execute([$id]);

            // users_info will auto delete because of FK CASCADE
            // but to be safe:
            $this->con->prepare("DELETE FROM pre_approved_users_info WHERE pre_user_id=?")->execute([$id]);

            // =========================
            // EMAIL (APPROVED)
            // =========================
            $this->sendEmail(
                $user['email'],
                "Account Approved",
                "Hello {$user['firstname']}, your account has been approved. You can now login."
            );

            $this->con->commit();

        } catch (\Exception $e) {
            $this->con->rollBack();
            echo $e->getMessage();
        }
    }

    public function reject()
    {
        if (!isset($_GET['id']))
            return;

        $id = $_GET['id'];

        // GET EMAIL FIRST
        $stmt = $this->con->prepare("
        SELECT email, firstname 
        FROM pre_approved_users_info 
        WHERE pre_user_id = ?
    ");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        // DELETE DATA
        $this->con->prepare("DELETE FROM pre_approved_users WHERE id=?")->execute([$id]);
        $this->con->prepare("DELETE FROM pre_approved_users_info WHERE pre_user_id=?")->execute([$id]);

        // SEND EMAIL
        if ($user) {
            $this->sendEmail(
                $user['email'],
                "Account Rejected",
                "Hello {$user['firstname']}, your registration was rejected."
            );
        }
    }

    public function sendEmail($to, $subject, $body)
    {
        require __DIR__ . '/../phpmailer/src/PHPMailer.php';
        require __DIR__ . '/../phpmailer/src/SMTP.php';
        require __DIR__ . '/../phpmailer/src/Exception.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'andrewpablo.neust.student@gmail.com';
            $mail->Password = 'uwlb bpni hhwn llix';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('andrewpablo.neust.student@gmail.com', 'MMBPOS Admin');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = "<p>$body</p>";

            $mail->send();

        } catch (\Exception $e) {
            // optional log
        }
    }
}
?>