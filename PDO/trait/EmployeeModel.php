<?php


trait EmployeeModel
{
    public function createEmployeeForce(
        $name,
        $email,
        $emp_rank,
        $password,
        $salary,
        $emp_profile_picture_link,
        $immediate_supervisor_id = NULL,
        $created_by
    ) {
        try {

            $this->pdo_initializer();
            $this->pdo->beginTransaction();

            $id = $this->UUID_GENERATOR();


            $stmt = $this->pdo->prepare(
                "INSERT INTO Employee
            (
                emp_id,
                emp_name,
                email,
                emp_rank,
                password,
                salary,
                emp_profile_picture_link,
                immediate_supervisor_id
            )
            VALUES
            (
                :emp_id,
                :emp_name,
                :email,
                :emp_rank,
                :password,
                :salary,
                :emp_profile_picture_link,
                :immediate_supervisor_id
            )"
            );

            if (
                !$stmt->execute([
                    ':emp_id' => $id,
                    ':emp_name' => $name,
                    ':email' => $email,
                    ':emp_rank' => $emp_rank,
                    ':password' => $password,
                    ':salary' => $salary,
                    ':emp_profile_picture_link' => $emp_profile_picture_link,
                    ':immediate_supervisor_id' => $immediate_supervisor_id
                ])
            ) {
                print_r($stmt->errorInfo());
                exit("STEP 1 FAILED: Employee insert");
            }

            echo "STEP 1 OK: Employee inserted\n";


            $stmt = $this->pdo->prepare(
                "INSERT INTO Employee_history
            (
                emp_id,
                event_type,
                rank_assigned_by,
                supervisor_id,
                rescue_point_id,
                emp_rank,
                salary,
                reason
            )
            VALUES
            (
                :emp_id,
                1,
                :rank_assigned_by,
                :supervisor_id,
                NULL,
                :emp_rank,
                :salary,
                :reason
            )"
            );

            if (
                !$stmt->execute([
                    ':emp_id' => $id,
                    ':rank_assigned_by' => $created_by,
                    ':supervisor_id' => $immediate_supervisor_id,
                    ':emp_rank' => $emp_rank,
                    ':salary' => $salary,
                    ':reason' => "Employee Created"
                ])
            ) {
                print_r($stmt->errorInfo());
                exit("STEP 2 FAILED: Employee_history insert");
            }

            echo "STEP 2 OK: History inserted\n";


            $stmt = $this->pdo->prepare(
                "INSERT INTO email_verification
            (
                email_verification_id,
                email_id,
                table_name,
                is_verified
            )
            VALUES
            (
                :email_verification_id,
                :email_id,
                :table_name,
                'Y'
            )"
            );

            if (
                !$stmt->execute([
                    ':email_verification_id' => $this->UUID_GENERATOR(),
                    ':email_id' => $email,
                    ':table_name' => "Employee"
                ])
            ) {
                print_r($stmt->errorInfo());
                exit("STEP 3 FAILED: Email verification insert");
            }

            echo "STEP 3 OK: Email verification inserted\n";


            $this->pdo->commit();


            return true;

        } catch (PDOException $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            echo "<pre>";
            echo "PDO EXCEPTION:\n";
            echo $e->getMessage();
            echo "</pre>";

            exit;
        }

        return false;
    }



    public function removeEmployeeForce($emp_id)
    {
        try {

            $this->pdo_initializer();

            $email = $this->getEmployee_email($emp_id);

            $this->pdo->beginTransaction();

            /*
                START TRANSACTION
                    UPDATE animals SET emp_id = NULL WHERE emp_id = ?;
                    DELETE FROM Employee_history where emp_id = ?;
                    DELETE FROM Employee where emp_id = ?;
                    DELETE FROM EmployeeNotification where emp_id = ?;
                    DELETE FROM email_verification where email_id = ?; 

                COMMIT;
            */


            $stmt = $this->pdo->prepare(
                "UPDATE animals 
             SET emp_id = NULL 
             WHERE emp_id = ?"
            );

            $stmt->execute([$emp_id]);


            $stmt = $this->pdo->prepare(
                "DELETE FROM Employee_history 
             WHERE emp_id = ?"
            );

            $stmt->execute([$emp_id]);


            $stmt = $this->pdo->prepare(
                "DELETE FROM EmployeeNotification 
             WHERE emp_id = ?"
            );

            $stmt->execute([$emp_id]);

            $stmt = $this->pdo->prepare(
                "DELETE FROM email_verification 
             WHERE email_id = ?"
            );

            $stmt->execute([$email]);


            $stmt = $this->pdo->prepare(
                "DELETE FROM Employee 
             WHERE emp_id = ?"
            );

            $stmt->execute([$emp_id]);

            $this->pdo->commit();

            return true;

        } catch (PDOException $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            error_log($e->getMessage());

            return false;
        }
    }

