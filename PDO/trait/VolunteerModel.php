<?php

trait VolunteerModel
{

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
            $stmt->execute([$image_path, $_POST['bio'], $this->UUID_TO_BIN($id)]);

            $_SESSION['image'] = $image_path;
            $_SESSION['bio']   = $_POST['bio'];

        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    //get public profile

    public function get_volunteer_profile($id)
    {
        $stmt = 'SELECT volunteer_id , volunteer_name , email , volunteer_image_link , volunteer_bio from volunteers where volunteer_id = ? limit 1';

        $this->pdo_initializer();
        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([$this->UUID_TO_BIN($id)]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res;
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

}
