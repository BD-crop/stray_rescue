<?php

trait ManagerViewModel
{
    public function getManagerRescuePointID($managerID){
        $stmt = $this->pdo->
                prepare("SELECT rescue_point_id 
                        from 
                        rescue_point where supervisor_id = :id ;");
        $stmt->bindValue(':id',$managerID ,PDO::PARAM_STR);
        $stmt->execute();

        return $stmt ->fetchColumn();
    }

    public function addAnimal(){
        try{
        
        $animal_id = $this->UUID_GENERATOR();

        $this->pdo->beginTransaction();
        $stmt = $this -> pdo ->
            prepare("INSERT INTO shelter_animals(
                animal_id ,rescue_point_id , animal_name,
                animal_age , health_status )
            values(?,?,?,?,?);");
        $stmt->execute([$animal_id , $_POST['rescue_point_id'] 
            , $_POST['animal_name'] , $_POST['animal_age'] , $_POST['health_status']]);
        
        $this->addAnimalImage($animal_id);
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
                        animal_property,
                        CASE
                            WHEN adopt.shelter_id IS NULL THEN 'not listed'
                            ELSE 'listed' 
                        END as is_listed

                        FROM shelter_animals as ani
                        LEFT JOIN animal_image_cte as shelter
                        ON shelter.animal_id = ani.animal_id
                        LEFT JOIN shelter_animal_Property_cte as  property
                        ON property.animal_id = ani.animal_id
                        LEFT JOIN Adoption_animals as adopt
                        on adopt.shelter_id = ani.animal_id 
                        WHERE ani.animal_id = ? 
                    ");

            $stmt->execute([$animal_id , $animal_id , $animal_id]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            exit(json_encode($e->getMessage() , JSON_PRETTY_PRINT));
        }
    }

    public function UpdateAnimalAge($animal_id , $age){
        try{
        $stmt = $this->pdo->prepare("UPDATE 
            shelter_animals set animal_age = :age 
            where animal_id = :id");

        $stmt->bindValue(':age' , $age , PDO::PARAM_INT);
        $stmt->bindValue(':id' , $animal_id , PDO::PARAM_STR);

        $stmt->execute();
        }catch(PDOException $e){
            exit(json_encode($e->getMessage() , JSON_PRETTY_PRINT));
        }


    }

    public function addAnimalProperty($animal_id , $property_type , $property_value){
        try{
            $stmt = $this->pdo->prepare("
                INSERT INTO shelter_animal_Property
                (animal_id , property_type , animal_property)
                values(?, ?, ?);
            ");
            $stmt ->execute([$animal_id , $property_type , $property_value]);
        }catch(PDOException $e){
            exit(json_encode($e->getMessage() , JSON_PRETTY_PRINT));
        } 


    }

    public function enlist($shelter_id){
        $id = $this->UUID_GENERATOR();

        $stmt = $this->pdo->prepare("INSERT INTO 
            Adoption_animals(animal_id , shelter_id)
            values (?,?)");
    
        $stmt->execute([$id , $shelter_id]);

    }

    public function unlist($shelter_id){
        $id = $this->UUID_GENERATOR();

        $stmt = $this->pdo->prepare("DELETE FROM  
            Adoption_animals where shelter_id = ? ");
    
        $stmt->execute([$shelter_id]);
        
    }

    public function addAnimalImage($animal_id){
        $obj = $this->upload_multiple_images();

        $stmt = $this->pdo->prepare(
            "INSERT INTO shelter_animals_images
            (animal_id, image_path)
            VALUES (?,?)"
        );
 
        foreach ($obj as $value) {
                $stmt->execute([$animal_id, $value]);
        }
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
        $stmt= "SELECT shelter_id from Adoption_animals
                where animal_id = ? ";
        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([$animal_id]);
        $shelter_id=$stmt ->fetchColumn(PDO::FETCH_ASSOC);

        $this->readAnimal($shelter_id);
    }

    public function getAllShelteredAnimals($supervisor_id,$name ="" ,$rank_by = 'added_at' ,$order='asc'){
        try {

            $rank_by = $rank_by ?? 'added_at';

            if (
                $rank_by !== 'animal_age'  && $rank_by !== 'is_removed'
                && $rank_by !== 'health_status' && $rank_by !== 'added_at'
            ) {

                return [];
            }

            if($order != 'asc' || $order != 'desc'){
                $order = 'asc'; 
            }


            $stmt = "WITH animal_name_cte as (
                    SELECT    
                    
                    animal_id,
                    animal_name,
                    animal_age,
                    is_removed,
                    health_status,
                    added_at

                    from shelter_animals
                    inner join rescue_point on 
                    shelter_animals.rescue_point_id = rescue_point.rescue_point_id
                    inner join Employee on
                    Employee.emp_id = rescue_point.supervisor_id 
                    where animal_name like concat(substr(? , 1 ,1 ) ,'%')
                    AND Employee.emp_id = ?
                )
                SELECT  ani.animal_id       as animal_id,
                        ani.animal_name     as animal_name,
                        ani.animal_age      as animal_age, 
                        ani.is_removed      as is_removed,
                        ani.health_status   as health_status,
                        ani.added_at        as added_at,
                    COUNT(DISTINCT shel_image.image_path) as image_count,
                    COUNT(DISTINCT shel_prop.property_type) as prop_count,
                    CASE
                        WHEN adopt.shelter_id IS NULL THEN 'not listed'
                        ELSE 'listed' 
                    END as is_listed
                FROM animal_name_cte as ani
                LEFT JOIN shelter_animals_images as shel_image
                ON 
                shel_image.animal_id = ani.animal_id
                LEFT JOIN shelter_animal_Property as shel_prop
                ON
                shel_prop.animal_id = ani.animal_id
                LEFT JOIN Adoption_animals  as adopt
                ON 
                adopt.shelter_id = ani.animal_id
                GROUP BY
                    animal_id,
                    animal_name,
                    animal_age,
                    is_removed,
                    health_status,
                    added_at,
                    is_listed
                ORDER BY
                ".$rank_by." ".$order." 

            ";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([ $name,$supervisor_id]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);


        } catch(PDOException $e){
            exit(json_encode(
                ["msg" => $e->getMessage()],
                JSON_PRETTY_PRINT
            ));
        }

    }

    public function getAllShelteredAnimalsByEmpID($emp_id , $name ="" ,$rank_by = 'added_at' ,$order='asc' ){
        try {

            $rank_by = $rank_by ?? 'added_at';

            if (
                $rank_by !== 'animal_age'  && $rank_by !== 'is_removed'
                && $rank_by !== 'health_status' && $rank_by !== 'added_at'
            ) {

                return [];
            }

            if($order != 'asc' || $order != 'desc'){
                $order = 'asc'; 
            }


            $stmt = "WITH animal_name_cte as (
                    SELECT    
                    
                    animal_id,
                    animal_name,
                    animal_age,
                    is_removed,
                    health_status,
                    added_at,
                    manager
                    from shelter_animals
                    where animal_name like concat(substr(? , 1 ,1 ) ,'%')
                )
                SELECT  ani.animal_id       as animal_id,
                        ani.animal_name     as animal_name,
                        ani.animal_age      as animal_age, 
                        ani.is_removed      as is_removed,
                        ani.health_status   as health_status,
                        ani.added_at        as added_at,
                        ani.manager         as manager,
                    COUNT(DISTINCT shel_image.image_path) as image_count,
                    COUNT(DISTINCT shel_prop.property_type) as prop_count,
                    CASE
                        WHEN adopt.shelter_id IS NULL THEN 'not listed'
                        ELSE 'listed' 
                    END as is_listed
                FROM animal_name_cte as ani
                LEFT JOIN shelter_animals_images as shel_image
                ON 
                shel_image.animal_id = ani.animal_id
                LEFT JOIN shelter_animal_Property as shel_prop
                ON
                shel_prop.animal_id = ani.animal_id
                LEFT JOIN Adoption_animals  as adopt
                ON 
                adopt.shelter_id = ani.animal_id
                WHERE ani.manager = ?
                GROUP BY
                    animal_id,
                    animal_name,
                    animal_age,
                    is_removed,
                    health_status,
                    added_at,
                    is_listed,
                    manager
                ORDER BY
                ".$rank_by." ".$order." 

            ";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$name ,$emp_id]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);


        } catch(PDOException $e){
            exit(json_encode(
                ["msg" => $e->getMessage()],
                JSON_PRETTY_PRINT
            ));
        }

    }
}



?>