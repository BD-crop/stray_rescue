<?php

trait RescuePointModel
{
    public function get_all_points()
    {
        try {
            $stmt = "
                    SELECT

                        LOWER(CONCAT(
                            SUBSTR(HEX(rescue_point.rescue_point_id), 1, 8), '-',
                            SUBSTR(HEX(rescue_point.rescue_point_id), 9, 4), '-',
                            SUBSTR(HEX(rescue_point.rescue_point_id), 13, 4), '-',
                            SUBSTR(HEX(rescue_point.rescue_point_id), 17, 4), '-',
                            SUBSTR(HEX(rescue_point.rescue_point_id), 21)
                        )) AS rescue_point_id,

                        rescue_point_name,
                        rescue_point_location_latitude,
                        rescue_point_location_longtitude,
                        emp_name,
                        email,
                        emp_profile_picture_link

                    FROM rescue_point
                    inner join  Employee on Employee.emp_id = rescue_point.supervisor_id;
                ";

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            exit("exception occured" . $e->getMessage());
        }
    }
    public function get_points_by_name($name , $rankBy ,$order){
        try{
            $stmt = "
                with rescue_point_cte as (
                    select rescue_point.rescue_point_id   as point_id,
                    rescue_point.rescue_point_name        as point_name ,
                    count(DISTINCT rescue_point_images.image_link) as image_count,
                    count(DISTINCT Emp.emp_id)                     as emp_count,
                    count(DISTINCT animals.animal_id)              as animal_count,
                    rescue_point.creation_date            as creation_date,
                    Supervisor.email                      as supervisor_email
                    from rescue_point
                    left join rescue_point_images 
                    on rescue_point_images.rescue_point_id = rescue_point.rescue_point_id
                    inner join Employee as Supervisor
                    on Supervisor.emp_id = rescue_point.supervisor_id
                    left join Employee as Emp
                    on Emp.rescue_point_id = rescue_point.rescue_point_id
                    left join animals 
                    on animals.rescue_point_id = rescue_point.rescue_point_id
                    where rescue_point.rescue_point_name like concat(substr(:name , 1 ,1 ),'%')
                    group by 
                        rescue_point.rescue_point_id ,
                        rescue_point.rescue_point_name, 
                        rescue_point.creation_date,
                        Supervisor.email                      
                    )
                    SELECT
                    LOWER(CONCAT(
                        SUBSTR(HEX(point_id), 1, 8), '-',
                        SUBSTR(HEX(point_id), 9, 4), '-',
                        SUBSTR(HEX(point_id), 13, 4), '-',
                        SUBSTR(HEX(point_id), 17, 4), '-',
                        SUBSTR(HEX(point_id), 21)
                    )) AS point_id,
                    point_name,
                    image_count,
                    emp_count,
                    animal_count,
                    creation_date ,
                    supervisor_email,
                    dense_rank() over(order by ".$rankBy." ".$order.") as rank,  
                    levenshtein(point_name , :name) as distance
                    FROM rescue_point_cte
                order by ".$rankBy." ".$order." ;
            ";

            $this->pdo_initializer();
            $stmt = $this->pdo->prepare($stmt);
            $stmt -> bindValue(":name", $name, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;


        }catch(PDOException $e){
            exit("". $e->getMessage());
        }
    }
    
    public function get_point_by_id($id)
    {
        try {

            $this->pdo_initializer();

            $sql = 
            "
                SELECT  rescue_point_id ,
                rescue_point_location_latitude,
                rescue_point_location_longtitude,
                supervisor_id,
                (select rescue_point_images.image_link from rescue_point_images 
                where rescue_point_images.rescue_point_id = :id) as image_links
                from rescue_point;
            ";



            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                $this->UUID_TO_BIN(($id)),
            ]);

            $point = $stmt->fetch(PDO::FETCH_ASSOC);

            return $point;

        } catch (PDOException $e) {
            exit("No point found: " . $e->getMessage());
        }
    }
    public function name_already_exists()
    {
        try {

            $name = $_POST['name'];
            $stmt = "
                    select Count(*) from rescue_point
                    where
                    rescue_point_name= ? ;
                ";

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$name]);

            $count = $stmt->fetchColumn();
            if ($count == 0) {
                return false;
            } else {
                return true;
            }
        } catch (PDOException $e) {
            exit("" . $e->getMessage());
        }

    }

    public function same_place()
    {

        try {

            $lat  = $_POST['lat'];
            $lang = $_POST['lang'];

            $sql = "
                    SELECT COUNT(*)
                    FROM rescue_point
                    WHERE
                        rescue_point_location_latitude = :lat
                    AND
                        rescue_point_location_longtitude = :lang
                ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(
                ':lat',
                (float) $lat
            );

            $stmt->bindValue(
                ':lang',
                (float) $lang
            );

            $stmt->execute();

            $count = $stmt->fetchColumn();

            return $count > 0;

        } catch (PDOException $e) {

            exit($e->getMessage());
        }
    }

    public function get_rescue_point_id_by_name($name)
    {
        try {
            $this->pdo_initializer();
            $stmt = $this->pdo->prepare(
                "select rescue_point_id from rescue_point where rescue_point_name = ?;"
            );

            $stmt->execute([$name]);
            return $this->BIN_TO_UUID($stmt->fetchColumn());

        } catch (PDOException $e) {
            exit("couldn't get any name" . $e->getMessage());
        }
    }

    public function create_rescue_point()
    {
        try {

            $this->pdo_initializer();

            if ($this->name_already_exists()) {
                $msg = urlencode("Name already exists");
                header("Location: http://localhost:80/dashboard/admin/createRescuePoint.php?msg=$msg");
                exit();

            }

            if ($this->same_place()) {
                $msg = urlencode("same place already exists");
                header("Location: http://localhost:80/dashboard/admin/createRescuePoint.php?msg=$msg");
                exit();
            }

            $stmt = $this->pdo->prepare("
                    INSERT INTO rescue_point(
                        rescue_point_name,
                        rescue_point_location_latitude,
                        rescue_point_location_longtitude,
                        supervisor_id
                    )
                    VALUES(
                        :name,
                        :lat,
                        :lang,
                        :supervisor_id
                    );
                ");

            $bin = $this->UUID_TO_BIN($_POST['manager_id']);

            $stmt->bindValue(
                ':name',
                $_POST['name'],
                PDO::PARAM_STR
            );

            $stmt->bindValue(
                ':lat',
                (float) $_POST['lat']
            );

            $stmt->bindValue(
                ':lang',
                (float) $_POST['lang']
            );

            $stmt->bindValue(
                ':supervisor_id',
                $bin,
                PDO::PARAM_LOB
            );

            $stmt->execute();

            return $this->get_rescue_point_id_by_name($_POST['name']);

        } catch (PDOException $e) {
            $msg = urlencode("same place already exists");
            header("Location: http://localhost:80/dashboard/admin/createRescuePoint.php?msg=$msg");
            exit();
        }
    }

    public function assign_manager($rescue_point_id, $rescue_point)
    {

    }
}
