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

    public function readAdoptionDetail($adoption_id ,$user_id){
            $stmt = $this->pdo->
                prepare(
                "WITH animal_image_cte as (
                    SELECT 
                    adopt.animal_id as adoption_id,
                    GROUP_CONCAT(image_path SEPARATOR ';;;') as images
                    FROM 
                    Adoption_animals as adopt
                    INNER JOIN shelter_animals_images as shel_image
                    ON shel_image.animal_id = adopt.shelter_id
                    where adopt.animal_id = ? 
                    GROUP BY adopt.animal_id
                    ) ,

                    shelter_animal_Property_cte as (
                            SELECT 
                                adopt.animal_id as adoption_id,
                                GROUP_CONCAT(
                                    CONCAT(shell_prop.property_type , '||' ,shell_prop.animal_property)
                                    SEPARATOR ';;;'
                                ) as animal_property
                            FROM shelter_animal_Property as shell_prop
                            INNER JOIN Adoption_animals  as adopt
                            ON adopt.shelter_id = shell_prop.animal_id
                            WHERE adopt.animal_id = ?
                            GROUP BY 
                            adopt.animal_id
                        )

                        SELECT 
                        ani.animal_id as shelter_id,
                        adopt.animal_id as animal_id,
                        rescue_point_id,
                        animal_name,
                        animal_age,
                        is_removed,
                        health_status,
                        added_at,
                        images,
                        animal_property,
                        CASE
                            WHEN application.adoption_application_status IS NULL THEN 'NO Application'
                            ELSE application.adoption_application_status 
                        END as adoption_status

                        FROM shelter_animals as ani
                        INNER JOIN Adoption_animals as adopt
                        on adopt.shelter_id = ani.animal_id 
                        LEFT JOIN animal_image_cte as shelter
                        ON shelter.adoption_id = adopt.animal_id
                        LEFT JOIN shelter_animal_Property_cte as  property
                        ON property.adoption_id = adopt.animal_id
                        LEFT JOIN Adoption_Application as application
                        ON application.animal_id = adopt.animal_id 
                        and application.user_id = ?
                        WHERE adopt.animal_id = ? 
                        ORDER BY 
                        application.created_at desc 
                        LIMIT 1;
                    ");

            $stmt->execute([$adoption_id , $adoption_id ,$user_id ,$adoption_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

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

    public function Add_Adoption_Application($animal_id , $user_id , $text){
        $stmt = $this->pdo->prepare(
            "INSERT INTO Adoption_Application
            (animal_id , user_id , adoption_Application_text
            ,adoption_application_status)
            VALUES(?,?,?,'pending')") ;
            $stmt->execute([$animal_id ,$user_id , $text ]);
        
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

    public function seeAdoptionRequests($manager_id){
        $stmt = "SELECT * from Adoption_Application 
                INNER JOIN Adoption_animals
                ON Adoption_animals.animal_id = Adoption_Application.animal_id
                INNER JOIN shelter_animals 
                ON shelter_animals.animal_id = Adoption_animals.shelter_id
                INNER JOIN rescue_point
                ON 
                rescue_point.rescue_point_id = shelter_animals.rescue_point_id 
                WHERE rescue_point.supervisor_id = ? 
                AND 
                Adoption_Application.adoption_application_status = 'pending'
                ;";
        
        $stmt= $this->pdo->prepare($stmt);
        $stmt->execute([$manager_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateAdoptionRequest($application_id , $status){
        try{
            $stmt= "UPDATE Adoption_Application
                    SET adoption_application_status = ? 
                    where adoption_application_id = ? ";
            $stmt=$this->pdo->prepare($stmt);

            $stmt->execute([$status , $application_id]);

        }catch(PDOException $e){
            echo $e->getMessage();
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

    public function adoptionListing($page, $limit = 10,$name = "" ,$rank_by='created_at' )
    {
        $allowedOrder = ['created_at', 'health_status', 'animal_age'];

        if (!in_array($rank_by, $allowedOrder)) {
            $rank_by = 'created_at';
        }
        $offset = $page * $limit;

        $this->pdo_initializer();

        $stmt = $this->pdo->prepare(
                "WITH animal_cte AS (
                    SELECT 
                    shelter_animals.animal_id               as shelter_id,
                    shelter_animals.animal_name             as animal_name,
                    shelter_animals.animal_age              as animal_age,
                    shelter_animals.health_status           as health_status,
                    Adoption_animals.animal_id              as animal_id,
                    Adoption_animals.created_at             as created_at
                    FROM shelter_animals 
                    inner join Adoption_animals
                    on Adoption_animals.shelter_id = shelter_animals.animal_id
                    WHERE animal_name LIKE CONCAT(SUBSTRING(:name, 1, 1), '%')
                ),
                image_table as 
                (
                    SELECT animal_id as shelter_id,image_path
                    from shelter_animals_images 
                    group by
                    animal_id
                )       
                 SELECT 
                    animal_cte.shelter_id    as shelter_id   ,
                    animal_cte.animal_name   as animal_name  ,
                    animal_cte.animal_age    as animal_age   ,
                    animal_cte.health_status as health_status,
                    animal_cte.animal_id     as animal_id    ,
                    animal_cte.created_at    as created_at   ,
                    image_table.image_path   as image_path   ,
                    GROUP_CONCAT(DISTINCT CONCAT(
                        shelter_animal_Property.property_type,'--',
                        shelter_animal_Property.animal_property) SEPARATOR ';;;')
                    as animal_properties
                    FROM animal_cte  
                    INNER JOIN image_table 
                    on animal_cte.shelter_id = image_table.shelter_id 
                    LEFT JOIN shelter_animal_Property
                    ON shelter_animal_Property.animal_id = animal_cte.shelter_id 
                    LEFT JOIN Adoption_Application
                    ON Adoption_Application.animal_id = animal_cte.animal_id
                    WHERE Adoption_Application.adoption_application_status = 'pending' 
                    OR Adoption_Application.adoption_application_status IS NULL
                    GROUP BY 
                        shelter_id   ,
                        animal_name  ,
                        animal_age   ,
                        health_status,
                        animal_id    ,
                        created_at   ,
                        image_path  
                    ORDER BY "."$rank_by"." desc 
                    LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':name' ,$name ,PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int) $limit+1, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) ($offset), PDO::PARAM_INT);

        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $count = $this->total_rescue_posts($limit , $offset);

        return ['count' => $count, 
            'posts' => $posts ,
            'is_left' => $page == 0 ? -1 : $page - 1,
            'is_right' => count($posts) > $limit ? $page + 1 : -1,
            'page' => $page
        ];
    }

    public function getAllProperties(){
        $stmt = "SELECT 
                DISTINCT CONCAT(property_type , '--' , animal_property) as property
                from shelter_animal_Property ";
        $stmt= $this->pdo->prepare($stmt);

        $stmt->execute();

        return $stmt ->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getFilteredAdoptionListing($filter = [])
    {
        $placeholders = implode(',', array_fill(0, count($filter), '?'));

        $stmt = "
        WITH animal_cte AS (
            SELECT
                shelter_animals.animal_id     AS shelter_id,
                shelter_animals.animal_name   AS animal_name,
                shelter_animals.animal_age    AS animal_age,
                shelter_animals.health_status AS health_status,
                Adoption_animals.animal_id    AS animal_id,
                Adoption_animals.created_at   AS created_at
            FROM shelter_animals
            INNER JOIN Adoption_animals
                ON Adoption_animals.shelter_id = shelter_animals.animal_id
        ),
        image_table AS (
            SELECT
                animal_id AS shelter_id,
                GROUP_CONCAT(image_path SEPARATOR ';;;') AS image_path
            FROM shelter_animals_images
            GROUP BY animal_id
        )

        SELECT
            animal_cte.shelter_id    AS shelter_id,
            animal_cte.animal_name   AS animal_name,
            animal_cte.animal_age    AS animal_age,
            animal_cte.health_status AS health_status,
            animal_cte.animal_id     AS animal_id,
            animal_cte.created_at    AS created_at,
            image_table.image_path   AS image_path,

            GROUP_CONCAT(
                DISTINCT CONCAT(
                    shelter_animal_Property.property_type,
                    '--',
                    shelter_animal_Property.animal_property
                )
                SEPARATOR ';;;'
            ) AS animal_properties

        FROM animal_cte

        INNER JOIN image_table
            ON image_table.shelter_id = animal_cte.shelter_id

        LEFT JOIN shelter_animal_Property
            ON shelter_animal_Property.animal_id = animal_cte.shelter_id

        LEFT JOIN Adoption_Application
            ON Adoption_Application.animal_id = animal_cte.animal_id

        WHERE
            (
                Adoption_Application.adoption_application_status = 'pending'
                OR Adoption_Application.adoption_application_status IS NULL
            )
        ";

        if (!empty($filter)) {
            $stmt .= "
            AND animal_cte.shelter_id IN (
                SELECT animal_id
                FROM shelter_animal_Property
                WHERE animal_property IN ($placeholders)
                GROUP BY animal_id
                HAVING COUNT(DISTINCT animal_property) = ?
            )
            ";
        }

        $stmt .= "
        GROUP BY
            shelter_id,
            animal_name,
            animal_age,
            health_status,
            animal_id,
            created_at,
            image_path
        ";

        $params = $filter;

        if (!empty($filter)) {
            $params[] = count($filter);
        }

        $stmt2 = $this->pdo->prepare($stmt);
        $stmt2->execute($params);

        return $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
}



?>