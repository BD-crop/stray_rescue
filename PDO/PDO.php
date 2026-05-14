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
            $this->initializer();
            $stmt = $this->pdo->prepare("insert into email_verification( email_verification_id ,email_id,table_name)
                    values(UNHEX(REPLACE(? , '-','')),?,?);");

            $uuid_id = $this->UUID_GENERATOR();

            $stmt->execute([$uuid_id, $email_id, $table_name]);

            return $uuid_id;
        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }

        return null;
    }

    public function user_insert($name, $email, $password)
    {
        try {

            $stmt = $this->pdo->prepare("insert into Users(user_name,email , password)
                    values(?,?,?);");
// production -->   $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT)]);
            $stmt->execute([$name, $email, $password]);
            $lastUUID = $this->email_verification_insert($email, "Users");

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
// production -->   $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT)]);
            $stmt->execute([$name, $email, $password]);
            $lastUUID = $this->email_verification_insert($email, "Employee");

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
// production -->   $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT)]);
            $stmt->execute([$name, $email, $password]);            
            $lastUUID = $this->email_verification_insert($email, "volunteers");

            send_mail($name, $email, $lastUUID, "volunteers");

        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }

    // rescue_point_creation

    public function name_already_exists(){
        try{

            $name = $_POST['name'];
            $stmt ="
                select Count(*) from rescue_point 
                where
                rescue_point_name= ? ; 
            ";

            $stmt = $this->pdo->prepare($stmt); 
            $stmt->execute([$name]);    

            $count = $stmt->fetchColumn();
            if($count == 0){    
                return false;
            }else{  
                return true;
            }
        }catch (PDOException $e) {
            exit("". $e->getMessage());
        }

    }

    public function same_place(){
        try{

            $lat = $_POST['lat'];
            $lang = $_POST['lang'];
            
            $stmt = $this->pdo->prepare('SELECT * FROM rescue_post ORDER BY post_time_stamp DESC LIMIT :limit OFFSET :offset');

            
            $stmt ="
                select Count(*) from rescue_point 
                where
                rescue_point_location_latitude = :lat and
                rescue_point_location_longtitude = :lang   ; 
            ";

            $stmt->bindValue(':lat', (float) $lat);
            $stmt->bindValue(':lang', (float) $lang);

            $stmt = $this->pdo->prepare($stmt); 
            $stmt->execute([$lat , $lang]);    

            $count = $stmt->fetchColumn();
            if($count == 0){    
                return false;
            }else{  
                return true;
            }
        }catch (PDOException $e) {
            exit("". $e->getMessage());
        }

    }
    
    public function get_rescue_point_id_by_name($name){
        try{
            $this->pdo_initializer();
            $stmt = $this->pdo->prepare(
                "select rescue_point_id from rescue_point where rescue_point_name = ?;"
            );

            $stmt->execute([$name]);
            return $this->BIN_TO_UUID($stmt->fetchColumn());


        }catch(PDOException $e){
            exit("couldn't get any name". $e->getMessage()); 
        }
    }

    public function create_rescue_point(){
        try {
            $this->pdo_initializer();

            if($this->name_already_exists()){
                $message;
                $message['msg'] ="Similar rescue point name already exists ";
                exit(   json_encode($message , JSON_PRETTY_PRINT));
            }

            if($this->same_place()){
                $message;
                $message['msg'] ="similar rescue point location already exists ";
                exit(   json_encode($message , JSON_PRETTY_PRINT));
            }


            $stmt = $this->pdo->prepare("
                insert into 
                rescue_point(
                rescue_point_name , 
                rescue_point_location_latitude,
                rescue_point_location_longtitude,
                supervisor_id
                )
                values 
                (?,?,?,?);
            ");

            $bin = $this->UUID_TO_BIN($_POST['id']);

            $stmt->execute([$_POST['name'] , $_POST['lat'] , $_POST['lang'] , $bin]);

            echo "Affected rows: " . $stmt->rowCount();

        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }

    //email verification

    public function email_verification($email_verification_id)
    {
        try {
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare("
                UPDATE email_verification
                SET is_verified = 'Y'
                WHERE email_verification_id = ?
            ");

            $bin = $this->UUID_TO_BIN($email_verification_id);

            $stmt->execute([$bin]);

            echo "Affected rows: " . $stmt->rowCount();

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
                "select count(t1.email) from " . $table . " as t1 inner join email_verification as e1 on t1.email=e1.email_id and e1.table_name=? where email = ?  and is_verified ='Y' ;");
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

            // return password_verify($password, $hash);
            return $hash === $password ;
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

            $id = $this->BIN_TO_UUID($stmt->fetchColumn());

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

            $stmt = "UPDATE Users SET user_profile_picture_link = ?, user_bio = ?git  WHERE user_id = ?";
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$image_path, $_POST['bio'], $this->UUID_TO_BIN($id)]);

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
            $stmt->execute([$image_path, $_POST['bio'], $this->UUID_TO_BIN($id)]);

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
            $stmt->execute([$image_path, $_POST['bio'],$this->UUID_TO_BIN($id)]);

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

        $stmt->execute([$this -> UUID_TO_BIN($id)]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res;
    }

    public function get_admin_profile($id)
    {
        $stmt = 'SELECT emp_id , emp_name , email , emp_profile_picture_link , emp_bio from Employee where emp_id = ? limit 1';

        $this->pdo_initializer();
        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([$this -> UUID_TO_BIN($id)]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res;
    }

    public function get_volunteer_profile($id)
    {
        $stmt = 'SELECT volunteer_id , volunteer_name , email , volunteer_image_link , volunteer_bio from volunteers where volunteer_id = ? limit 1';

        $this->pdo_initializer();
        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([$this -> UUID_TO_BIN($id)]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res;
    }

    public function upload_rescue_post()
    {
        try {
            if(!( isset($_POST['fileToUpload']) && isset($_POST['submit']) && isset($_POST['post']) && isset($_POST['species_type']) && isset($_POST['gender']) && isset($_POST['age']) && isset( $_POST['latitude'] ) && isset($_POST['longitude']))){
                http_response_code(400);
                $msg['msg'] = 'all fields need to be present';
                exit(json_encode($msg , JSON_PRETTY_PRINT));
            }
            $uniqid_ = $this->UUID_GENERATOR();
            $stmt    = "insert into rescue_post(rescue_post_id,rescue_post_image_link , rescue_post , animal_species_type , animal_gender_type , animal_age ,post_loc_latitude , post_loc_longtitude ,user_id) values(?,?,?,?,?,?,?,?,?);";

            $global_path = $this->image_upload() ?? '';

            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);

            $stmt->execute([$this->UUID_TO_BIN( $uniqid_), $global_path, $_POST['post'], $_POST['species_type'], $_POST['gender'], $_POST['age'], $_POST['latitude'], $_POST['longitude'], $this->UUID_TO_BIN(  $_SESSION['id'])]);

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
            $stmt->execute([$this->UUID_TO_BIN( $id)]);

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
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) ($offset * $limit), PDO::PARAM_INT);

        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $count = $this->total_rescue_posts();

        return ['count' => $count, 'posts' => $posts];
    }

    // employee management
    public function get_all_employee($rank , $name)
    {
        try {
            $stmt = "select emp_id ,emp_profile_picture_link, emp_name , email , emp_rank from Employee where emp_rank > ? and emp_name like ? ;";
            $this->pdo_initializer();


            $s = str_split($name);
            $temp = "";
            foreach ($s as $char) {
                $temp .= "%" . $char;
            }
            $temp .= "%";
            

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$rank , $temp]);
            
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $employees;

        } catch (PDOException $e) {
            exit(json_encode($e->message(), JSON_PRETTY_PRINT));
        }
    }

    public function find_employee_level(){
        $stmt = 'select emp_rank from Employee where emp_id = ?;';
        $id   = $_SESSION['id'];

        $this->pdo_initializer();
        $stmt = $this->pdo->prepare($stmt);
        $stmt->execute([$this->UUID_TO_BIN($id)]);
        $count = $stmt->fetchColumn();
        
        // if(!$count){
        //     http_response_code(400);
        //     $msg ;
        //     $msg['msg'] = 'Something went wrong';

        //     exit(json_encode( $msg , JSON_PRETTY_PRINT ));
        // }
        return $count ;
    }
    
    public function get_emplyee_info(){
        $stmt = 'select 
        E1.emp_id ,
        E1.emp_bio ,
        E1.emp_name,
        E1.emp_rank ,
        E1.email ,
        E1.salary ,
        E1.joing_date ,
        E1.emp_profile_picture_link ,
        E1.rank_assign_date ,
        E1.immediate_supervisor_id as supervisor_id ,

        E2.emp_name as supervisor_name ,
        E2.email as supervisor_email ,
        E2.emp_profile_picture_link as supervisor_profile_image 

        from Employee as E1 

        left join Employee as E2 
        on E1.immediate_supervisor_id = E2.emp_id 

        where E1.emp_id = ? ;';

        $id = $_SESSION['id'];

        $this->pdo_initializer();

        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([
            $this->UUID_TO_BIN($id)
        ]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        // if(!$res){

        //     http_response_code(400);

        //     $msg = [];
        //     $msg['msg'] = 'Something went wrong';

        //     exit(json_encode($msg , JSON_PRETTY_PRINT));
        // }

        $result = [];

        $result['emp_id'] = $this->BIN_TO_UUID($res['emp_id']);
        $result['emp_bio'] = $res['emp_bio'];
        $result['emp_name'] = $res['emp_name'];
        $result['emp_rank'] = $res['emp_rank'];
        $result['email'] = $res['email'];
        $result['salary'] = $res['salary'];
        $result['joing_date'] = $res['joing_date'];
        $result['emp_profile_picture_link'] = $res['emp_profile_picture_link'];
        $result['rank_assign_date'] = $res['rank_assign_date'];

        $result['supervisor_id'] = $this->BIN_TO_UUID($res['supervisor_id']);
        $result['supervisor_name'] = $res['supervisor_name'];
        $result['supervisor_email'] = $res['supervisor_email'];
        $result['supervisor_profile_image'] = $res['supervisor_profile_image'];

        return $result;
    }



    public function assign_manager($rescue_point_id, $rescue_point)
    {

    }

    // utility function -- only utility functions will come after this

    public function UUID_GENERATOR()
    {
        $stmt = 'SELECT UUID();';
        $this->pdo_initializer();

        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([]);

        $data = $stmt->fetchColumn();
        return $data;

    }

    public function UUID_TO_BIN($key)
    {
        return hex2bin(str_replace('-', '', $key));
    }

    function BIN_TO_UUID($bin)
    {
        $hex = bin2hex($bin);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20)
        );
    }




}
