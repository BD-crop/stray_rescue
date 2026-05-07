<?php
include_once __DIR__ . "/../mail/verification.php";
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL); // show all errors

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

            $stmt = $this->pdo->prepare("insert into Users(user_name,email , password)
                    values(?,?,?);");
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT)]);
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

            $stmt = $this->pdo->prepare("insert into Employee(emp_name,email , password )
                    values(?,?,?);");
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT)]);
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

            $stmt = $this->pdo->prepare("insert into volunteers(volunteer_name,email , password)
                    values(?,?,?);");
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT)]);
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

            $local_path = uniqid() . "." . $imageFileType;

            $uniqueFileName = $target_dir . $local_path;

            $global_path = "http://localhost:80/dashboard/upload_images/" . $local_path;
            echo $global_path;

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
                return $global_path;
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
            exit(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
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

    //get public profile

    public function get_user_profile($id)
    {
        $stmt = 'SELECT user_id , user_name , email , user_profile_picture_link , user_bio from Users where user_id = ? limit 1';

        $this->pdo_initializer();
        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([$id]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res;
    }

    public function get_admin_profile($id)
    {
        $stmt = 'SELECT emp_id , emp_name , email , emp_profile_picture_link , emp_bio from Employee where emp_id = ? limit 1';

        $this->pdo_initializer();
        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([$id]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res;
    }

    public function get_volunteer_profile($id)
    {
        $stmt = 'SELECT volunteer_id , volunteer_name , email , volunteer_image_link , volunteer_bio from volunteers where volunteer_id = ? limit 1';

        $this->pdo_initializer();
        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([$id]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res;
    }
    public function UUID_GENERATOR()
    {
        $stmt = 'SELECT UUID();';
        $this->pdo_initializer();

        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([]);

        $data = $stmt->fetchColumn();
        return $data;

    }
    public function upload_rescue_post()
    {
        try {
            $uniqid_ = $this->UUID_GENERATOR();
            $stmt    = "insert into rescue_post(rescue_post_id,rescue_post_image_link , rescue_post , animal_species_type , animal_gender_type , animal_age ,post_loc_latitude , post_loc_longtitude ,user_id) values(?,?,?,?,?,?,?,?,?);";

            $global_path = $this->image_upload();
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);

            $stmt->execute([$uniqid_, $global_path, $_POST['post'], $_POST['species_type'], $_POST['gender'], $_POST['age'], $_POST['latitude'], $_POST['longitude'], $_SESSION['id']]);

            return $uniqid_;

        } catch (PDOException $e) {
            exit(json_encode($e->message(), JSON_PRETTY_PRINT));
        }

    }

    public function see_rescue_post($id)
    {

        try {

            $stmt = 'select * from rescue_post where rescue_post_id = ?';
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$id]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            return $data;
        } catch (PDOException $e) {
            exit(json_encode($e->message(), JSON_PRETTY_PRINT));
        }
        return "";
    }

    public function total_rescue_posts()
    {
        $stmt = 'select COUNT(*) from rescue_post';

        $this->pdo_initializer();
        $stmt = $this->pdo->prepare($stmt);
        $stmt->execute([]);
        $count = $stmt->fetchColumn();

        return $count;

    }

    public function see_rescue_posts($offset, $limit = 10)
    {
        $this->pdo_initializer();

        $stmt = $this->pdo->prepare('SELECT * FROM rescue_post ORDER BY post_time_stamp DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)($offset * $limit), PDO::PARAM_INT);

        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $count = $this->total_rescue_posts();

        return ['count' => $count, 'posts' => $posts];
    }

    public function add_rescue_point(){
        
    }
}
