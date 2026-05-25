<?php

    trait RescuePostUtility{
        public function count_total_RescuePOST(){
            $stmt = $this->pdo->prepare(
                "SELECT 
                    case 
                        when sos_level = 3 then 'Emergency'
                        when sos_level = 1 then 'Attention Needed' 
                        else 'Healty Animal' 
                    END as status
                    , COUNT(*) as total from rescue_post 
                group by status ;"
            );
            $stmt->execute();
            
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $output=[];
            $output['Emergency'] =0;
            $output['Attention Needed']=0;
            $output['Healty Animal']=0;
            foreach ($data as $row) {
                $output[$row['status']] = (int)$row['total'];
            }

            return $output;
        }
    }


?>