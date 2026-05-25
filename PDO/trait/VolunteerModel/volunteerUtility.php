<?php

trait volunteerUtility{
    public function count_total_volunteers(){
        $stmt = $this->pdo->prepare(
            "SELECT 
                case 
                    when has_resigned = 0 then 'Active'
                    else 'Inactive' 
                END as status
                 , COUNT(*) as total from volunteers 
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