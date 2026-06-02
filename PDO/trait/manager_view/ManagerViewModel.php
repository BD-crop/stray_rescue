<?php

trait ManagerViewModel
{
    public function getManagerID($managerID){
        $stmt = $this->pdo->
                prepare("SELECT rescue_point_id 
                        from 
                        rescue_point where supervisor_id = ? ");
        $stmt->execute([$managerID]);

        return $stmt ->fetchColumn(PDO::FETCH_ASSOC);
    }

    public function addAnimal(){
        try{
        
        $animal_id = $this->UUID_GENERATOR();

        $this->pdo->beginTransaction();
        $stmt = $this -> pdo ->
            prepare("INSERT INTO shelter_animals(
                animal_id ,rescue_point_id , animal_name,
                animal_age , health_status )
            values(?,?,?,?,?,?);");
        $stmt->execute([$animal_id , $_POST['rescue_point_id'] 
            , $_POST['animal_name'] , $_POST['animal_age'] , $_POST['health_status']]);
        
            $obj = $this->upload_multiple_images();

            $stmt = $this->pdo->prepare(
                "INSERT INTO shelter_animals_images
                (animal_id, image_path)
                VALUES (?,?)"
            );
 
            foreach ($obj as $value) {
                $stmt->execute([$animal_id, $value]);
            }
         $this->pdo->commit();

        return $animal_id;

        }
        catch(PDOException $e){
            if (
                isset($this->pdo) &&
                $this->pdo->inTransaction()
            ) {
                $this->pdo->rollBack();
            }

            exit(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
        }
    }

    public function removeAnimal($animal_id){
        try{    
            $stmt = $this->pdo->
                prepare("UPDATE shelter_animals 
                        set is_removed = 1 WHERE animal_id = ?  ;");
            $stmt->execute([$animal_id]);

        }catch(PDOException $e){
            exit(json_encode($e->getMessage() , JSON_PRETTY_PRINT));
        }
    }

    public function readAnimal($animal_id){
        try{
            
            $stmt = $this->pdo->
                prepare("WITH animal_image_cte as (
                        SELECT 
                            animal_id,
                            GROUP_CONCAT(image_path SEPARATOR ';;;') as images
                            from 
                            shelter_animals_images
                            where animal_id = ? 
                            GROUP BY
                            animal_id) ,
                        shelter_animal_Property_cte as (
                            SELECT 
                                animal_id ,
                                GROUP_CONCAT(
                                    CONCAT(property_type , '||' ,animal_property)
                                    SEPARATOR ';;;'
                                ) as animal_property
                            FROM shelter_animal_Property
                            where animal_id = ?
                            GROUP BY 
                            animal_id
                        )

                        SELECT 
                        ani.animal_id as animal_id,
                        rescue_point_id,
                        animal_name,
                        animal_age,
                        is_removed,
                        health_status,
                        added_at,
                        images,
                        animal_property

                        FROM shelter_animals as ani
                        INNER JOIN animal_image_cte as shelter
                        ON shelter.animal_id = ani.animal_id
                        INNER JOIN shelter_animal_Property_cte as  property
                        ON property.animal_id = ani.animal_id
                        WHERE animal_id = ? 
                    ");

            $stmt->execute([$animal_id , $animal_id , $animal_id]);
        }catch(PDOException $e){
            exit(json_encode($e->getMessage() , JSON_PRETTY_PRINT));
        }
    }
    
    public function addAnimalProperty($animal_id , $property_type , $property_value){
        

    }

    public function createAdoptionListing($shelter_id){
        try{
        
        $animal_id = $this->UUID_GENERATOR();

        
        $stmt = $this -> pdo ->
            prepare("INSERT INTO Adoption_animals(
                animal_id ,shelter_id)
            
            values(?,?);");

            $stmt->execute([$animal_id , $shelter_id]);
        


            return $animal_id;

        }
        catch(PDOException $e){
            if (
                isset($this->pdo) &&
                $this->pdo->inTransaction()
            ) {
                $this->pdo->rollBack();
            }

            exit(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
        }
    }


    public function removeAdoptionListing($animal_id)
    {
        try{    
            $stmt = $this->pdo->
                prepare("DELETE FROM Adoption_animals where 
                animal_id = ?  ;");
            $stmt->execute([$animal_id]);

        }catch(PDOException $e){
            exit(json_encode($e->getMessage() , JSON_PRETTY_PRINT));
        }
    }

    public function readAdoptionListing($animal_id){

    }
}



?>