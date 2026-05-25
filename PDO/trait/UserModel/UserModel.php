<?php
include_once __DIR__."/UserUtility.php";
trait UserModel
{
    use UserUtility;
    public function update_user(){
        $sql="UPDATE Users set user_profile_picture_link = ? , user_bio= ? where user_id = ?;";
        $this->initializer();


        $sql = $this->pdo->prepare($sql);
        
        $image_path = $this->image_upload();

        if ($image_path === "") {
            $image_path = "https://res.cloudinary.com/dvpwqtobj/image/upload/v1757076286/user_xhxvc9.png";
        }
        
        $sql->execute( [$image_path, $_POST['add_bio'] ,  $_POST['id']]);
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

    public function update_bio_user($id)
    {
        try {
            $image_path = $this->image_upload();

            if ($image_path === "") {
                $image_path = "https://res.cloudinary.com/dvpwqtobj/image/upload/v1757076286/user_xhxvc9.png";
            }

            $stmt = "UPDATE Users SET user_profile_picture_link = ?, user_bio = ?git  WHERE user_id = ?";
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$image_path, $_POST['bio'], $id]);

            $_SESSION['image'] = $image_path;
            $_SESSION['bio']   = $_POST['bio'];

        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function get_user_profile($id)
    {
        $stmt = 'SELECT user_id , user_name , email , user_profile_picture_link , user_bio from Users where user_id = ? limit 1';

        $this->pdo_initializer();
        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([$id]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res;
    }

}
