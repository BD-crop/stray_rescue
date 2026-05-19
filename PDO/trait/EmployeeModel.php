<?php

trait EmployeeModel
{
    public function createEmployeeForce($name , $email ,
     $emp_rank , $password , $salary , $emp_profile_picture_link , $immediate_supervisor_id){
        try{
                    $this->initializer();

            $stmt = $this->pdo->prepare("INSERT INTO Employee(emp_name , email , emp_rank , password ,
                                        salary , emp_profile_picture_link , immediate_supervisor_id)
                                        values (?,?,?,?,?,?,?);");

            $stmt -> execute([$name , $email , $emp_rank , $password ,$salary , 
            $emp_profile_picture_link , $this->UUID_TO_BIN( $immediate_supervisor_id) ]);

            
            $stmt = $this->pdo->prepare("insert into email_verification( email_verification_id ,email_id,table_name)
                        values(UNHEX(REPLACE(? , '-','')),?,?);");
            
            
            $uuid_id = $this->UUID_GENERATOR();

            $stmt->execute([$uuid_id, $email, "Employee"]);

            return true;
        }catch(PDOException $e){
            return false;
        }


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

        $stmt->execute([$this->UUID_TO_BIN($id)]);

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
            $stmt->execute([$image_path, $_POST['bio'], $this->UUID_TO_BIN($id)]);

            $_SESSION['image'] = $image_path;
            $_SESSION['bio']   = $_POST['bio'];

        } catch (PDOException $e) {
            exit(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
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
                    SELECT
                    LOWER(CONCAT(
                        SUBSTR(HEX(emp_id), 1, 8), '-',
                        SUBSTR(HEX(emp_id), 9, 4), '-',
                        SUBSTR(HEX(emp_id), 13, 4), '-',
                        SUBSTR(HEX(emp_id), 17, 4), '-',
                        SUBSTR(HEX(emp_id), 21)
                    )) AS emp_id,
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
                    LOWER(CONCAT(
                        SUBSTR(HEX(emp_id), 1, 8), '-',
                        SUBSTR(HEX(emp_id), 9, 4), '-',
                        SUBSTR(HEX(emp_id), 13, 4), '-',
                        SUBSTR(HEX(emp_id), 17, 4), '-',
                        SUBSTR(HEX(emp_id), 21)
                    )) AS emp_id,
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
        $stmt->execute([$this->UUID_TO_BIN($id)]);
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
            $this->UUID_TO_BIN($id),
        ]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        $result = [];

        $result['emp_id']                   = $this->BIN_TO_UUID($res['emp_id']);
        $result['emp_bio']                  = $res['emp_bio'];
        $result['emp_name']                 = $res['emp_name'];
        $result['emp_rank']                 = $res['emp_rank'];
        $result['email']                    = $res['email'];
        $result['salary']                   = $res['salary'];
        $result['joing_date']               = $res['joing_date'];
        $result['emp_profile_picture_link'] = $res['emp_profile_picture_link'];
        $result['rank_assign_date']         = $res['rank_assign_date'];
        $result['supervisor_id']            = $this->BIN_TO_UUID($res['supervisor_id']);
        $result['supervisor_name']          = $res['supervisor_name'];
        $result['supervisor_email']         = $res['supervisor_email'];
        $result['supervisor_profile_image'] = $res['supervisor_profile_image'];

        return $result;
    }

}