    public function change_employee_rank($emp_id, $event_type, $new_rank, $promoted_by, $reason)
    {
        try {

            $this->pdo_initializer();
            $this->pdo->beginTransaction();

            $result = $this->getEmployeeHistoryLatest($emp_id);

            if (!$result) {
                throw new Exception("Employee history not found");
            }

            /* prevent useless update */
            if ($result['emp_rank'] == $new_rank) {
                $this->pdo->rollBack();
                return false;
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO Employee_history
                (
                    emp_id,
                    event_type,
                    animal_id,
                    rank_assigned_by,
                    supervisor_id,
                    rescue_point_id,
                    emp_rank,
                    salary,
                    reason
                )
                VALUES
                (?, ?, NULL, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $emp_id,
                $event_type,
                $promoted_by,
                $result['supervisor_id'],
                $result['rescue_point_id'],
                $new_rank,
                $result['salary'],
                $reason
            ]);

            $stmt = $this->pdo->prepare("
                UPDATE Employee 
                SET emp_rank = ? 
                WHERE emp_id = ?
            ");

            $stmt->execute([$new_rank, $emp_id]);

            $this->pdo->commit();
            $this->add_notification($emp_id, $promoted_by, $reason);
            return true;

        } catch (Exception $e) {
            echo "this is the event_type " . $e->getMessage();

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            error_log("Rank change failed: " . $e->getMessage());

            return false;
        }
    }

    public function change_employee_salary($emp_id, $salary, $promoted_by, $reason = "Salary Changed")
    {
        try {
            $this->pdo_initializer();
            $this->pdo->beginTransaction();
            $result = $this->getEmployeeHistoryLatest($emp_id);

            $stmt = "INSERT INTO Employee_history(emp_id , event_type , animal_id ,
                rank_assigned_by , supervisor_id , rescue_point_id , emp_rank , salary,
                reason)
                values (?,4,NULL, ?,?,?,?,?,?);";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([
                $emp_id,
                $promoted_by,
                $result['supervisor_id'],
                $result['rescue_point_id'],
                $result['emp_rank'],
                $salary,
                $reason
            ]);

