<?php


trait EmployeeModel
{
    public function createEmployeeForce(
        $name, $email, $emp_rank, $password,
        $salary, $emp_profile_picture_link,
        $immediate_supervisor_id, $created_by
    ){
        try {
            /*
                START TRANSACTION
                    INSERT INTO Employee(emp_name , email , emp_rank , 
                    password,salary ,emp_profile_picture_link ,immediate_supervisor_id)
                    values (?,?,?,?,?,?,?);
                    INSERT INTO 
                    Employee_history(event_type, rank_assigned_by, supervisor_id,
                    rescue_point_id, emp_rank, salary, reason) values (?,?,?,?,?,?,?);
                    INSERT INTO
                    email_verification(
                    email_verification_id, email_id, table_name, is_verified)
                    values (?,?,?,'Y');
                COMMIT;
            
            */ 



            $this->pdo_initializer();
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "INSERT INTO Employee
                (
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
                    :emp_name,
                    :email,
                    :emp_rank,
                    :password,
                    :salary,
                    :emp_profile_picture_link,
                    :immediate_supervisor_id
                )"
            );
            // production -->   $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT)]);

            $stmt->execute([
                ':emp_name' => $name,
                ':email' => $email,
                ':emp_rank' => $emp_rank,
                ':password' => $password,
                ':salary' => $salary,
                ':emp_profile_picture_link' => $emp_profile_picture_link,
                ':immediate_supervisor_id' => $immediate_supervisor_id
            ]);

            $emp_id = $this->getEmployee_id($email);

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

            $stmt->execute([
                ':emp_id' => $emp_id,
                ':rank_assigned_by' => $created_by,
                ':supervisor_id' => $immediate_supervisor_id,
                ':emp_rank' => $emp_rank,
                ':salary' => $salary,
                ':reason' => "Employee Created"
            ]);

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

            $stmt->execute([
                ':email_verification_id' => $this->UUID_GENERATOR(),
                ':email_id' => $email,
                ':table_name' => "Employee"
            ]);

            $this->pdo->commit();

            return true;

        } catch(PDOException $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return false;
        }
    }
    
    public function getEmployee_id($email){
        $stmt = $this->pdo->prepare(
            "select emp_id from Employee where email= :email ;");

            $stmt->bindValue(":email", $email , PDO::PARAM_STR);
            $stmt->execute();
          
        return $stmt->fetchColumn();
    }

    public function getEmployee_supervisor_id($email){
        $stmt = $this->pdo->prepare(
            "SELECT immediate_supervisor_id from Employee where email = :email ;" );
        
            $stmt->bindValue(":email", $email , PDO::PARAM_STR);
            $stmt->execute();
        
        return $stmt->fetchColumn();
    }

    public function updateEmployeeForce($id , $rank){

    }

    public function deleteEmployeeForce($id){

    }

    public function assignSupervisor($id , $emp_id){

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
            $stmt->execute([$image_path, $_POST['bio'],$id]);

            $_SESSION['image'] = $image_path;
            $_SESSION['bio']   = $_POST['bio'];

        } catch (PDOException $e) {
            exit(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
        }
    }
    public function admin_insert($name, $email, $password)
    {
        $this->createEmployeeForce($name , $email, 4 ,$password, 0, 
        'https://res.cloudinary.com/dvpwqtobj/image/upload/v1757076286/user_xhxvc9.png' ,null , null);
    }

    public function get_all_employee($rank, $name , $rank_by)
    {
        try {
            $rank_by = $rank_by ?? 'emp_rank';
            
            if($rank_by !== 'salary' && $rank !== null && $rank_by !== 'emp_rank' 
                && $rank_by !== 'joing_date' && $rank_by  !== 'rank_assign_date' ){
                
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
                    joing_date,
                    rank_assign_date
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
                    rank_assign_date,
                    rank() over(order by ".$rank_by." asc) as rank,
                    levenshtein(emp_name , :name) as distance
                FROM employee_cte
                where emp_rank >= :rank
                order by rank asc ;
            ";
            $this->pdo_initializer();



            $stmt = $this->pdo->prepare($stmt);

            $stmt -> bindValue(":rank", $rank, PDO::PARAM_INT);
            $stmt -> bindValue(":name", $name, PDO::PARAM_STR);

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
    
    public function get_all_employeeEX($rank, $name )
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
                    rank_assign_date,
                    levenshtein(emp_name, :name) AS distance                    
                FROM Employee
                where emp_rank >= :rank && emp_name like concat(:name , '%')
                ORDER BY distance ASC;
            ";
            $this->pdo_initializer();



            $stmt = $this->pdo->prepare($stmt);

            $stmt -> bindValue(":rank", $rank, PDO::PARAM_INT);
            $stmt -> bindValue(":name", $name, PDO::PARAM_STR);

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

    public function find_employee_level( )
    {
        $stmt = 'select emp_rank from Employee where emp_id = ?;';
        $id   =  $_SESSION['id'];

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

        $this->pdo_initializer();

        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([
            $id
        ]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        $result = [];

        $result['emp_id']                   = $res['emp_id'];
        $result['emp_bio']                  = $res['emp_bio'];
        $result['emp_name']                 = $res['emp_name'];
        $result['emp_rank']                 = $res['emp_rank'];
        $result['email']                    = $res['email'];
        $result['salary']                   = $res['salary'];
        $result['joing_date']               = $res['joing_date'];
        $result['emp_profile_picture_link'] = $res['emp_profile_picture_link'];
        $result['rank_assign_date']         = $res['rank_assign_date'];
        $result['supervisor_id']            = $res['supervisor_id'];
        $result['supervisor_name']          = $res['supervisor_name'];
        $result['supervisor_email']         = $res['supervisor_email'];
        $result['supervisor_profile_image'] = $res['supervisor_profile_image'];

        return $result;
    }

}
