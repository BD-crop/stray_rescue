<?php
include_once __DIR__ . "/../mail/verification.php";

class PDO_class
{

    public static $PDO_obj;

    public $user     = "root";
    public $password = "";
    public $dsn      = "mysql:host=localhost;dbname=stray_rescue;port=3306";
    public $dsn2     = "mysql:host=localhost;port=3306";
    public $pdo;

    public static function initializer(): PDO_class
    {
        if (self::$PDO_obj != null) {
            return self::$PDO_obj;

        }

        self::$PDO_obj = new PDO_class();

        return self::$PDO_obj;
    }

    public function __construct()
    {
        try {
            $this->pdo   = new PDO($this->dsn2, $this->user, $this->password);
            $sql_content = file_get_contents(__DIR__ . "/project_database.sql");
            $this->pdo->exec($sql_content);

            $this->pdo = new PDO($this->dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }

    public function __clone()
    {}

    public function pdo_initializer()
    {
        try {

            $this->pdo = new PDO($this->dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }

    }

    //signup

    public function email_verification_insert($email_id, $table_name)
    {
        try {

            $stmt = $this->pdo->prepare("insert into email_verification(email_id,table_name)
                    values(?,?);");
            $stmt->execute([$email_id, $table_name]);

        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }
    public function user_insert($name, $email, $password)
    {
        try {

            $stmt = $this->pdo->prepare("insert into Users(user_name,email , password ,user_profile_picture_link)
                    values(?,?,?,?);");
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT), ""]);
            $this->email_verification_insert($email, "Users");
            $lastUUID = $this->pdo->lastInsertId();
            send_mail($name, $email, $lastUUID, "Users");

        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }
    public function admin_insert($name, $email, $password)
    {
        try {

            $stmt = $this->pdo->prepare("insert into Employee(emp_name,email , password ,emp_profile_picture_link)
                    values(?,?,?,?);");
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT), ""]);
            $this->email_verification_insert($email, "Employee");

            $lastUUID = $this->pdo->lastInsertId();
            send_mail($name, $email, $lastUUID, "Employee");

        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }
    public function volunteer_insert($name, $email, $password)
    {
        try {

            $stmt = $this->pdo->prepare("insert into volunteers(volunteer_name,email , password ,volunteer_image_link)
                    values(?,?,?,?);");
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT), ""]);
            $this->email_verification_insert($email, "volunteers");
            $lastUUID = $this->pdo->lastInsertId();
            send_mail($name, $email, $lastUUID, "volunteers");

        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }
    //email verification

    public function email_verification($email, $table_name)
    {
        try {
            $stmt = $this->pdo->prepare("Update  email_verification set is_verified='Y' where email_id=? and table_name=?;");
            $stmt->execute([$email, $table_name]);

        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }

    //login

    public function email_checker($email, $table)
    {
        try {

            $stmt = $this->pdo->prepare(
                "select count(email) from " . $table . " where email = ?;");
            $stmt->execute([$email]);

            $count = $stmt->fetchColumn();

            if ($count == 0) {
                return false;
            }

            return true;

        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }

    public function login_email_checker($email, $table)
    {
        try {

            $stmt = $this->pdo->prepare(
                "select count(email) from " . $table . " as t1 inner join email_verification as e1 on t1.email=e1.email_id and e1.table_name=? where email = ?  and is_verified ='Y' ;");
            $stmt->execute([$table, $email]);

            $count = $stmt->fetchColumn();

            if ($count == 0) {
                return false;
            }

            return true;

        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }

    public function password_checker($email, $password, $table)
    {
        try {

            $stmt = $this->pdo->prepare(
                "SELECT password FROM $table WHERE email = ?"
            );

            $stmt->execute([$email]);

            $hash = $stmt->fetchColumn();

            if (! $hash) {
                return false;
            }

            return password_verify($password, $hash);

        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }

    public function get_id($id, $email, $table_name)
    {
        try {
            $this->pdo_initializer();

            $stmt = "SELECT `$id` FROM `$table_name` WHERE email = ?";

            $stmt = $this->pdo->prepare($stmt);

            $stmt->execute([$email]);

            $id = $stmt->fetchColumn();

            return $id;
        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }

    public function image_upload()
    {
        if (isset($_POST['submit'])) {

            $target_dir = "./../upload_images/";

            $uploadOk      = 1;
            $imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));

            $uniqueFileName = $target_dir . uniqid() . "." . $imageFileType;

            $allowed_file_types = ['png', 'jpeg', 'jpg', 'webp'];

            if (! in_array($imageFileType, $allowed_file_types)) {
                $array['msg'] = "Wrong file type";
                exit(json_encode($array, JSON_PRETTY_PRINT));
            } else {
            }

            if ($_FILES["fileToUpload"]["size"] > 5000000) {
                $array['msg'] = "Image file exceeds 5MB";
                exit(json_encode($array, JSON_PRETTY_PRINT));
            } else {
            }


            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $uniqueFileName)) {
                return $uniqueFileName;
            } else {
                $array['msg'] = "Error when uploading the file";
                exit(json_encode($array, JSON_PRETTY_PRINT));
            }
        } else {
        }

        return "";
    }
    public function update_bio_user($id)
    {
        try {
            $image_path = $this->image_upload();

            if ($image_path === "") {
                $image_path = "default_image.jpg";
            }

            $stmt = "UPDATE Users SET user_profile_picture_link = ?, user_bio = ? WHERE user_id = ?";
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$image_path, $_POST['bio'], $id]);

            $_SESSION['image'] = $image_path;
            $_SESSION['bio']   = $_POST['bio'];

        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function update_bio_employee($id)
    {
        try {
            $image_path = $this->image_upload();

            if ($image_path === "") {
                $image_path = "default_image.jpg";
            }

            $stmt = "UPDATE Employee SET emp_profile_picture_link = ? , emp_bio = ? WHERE emp_id = ?";
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$image_path, $_POST['bio'], $id]);

            $_SESSION['image'] = $image_path;
            $_SESSION['bio']   = $_POST['bio'];

        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function update_bio_volunteer($id)
    {
        try {
            $image_path = $this->image_upload();

            if ($image_path === "") {
                $image_path = "default_image.jpg";
            }

            $stmt = "UPDATE volunteers SET volunteer_image_link = ?, volunteer_bio = ? WHERE volunteer_id = ?";
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$image_path, $_POST['bio'], $id]);

            $_SESSION['image'] = $image_path;
            $_SESSION['bio']   = $_POST['bio'];

        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

}
