<?php

trait UtilityModel
{
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

    public function get_raw_id($id, $table_name){

    }

    public function email_verification($email_verification_id)
    {
        try {
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare("
                UPDATE email_verification
                SET is_verified = 'Y'
                WHERE email_verification_id = ?
            ");

            $bin = $email_verification_id;

            $stmt->execute([$bin]);

            echo "
                <h2>Email verification successful</h2>
                <h4>login to access the service</h4>
            ";

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
            return $hash === $password;
        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }
    public function email_verification_insert($email_id, $table_name)
    {
        try {
            $this->initializer();
            $stmt = $this->pdo->prepare("insert into email_verification( email_verification_id ,email_id,table_name)
                    values(? ,?,?);");

            $uuid_id = $this->UUID_GENERATOR();

            $stmt->execute([$uuid_id, $email_id, $table_name]);

            return $uuid_id;
        } catch (PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }

        return null;
    }

    public function image_upload()
    {
        if (!isset($_FILES['fileToUpload'])) {
            return "";
        }

        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/dashboard/upload_images/";

        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                exit(json_encode([
                    "msg" => "Failed to create upload directory",
                    "path" => $target_dir
                ]));
            }
        }

        $file = $_FILES['fileToUpload'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            exit(json_encode([
                "msg" => "File upload error",
                "code" => $file['error']
            ]));
        }

        $imageFileType = strtolower(
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        $allowed_file_types = ['png', 'jpeg', 'jpg', 'webp'];

        if (!in_array($imageFileType, $allowed_file_types)) {
            exit(json_encode([
                "msg" => "Wrong file type"
            ]));
        }

        if ($file['size'] > 5000000) {
            exit(json_encode([
                "msg" => "Image file exceeds 5MB"
            ]));
        }

        $local_path = uniqid('', true) . "." . $imageFileType;

        $uniqueFileName = $target_dir . $local_path;

        $global_path = "http://localhost/dashboard/upload_images/" . $local_path;

        if (!move_uploaded_file($file['tmp_name'], $uniqueFileName)) {
            exit(json_encode([
                "msg" => "Failed to move uploaded file"
            ]));
        }

        return $global_path;
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


}