            $stmt = "Update Employee SET salary = ? WHERE emp_id = ? ;";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$salary, $emp_id]);

            $this->pdo->commit();
            $this->add_notification($emp_id, $promoted_by, $reason);

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        }
    }

    public function assign_employee_supervisor($emp_id, $super_visor_id, $promoted_by, $reason = "Change Employee Supervisor")
    {
        try {

            $this->pdo_initializer();
            $this->pdo->beginTransaction();
            $result = $this->getEmployeeHistoryLatest($emp_id);



            $stmt = "INSERT INTO Employee_history(emp_id , event_type , animal_id ,
                rank_assigned_by , supervisor_id , rescue_point_id , emp_rank , salary,
                reason)
                values (?,5,NULL, ?,?,?,?,?,?);";
            $stmt = $this->pdo->prepare($stmt);

            $stmt->execute([
                $emp_id,
                $promoted_by,
                $super_visor_id,
                $result['rescue_point_id'],
                $result['emp_rank'],
                $result['salary'],
                $reason
            ]);

            $stmt = "Update Employee SET immediate_supervisor_id = ? WHERE emp_id = ? ;";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$super_visor_id, $emp_id]);

            $this->pdo->commit();
            $this->add_notification($emp_id, $promoted_by, $reason);

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        }
    
    
        }

    public function remove_employee_supervisor($emp_id, $promoted_by, $reason = "Removed Employee Supervisor")
    {
        try {

            $this->pdo_initializer();
            $this->pdo->beginTransaction();
            $result = $this->getEmployeeHistoryLatest($emp_id);


            $stmt = "INSERT INTO Employee_history(emp_id , event_type , animal_id ,
                rank_assigned_by , supervisor_id , rescue_point_id , emp_rank , salary,
                reason)
                values (?,6,NULL, ?,NULL,?,?,?,?);";
            $stmt = $this->pdo->prepare($stmt);

            $stmt->execute([
                $emp_id,
                $promoted_by,
                $result['rescue_point_id'],
                $result['emp_rank'],
                $result['salary'],
                $reason
            ]);

            $stmt = "Update Employee SET immediate_supervisor_id = NULL WHERE emp_id = ? ;";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$emp_id]);

            $this->pdo->commit();
            $this->add_notification($emp_id, $promoted_by, $reason);

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        }
    }

    public function assign_at_a_point($emp_id, $type, $promoted_by, $rescue_point, $reason = "Assigned At a point")
    {
        try {
            $res = $this->getPointHistoryLatest($emp_id);

            $this->pdo_initializer();
            $this->pdo->beginTransaction();
            $result = $this->getEmployeeHistoryLatest($emp_id);

           
            $stmt = "Update animals set emp_id = NULL where emp_id = ?; ";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$emp_id]);

            $this->remove_from_an_point($emp_id , $promoted_by, $res);  


            $stmt = "INSERT INTO Employee_history(emp_id , event_type , animal_id ,
                rank_assigned_by , supervisor_id , rescue_point_id , emp_rank , salary,
                reason)
                values (?,?,NULL, ?,?,?,?,?,?);";

            $stmt = $this->pdo->prepare($stmt);

            $stmt->execute([
                $emp_id,
                $type,
                $promoted_by,
                $rescue_point,
                3,
                $result['salary'],
                $reason
            ]);

            $stmt = "Update Employee SET rescue_point_id = ? WHERE emp_id = ? ;";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$rescue_point, $emp_id]);

            $this->pdo->commit();
            $this->add_notification($emp_id, $promoted_by, $reason);

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        }
    }

    public function remove_from_an_point($emp_id ,$promoted_by ,$res){
        try {
            $stmt = "Update Employee set emp_rank = 4 where emp_id = ? ;";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$emp_id]);

            $stmt = "Select rescue_point_id from Employee where emp_id = ?";
            $stmt = $this->pdo->prepare($stmt); 
            $stmt->execute([$emp_id]);
            $result = $stmt->fetchColumn();

            if($result !== NULL){
                $stmt = "INSERT INTO Employee_history(emp_id ,event_type 
                , rank_assigned_by ,supervisor_id , rescue_point_id , emp_rank ,salary , reason ) 
                values( ? , 8 , ? ,?, NULL,?,?,? )";
                
                $stmt = $this->pdo->prepare($stmt);
                $stmt->execute([$emp_id , $promoted_by , $res['supervisor_id'] 
                    , 4, $res['salary'] , "REMOVED FROM a point"]);
                
            }
            $this->add_notification($emp_id , $promoted_by, "Remove from an point");



            
        }catch(PDOException $e){
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        }
    }

    public function assign_an_animal($emp_id, $promoted_by, $animal_id, $reason = "A new animal assigned")
    {
        try {
            $this->pdo_initializer();
            $this->pdo->beginTransaction();
            $result = $this->getEmployeeHistoryLatest($emp_id);

            $stmt = "INSERT INTO Employee_history(emp_id , event_type , animal_id ,
                rank_assigned_by , supervisor_id , rescue_point_id , emp_rank , salary,
                reason)
                values (?,9 ,?, ?,?,?,?,?,?);";

            $stmt = $this->pdo->prepare($stmt);

            $stmt->execute([
                $emp_id,
                $animal_id,
                $promoted_by,
                $result['supervisor_id'],
                $result['rescue_point_id'],
                $result['emp_rank'],
                $result['salary'],
                $reason
            ]);

            $stmt = "Update animals SET emp_id = ? WHERE animal_id = ? ;";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$emp_id, $animal_id]);

            $this->pdo->commit();
            $this->add_notification($emp_id, $promoted_by, $reason);

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        }
    }

    public function assign_as_a_manager($emp_id, $promoted_by, $rescue_point, $reason = "Assigned a Point")
    {
        try {

            $this->pdo_initializer();


            $employee = $this->getEmployeeHistoryLatest($emp_id);

            if (!$employee) {
                throw new Exception("Employee not found");
            }


            $stmt = $this->pdo->prepare("
                SELECT supervisor_id 
                FROM rescue_point 
                WHERE rescue_point_id = ?
            ");

            $stmt->execute([$rescue_point]);
            $oldSupervisorId = $stmt->fetchColumn();


            if (!empty($oldSupervisorId)) {

                $oldSupervisorHistory = $this->getEmployeeHistoryLatest($oldSupervisorId);

                if ($oldSupervisorHistory) {

                    $stmt = $this->pdo->prepare("
                        UPDATE Employee
                        SET rescue_point_id = NULL
                        WHERE emp_id = ?
                    ");
                    $stmt->execute([$oldSupervisorId]);

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
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");

                    $stmt->execute([
                        $oldSupervisorId,
                        6,
                        $promoted_by,
                        null,
                        $oldSupervisorHistory['emp_rank'],
                        $oldSupervisorHistory['salary'],
                        "Removed from manager position"
                    ]);

                    $this->add_notification(
                        $oldSupervisorId,
                        $promoted_by,
                        "Removed from Manager position"
                    );
                }
            }


            $stmt = $this->pdo->prepare("
                UPDATE Employee
                SET rescue_point_id = ?
                WHERE emp_id = ?
            ");

            $stmt->execute([$rescue_point, $emp_id]);


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
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $emp_id,
                11,
                $promoted_by,
                $rescue_point,
                $employee['emp_rank'],
                $employee['salary'],
                $reason
            ]);


            $stmt = $this->pdo->prepare("
                UPDATE rescue_point
                SET supervisor_id = ?
                WHERE rescue_point_id = ?
            ");

            $stmt->execute([$emp_id, $rescue_point]);


            $this->add_notification(
                $emp_id,
                $promoted_by,
                "Assigned As a Manager"
            );

            $this->pdo->commit();


            return true;

        } catch (Exception $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            // error_log("assign_at_a_point error: " . $e->getMessage());

            return false;
        }
    }

    public function getEmployeeHistoryLatest($emp_id)
    {
        try {
            $stmt = "SELECT emp_id , created_at , event_type , animal_id , 
            rank_assigned_by , supervisor_id , rescue_point_id , emp_rank  , salary 
            from  Employee_history where emp_id = ? ;";

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$emp_id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;

        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getEmployee_email($emp_id)
    {
        try {
            $stmt = "SELECT email from Employee WHERE emp_id = ? ; ";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$emp_id]);
            $result = $stmt->fetchColumn(PDO::FETCH_ASSOC);
            return $result;

        } catch (PDOException $e) {
            exit($e->getMessage());

        }
    }

    public function getEmployee_id($email)
    {
        $stmt = $this->pdo->prepare(
            "select emp_id from Employee where email= :email ;"
        );

        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function getEmployee_supervisor_id($email)
    {
        $stmt = $this->pdo->prepare(
            "SELECT immediate_supervisor_id from Employee where email = :email ;"
        );

        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }


    public function get_admin_profile($id)
    {
        return $this->get_employee_info($id);
    }

    public function update_bio_employee($id)
    {
        try {
            $image_path = $this->image_upload();

            if ($image_path === "") {
                $image_path = 'https://res.cloudinary.com/dvpwqtobj/image/upload/v1757076286/user_xhxvc9.png';
            }

            $stmt = "UPDATE Employee SET emp_profile_picture_link = ? , emp_bio = ? WHERE emp_id = ?";
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$image_path, $_POST['bio'], $id]);

            $_SESSION['image'] = $image_path;
            $_SESSION['bio'] = $_POST['bio'];

        } catch (PDOException $e) {
            exit(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
        }
    }
    public function admin_insert($name, $email, $password)
    {
        $this->createEmployeeForce(
            $name,
            $email,
            4,
            $password,
            0,
            'https://res.cloudinary.com/dvpwqtobj/image/upload/v1757076286/user_xhxvc9.png',
            null,
            null
        );
    }

    public function get_all_employee($rank, $name, $rank_by)
    {
        try {
            $rank_by = $rank_by ?? 'emp_rank';

            if (
                $rank_by !== 'salary' && $rank !== null && $rank_by !== 'emp_rank'
                && $rank_by !== 'joing_date' && $rank_by !== 'rank_assign_date'
            ) {

                return [];
            }
            /*
            // -- rank by emp_rank

            SELECT emp_id , emp_profile_picture_link ,
            emp_name , email, emp_rank ,
            rank() over(order by emp_rank asc) as company_based_rank from Employee;

            // -- rank by salary

            SELECT emp_id , emp_profile_picture_link ,
            emp_name , email, emp_rank ,
            rank() over(order by salary desc) as company_based_rank from Employee;

            // --rank by joining date

            SELECT emp_id , emp_profile_picture_link ,
            emp_name , email, emp_rank ,
            rank() over(order by joing_date desc) as company_based_rank from Employee;  

            // -- rank by promotion date /demotion date

            SELECT emp_id , emp_profile_picture_link ,
            emp_name , email, emp_rank ,
            rank() over(order by rank_assign_date desc) as company_based_rank from Employee; 


            // 
        */
            $stmt = "
                with employee_cte as (
                    select emp_id,
                    emp_profile_picture_link,
                    emp_name,
                    email,
                    emp_rank,
                    salary,
                    joing_date
                    from Employee 
                    where emp_name like concat(substr(:name , 1 ,1 ),'%') 
                )
                    SELECT emp_id,
                    emp_profile_picture_link,
                    emp_name,
                    email,
                    emp_rank,
                    salary ,
                    joing_date,
                    rank() over(order by " . $rank_by . " asc) as rank,
                    levenshtein(emp_name , :name) as distance
                FROM employee_cte
                where emp_rank >= :rank
                order by rank asc ;
            ";
            $this->pdo_initializer();



            $stmt = $this->pdo->prepare($stmt);

            $stmt->bindValue(":rank", $rank, PDO::PARAM_INT);
            $stmt->bindValue(":name", $name, PDO::PARAM_STR);

            $stmt->execute();

            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $employees;

        } catch (PDOException $e) {

            exit(json_encode(
                ["msg" => $e->getMessage()],
                JSON_PRETTY_PRINT
            ));
        }
    }

    public function get_all_employeeEX($rank, $name)
    {
        try {


            $stmt = "

                SELECT
                    emp_id,
                    emp_profile_picture_link,
                    emp_name,
                    email,
                    emp_rank,
                    salary,
                    joing_date,
                    levenshtein(emp_name, :name) AS distance                    
                FROM Employee
                where emp_rank >= :rank && emp_name like concat(:name , '%')
                ORDER BY distance ASC;
            ";
            $this->pdo_initializer();



            $stmt = $this->pdo->prepare($stmt);

            $stmt->bindValue(":rank", $rank, PDO::PARAM_INT);
            $stmt->bindValue(":name", $name, PDO::PARAM_STR);

            $stmt->execute();

            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $employees;

        } catch (PDOException $e) {

            exit(json_encode(
                ["msg" => $e->getMessage()],
                JSON_PRETTY_PRINT
            ));
        }
    }

    public function find_employee_level()
    {
        $stmt = 'select emp_rank from Employee where emp_id = ?;';
        $id = $_SESSION['id'];

        $this->pdo_initializer();
        $stmt = $this->pdo->prepare($stmt);
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();

        // if(!$count){
        //     http_response_code(400);
        //     $msg ;
        //     $msg['msg'] = 'Something went wrong';

        //     exit(json_encode( $msg , JSON_PRETTY_PRINT ));
        // }
        return $count;
    }

    public function get_employee_info($id)
    {
        /*
            SELECT E1.emp_id as emp_id , E1.emp_bio as emp_bio , E1.emp_name as emp_name, 
            E1.emp_rank as emp_rank , E1.salary  as salary,
            E1.joing_date as joing_date ,E1.emp_profile_picture_link as emp_profile_picture_link,
             E1.immediate_supervisor_id as supervisor_id
            ,count(DISTINCT history.animal_id) as total_animal 
            from Employee as E1 
            left join Employee as E2 on E1.immediate_supervisor_id = E2.emp_id
            left join Employee_history as history on history.emp_id = E1.emp_id
            where E1.emp_id = ? 
            GROUP BY
            E1.emp_id , E1.emp_bio, E1.emp_name , E1.emp_rank, E1.salary ,E1.joing_date
            , E1.emp_profile_picture_link , E1.immediate_supervisor_id
            ;

        */
        //first finding out the employee personal data
        $stmt = "
            SELECT E1.emp_id as emp_id , E1.emp_bio as emp_bio , E1.emp_name as emp_name, 
            E1.email,
            E1.emp_rank as emp_rank , E1.salary  as salary,
            E1.joing_date as joing_date ,E1.emp_profile_picture_link as emp_profile_picture_link,
             E1.immediate_supervisor_id as supervisor_id
            ,count(DISTINCT history.animal_id) as total_animal_cared 
            from Employee as E1 
            left join Employee as E2 on E1.immediate_supervisor_id = E2.emp_id
            left join Employee_history as history on history.emp_id = E1.emp_id
            where E1.emp_id = ? 
            GROUP BY
            E1.emp_id , E1.emp_bio, E1.emp_name , E1.emp_rank, E1.salary ,E1.joing_date
            , E1.emp_profile_picture_link , E1.immediate_supervisor_id;
        ";

        $this->pdo_initializer();

        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([
            $id
        ]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        $result = [];

        $result['emp_id'] = $res['emp_id'];
        $result['emp_bio'] = $res['emp_bio'];
        $result['emp_name'] = $res['emp_name'];
        $result['emp_rank'] = $res['emp_rank'];
        $result['email'] = $res['email'];
        $result['salary'] = $res['salary'];
        $result['joing_date'] = $res['joing_date'];
        $result['emp_profile_picture_link'] = $res['emp_profile_picture_link'];
        $result['supervisor_id'] = $res['supervisor_id'];
        $result['supervisor_name'] = $res['supervisor_name'];
        $result['supervisor_email'] = $res['supervisor_email'];
        $result['supervisor_profile_image'] = $res['supervisor_profile_image'];


        //getting back the animal_info
        $stmt = "select animal_id , rescue_point_id , species_type ,gender_type 
                , age , health_status , activity_level from animals where emp_id = ?";

        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([
            $id
        ]);

        $result['animals'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result['current_managing_animals'] = count($result['animals']);

        // window function 
        $stmt = $this->pdo->prepare("
            SELECT 
                emp_id,
                created_at,
                event_type,

                CASE 
                    WHEN event_type = 9 THEN animal_id
                    ELSE 'No animal assigned'
                END AS animal_id,

                rank_assigned_by,

                supervisor_id AS CURR_SUPERVISOR,
                LAG(supervisor_id) OVER (
                    PARTITION BY emp_id 
                    ORDER BY created_at
                ) AS PREV_SUPERVISOR,

                rescue_point_id,

                emp_rank AS CURR_RANK,
                LAG(emp_rank) OVER (
                    PARTITION BY emp_id 
                    ORDER BY created_at
                ) AS PREV_RANK,

                salary AS CURR_SALARY,
                LAG(salary) OVER (
                    PARTITION BY emp_id 
                    ORDER BY created_at
                ) AS PREV_SALARY,

                reason

            FROM Employee_history
            where emp_id = ?
            ;
        ");

        $stmt->execute([$id]);
        $result['history'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result['history_count'] = count($result['history']);




        return $result;


    }

    public function getEmployee_manager($name , $rank)
    {


        $stmt = $this->pdo->prepare('
            SELECT  E1.emp_id,
                    E1.emp_profile_picture_link,
                    E1.emp_name,
                    E1.email,
                    E1.emp_rank,
                    E1.salary,
                    E1.joing_date,
                    levenshtein(E1.emp_name, :name) AS distance,
                    R1.rescue_point_name,
                    R1.rescue_point_id,
                    R1.creation_date
            from Employee as E1 inner join rescue_point as R1 
            on R1.supervisor_id = E1.emp_id where E1.emp_rank = :rank ;
        ');

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':rank', (int)$rank, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //employee notification handling

    public function add_notification($emp_id, $written_by, $message)
    {
        try {
            /*
                INSERT INTO EmployeeNotification(emp_id , message , created_by)
                VALUES(
                    ?,?,?
                );
            */
        

            $stmt = $this->pdo->prepare(
                "INSERT INTO EmployeeNotification
            (
                emp_id,
                message,
                created_by
            )
            VALUES
            (
                ?, ?, ?
            )"
            );

            $stmt->execute([
                $emp_id,
                $message,
                $written_by
            ]);

            return true;

        } catch (PDOException $e) {

            error_log($e->getMessage());

            return false;
        }
    }



}
