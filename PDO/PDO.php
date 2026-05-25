<?php
include_once __DIR__ . "/../mail/verification.php";
// include_once __DIR__ ."/../error_print.php";

include_once __DIR__ . "/trait/EmployeeModel.php";
include_once __DIR__ . "/trait/AuthModel.php";
include_once __DIR__ . "/trait/RescuePointModel/RescuePointModel.php";
include_once __DIR__ . "/trait/UtilityModel.php";
include_once __DIR__ . "/trait/VolunteerModel/VolunteerModel.php";
include_once __DIR__ . "/trait/UserModel/UserModel.php";
include_once __DIR__ . "/trait/RescuePostModel/RescuePost.php";
include_once __DIR__ ."/trait/marketplace/analyticsModel.php";
include_once __DIR__ . "/trait/marketplace/orderModel.php";
include_once __DIR__ . "/trait/marketplace/ProductModel.php";
include_once __DIR__."/trait/PetCenters/PetCenterModel.php";

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
            // $this->pdo   = new PDO($this->dsn2, $this->user, $this->password);
            // $sql_content = file_get_contents(__DIR__ . "/project_database.sql");
            // $this->pdo->exec($sql_content);

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
    use PetCenterModel;
    use EmployeeModel;
    use AuthModel;
    use UtilityModel;
    use RescuePointModel;
    use VolunteerModel;
    use UserModel;
    use RescuePost;
    use orderModel;
    use ProductModel;
    use analyticsModel;
}
