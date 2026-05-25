<?php
    include_once __DIR__."/RescuePointUtility.php";

trait RescuePointModel
{
    use RescuePointUtility;
    
    public function total_rescue_points($offset , $limit)
    {
        $stmt = 'with view_cte as (
            select * from rescue_point limit :offset , :limit
        ) select count(*) from view_cte;';

        $this->pdo_initializer();
        $stmt = $this->pdo->prepare($stmt);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count;
    }

    public function get_all_points($offset = 0 , $limit = 10 )
    {
        try {
            $stmt = "
                    SELECT
                        rescue_point.rescue_point_id as rescue_point_id,
                        rescue_point_name,
                        rescue_point_location_latitude,
                        rescue_point_location_longtitude,
                        emp_name,
                        email,
                        emp_profile_picture_link

                    FROM rescue_point
                    inner join  Employee on Employee.emp_id = rescue_point.supervisor_id 
                    limit :offset , :limit;
                ";
    
            $stmt = $this->pdo->prepare($stmt);
            $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
            $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
            
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $arr =[];
            $arr['count'] = $this->total_rescue_points($offset , $limit);
            $arr['result'] = $result;

            return $arr;
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
                    point_id,
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
            "SELECT
                rescue_point.rescue_point_id,
                rescue_point.rescue_point_name,
                rescue_point_location_latitude,
                rescue_point_location_longtitude,
                rescue_point.supervisor_id        as supervisor_id,
                Supervisor.emp_name                     as supervisor_name
                ,Supervisor.email                       as supervisor_email 
                ,Supervisor.emp_profile_picture_link    as supervisor_image
                ,GROUP_CONCAT(DISTINCT rescue_point_images.image_link SEPARATOR  ';;;' ) as images
                ,GROUP_CONCAT(DISTINCT CONCAT(Assigned.emp_name  ,';;;;' ,Assigned.email ,';;;;' , Assigned.emp_id) SEPARATOR ';;;') as EMP_INFO
                ,GROUP_CONCAT(DISTINCT  animals.animal_id  SEPARATOR ';;;') as Animal_ID
                from rescue_point 
                inner join Employee as Supervisor 
                on Supervisor.emp_id = rescue_point.supervisor_id 
                left join rescue_point_images
                on rescue_point_images.rescue_point_id = rescue_point.rescue_point_id
                left join Employee as Assigned
                on Assigned.rescue_point_id = rescue_point.rescue_point_id
                left join animals 
                on animals.rescue_point_id  = rescue_point.rescue_point_id
                where rescue_point.rescue_point_id = ?
                GROUP BY 
                
                    rescue_point.rescue_point_id 
                    ,rescue_point_location_latitude 
                    ,rescue_point_location_longtitude
                    ,supervisor_id
                
                ;
            ";



            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                $id
            ]);

            $point = $stmt->fetch(PDO::FETCH_ASSOC);

            return $point;

        } catch (PDOException $e) {
            exit("No point found: " . $e->getMessage());
        }
    }
    public function name_already_exists($name)
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

    public function get_point_ID_by_name($name){
        try {

            
            $stmt = "
                    select rescue_point_id from rescue_point
                    where
                    rescue_point_name= ? ;
                ";

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$name]);

            $count = $stmt->fetchColumn();
            return $count;
        } catch (PDOException $e) {
            exit("" . $e->getMessage());
        }
    }
    public function same_place($lat , $lang)
    {

        try {


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
            return $stmt->fetchColumn();

        } catch (PDOException $e) {
            exit("couldn't get any name" . $e->getMessage());
        }
    }

    public function create_rescue_point($manager_id , $lat , $lang , $name)
    {
        try {

            $this->pdo_initializer();

            if ($this->name_already_exists($name)) {
                $msg = "Name already exists";
                return $msg;

            }

            if ($this->same_place($lat , $lang)) {
                $msg = "same place already exists";
                return $msg;
            }
            /*
                START TRANSACTION
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

                COMMIT
            */
            $this->pdo->beginTransaction();


            $stmt1 = $this->pdo->prepare("
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

            $stmt1->bindValue(
                ':name',
                $name,
                PDO::PARAM_STR
            );

            $stmt1->bindValue(
                ':lat',
                (float) $lat
            );

            $stmt1->bindValue(
                ':lang',
                (float) $lang
            );

            $stmt1->bindValue(
                ':supervisor_id',
                $manager_id, PDO::PARAM_STR
            );



            $stmt1->execute();
            $manager = $this->getEmployeeHistoryLatest($manager_id);


            $stmt = $this->pdo->prepare("
                INSERT INTO Employee_history
                (
                    emp_id,
                    event_type,
                    rank_assigned_by,
                    rescue_point_id,
                    emp_rank,
                    salary,
                    reason
                )
                VALUES
                (
                    :emp_id,
                    :event_type,
                    :rank_assigned_by,
                    :rescue_point_id,
                    :emp_rank,
                    :salary,
                    :reason
                )
            ");

            $stmt->bindValue(':emp_id', $manager_id, PDO::PARAM_STR);
            $stmt->bindValue(':event_type', 11, PDO::PARAM_INT);
            $stmt->bindValue(':rank_assigned_by', $_SESSION['id'], PDO::PARAM_STR);
            $stmt->bindValue(':rescue_point_id', $this->get_point_ID_by_name($name), PDO::PARAM_STR);
            $stmt->bindValue(':emp_rank', $manager['emp_rank'], PDO::PARAM_INT);
            $stmt->bindValue(':salary', $manager['salary']);
            $stmt->bindValue(':reason', "Assigned As a Manager", PDO::PARAM_STR);

            $stmt->execute();

            $this->add_notification($manager_id , $_SESSION['id'], "Assigned as a manager");

            $this->pdo->commit();
            $this->change_employee_rank($manager_id ,2 , 2 , $_SESSION['id'] , "Assigned as a Manager"  );
            return $this->get_rescue_point_id_by_name($_POST['name']);

        } catch (PDOException $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            exit('Error occurred when creating the Employee: ' . $e->getMessage());
        }
    }


    public function assign_Employee($rescue_point_id , $employee_id){
        try{

            $stmt = "Update Employee set rescue_point_id = :point_id , 
                    emp_rank =3 where emp_id = :emp_id and emp_rank > 2 ;";
            
            $stmt = $this->pdo->prepare($stmt);

            $stmt->bindValue(
                ':point_id',
                $rescue_point_id
            );

            $stmt->bindValue(
                ':emp_id',
                $employee_id
            );
            
            $stmt->execute();

        }catch(PDOException $e){
            exit('Error occured when assigned employee to a rescue position'. $e->getMessage());
        }
    }

    public function remove_employee_manager($point_id, $past_manager_id, $future_manager_id)
    {
        try {
            /*  
            START TRANSACTION 
                Update rescue_point 
                    set supervisor_id = :future_mangaer_id 
                where rescue_point_id = :point_id; 
                
                Update Employee set rescue_point_id = NULL 
                where emp_id = :past_manager_id;
                
                Update Employee set rescue_point_id = :point_id 
                where emp_id = :future_manager_id; 
                
                COMMIT; 
            */

            $this->pdo->beginTransaction();

            $stmt1 = $this->pdo->prepare("
                UPDATE rescue_point 
                SET supervisor_id = :future_manager_id 
                WHERE rescue_point_id = :point_id
            ");

            $stmt1->execute([
                ':future_manager_id' => $future_manager_id,
                ':point_id' => $point_id
            ]);

            $stmt2 = $this->pdo->prepare("
                UPDATE Employee
                SET rescue_point_id = NULL
                WHERE emp_id = :past_manager_id
                AND rescue_point_id = :point_id
            ");

            $stmt2->execute([
                ':past_manager_id' => $past_manager_id,
                ':point_id' => $point_id
            ]);

            $stmt3 = $this->pdo->prepare("
                UPDATE Employee
                SET rescue_point_id = :point_id
                WHERE emp_id = :future_manager_id
            ");

            $stmt3->execute([
                ':future_manager_id' => $future_manager_id,
                ':point_id' => $point_id
            ]);

            $this->pdo->commit();

        } catch (PDOException $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            exit("Error occurred when removing manager: " . $e->getMessage());
        }
    }

    public function remove_employee_non_manager($point_id , $employee_id){
        try{
            $stmt = "Update Employee set rescue_point_id = NULL , 
                    emp_rank =4 where emp_id = :emp_id and emp_rank > 2 
                    and rescue_point_id  = :point_id ;";
            
            $stmt = $this->pdo->prepare($stmt);

            $stmt->bindValue(
                ':point_id',
                $point_id
            );

            $stmt->bindValue(
                ':emp_id',
                $employee_id
            );
            
            $stmt->execute();

        }catch(PDOException $e){
            exit('Error occured when assigned employee to a rescue position'. $e->getMessage());
        }
    }


    //image upload delete logic complete 

    public function rescue_point_image_upload($rescue_point_id){
        try{
            $image_path = $this->image_upload();
        
            if($image_path === ""){
                $image_path= "https://res.cloudinary.com/dvpwqtobj/image/upload/v1779343058/animal_al93yw.png";
            }
            $stmt = "INSERT INTO rescue_point_images(rescue_point_id , image_link ) 
                    values (? , ?)";

            
            $stmt = $this->pdo->prepare($stmt);

            $stmt->execute([$rescue_point_id , $image_path]);
        }catch(PDOException $e) {
            echo "a PDOException has occured". $e->getMessage();
            exit();
        }

    }

    public function rescue_point_remove_image($rescue_point_id , $rescue_point_image_name){
        try{
                $stmt = "DELETE FROM rescue_point_images where rescue_point_id = ? and image_link = ? ;";
                $stmt = $this->pdo->prepare($stmt);
                $stmt->execute([$rescue_point_id , $rescue_point_image_name]);
            
            }catch(PDOException $e) {
            echo "this was the error". $e->getMessage();
            exit();
        }


    }

}
