<?php
trait UserUtility{

    public function count_total_user(){
        $stmt = $this->pdo->prepare(
            "SELECT 
                case 
                    when is_deleted = 0 then 'Active'
                    else 'Inactive' 
                END as status
                 , COUNT(*) as total from Users 
            group by status ;"
        );
        $stmt->execute();
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $output=[];
        foreach ($data as $row) {
            $output[$row['status']] = (int)$row['total'];
        }

        return $output;
    }

}


?>