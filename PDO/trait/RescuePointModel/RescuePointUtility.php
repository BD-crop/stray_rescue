<?php

    trait RescuePointUtility{
    
        public function count_total_RescuePoint(){
            $stmt = $this->pdo->prepare(
                "SELECT 
                    case 
                        when is_closed = 0 then 'Active'
                        else 'Inactive' 
                    END as status
                    , COUNT(*) as total from rescue_point 
                group by status ;"
            );
            $stmt->execute();
            
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $output=[];
            foreach ($data as $row) {
                $output[$row['status']] = (int)$row['total'];
            }

            $output['Active'] = $output['Active'] ?? 0;
            $output['Inactive'] = $output['Inactive'] ?? 0;


            return $output;
        }
    }
?>