<?php
    class PDO_class{

        public static $PDO_obj ;

        public $user = "root";
        public $password = "";
        public $dsn = "mysql:host=localhost;dbname=stray_rescue;port=3306";
        public  $dsn2="mysql:host=localhost;port=3306";
        public $pdo;


        public static function initializer() : PDO_class{
            if(self::$PDO_obj !=  null){
                return self::$PDO_obj;

            }   

            self::$PDO_obj = new PDO_class();

            return self::$PDO_obj;
        }


        function __construct(){
            try {
                $this->pdo = new PDO($this->dsn2 , $this->user , $this->password );
                $sql_content = file_get_contents(__DIR__."/project_database.sql");
                $this->pdo ->exec($sql_content);

                $this->pdo = new PDO($this->dsn, $this->user, $this->password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



            } catch (PDOException $e) {
                exit("Connection failed: " . $e->getMessage());
            }
        }


        public function __clone(){}

        function pdo_initializer(){
            try {


                $this->pdo = new PDO($this->dsn, $this->user, $this->password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



            } catch (PDOException $e) {
                exit("Connection failed: " . $e->getMessage());
            }

        }

        //signup 

            public function user_insert($name,$email ,$password){
                try {

                    $stmt= $this->pdo->prepare("insert into Users(user_name,email , password ,user_profile_picture_link) 
                    values(?,?,?,?);");
                    $stmt->execute([$name , $email ,password_hash($password , PASSWORD_BCRYPT),""]);



                } catch (PDOException $e) {
                    exit("Connection failed: " . $e->getMessage());
                }
            }
            public function admin_insert($name,$email ,$password){
                try {

                    $stmt= $this->pdo->prepare("insert into Employee(emp_name,email , password ,emp_profile_picture_link) 
                    values(?,?,?,?);");
                    $stmt->execute([$name , $email ,password_hash($password , PASSWORD_BCRYPT),""]);



                } catch (PDOException $e) {
                    exit("Connection failed: " . $e->getMessage());
                }
            }
            public function volunteer_insert($name,$email ,$password){
                try {

                    $stmt= $this->pdo->prepare("insert into volunteers(volunteer_name,email , password ,volunteer_image_link) 
                    values(?,?,?,?);");
                    $stmt->execute([$name , $email ,password_hash($password , PASSWORD_BCRYPT),""]);



                } catch (PDOException $e) {
                    exit("Connection failed: " . $e->getMessage());
                }
            }


        //login
        
            public function email_checker($email ,$table){
                try {


                    $stmt= $this->pdo->prepare("select count(email) from ".$table." where email = ?;");
                    $stmt->execute([$email]);
                    
                    $count = $stmt->fetchColumn();


                    if($count == 0){
                        return false;
                    }

                    return true;


                } catch (PDOException $e) {
                    exit("Connection failed: " . $e->getMessage());
                }
            }

        
            public function password_checker($email, $password, $table){
                try {

                    $stmt = $this->pdo->prepare(
                        "SELECT password FROM $table WHERE email = ?"
                    );

                    $stmt->execute([$email]);

                    $hash = $stmt->fetchColumn();

                    if (!$hash) {
                        return false; 
                    }

                    return password_verify($password, $hash);

                } catch (PDOException $e) {
                    exit("Connection failed: " . $e->getMessage());
                }
            }



    };


?>