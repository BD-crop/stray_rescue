<?php
include_once __DIR__."/RescuePostUtility.php";

trait RescuePost
{
    use RescuePostUtility;
    public function upload_rescue_post()
    {
        try {
            /*
            START TRANSACTION;
            INSERT INTO rescue_post(...) VALUES (...);
            INSERT INTO rescue_post_image(...) VALUES (...);
            INSERT INTO rescue_post_image(...) VALUES (...);
            INSERT INTO animals(...) VALUES (...);

            UPDATE rescue_post
            SET animal_id = ?
            WHERE rescue_post_id = ?;

            INSERT INTO animal_history(...) VALUES (...);

            INSERT INTO animal_history_image_upload(...) VALUES (...);
            INSERT INTO animal_history_image_upload(...) VALUES (...);

            INSERT INTO animal_history(...) VALUES (...);

            INSERT INTO animal_history_image_upload(...) VALUES (...);
            INSERT INTO animal_history_image_upload(...) VALUES (...);

            COMMIT;

            on error

            ROLLBACK;
            */

            $uniqid_ = $this->UUID_GENERATOR();
            $qr_path = $this->qr_code_generator($uniqid_);
            $animal_id = $this->UUID_GENERATOR();
            $history_id = $this->UUID_GENERATOR();
            $second_history = $this->UUID_GENERATOR();

            $this->pdo_initializer();

            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "INSERT INTO rescue_post
                (
                    
                    rescue_post_id,
                    rescue_post,
                    animal_species_type,
                    animal_gender_type,
                    animal_age,
                    post_loc_latitude,
                    post_loc_longtitude,
                    user_id,
                    sos_level,
                    qr_image,
                    address
                )
                VALUES (?,?,?,?,?,?,?,?,?,?,?)"
            );

            $stmt->execute([
                
                $uniqid_,
                $_POST['post'],
                $_POST['species_type'],
                $_POST['gender'],
                $_POST['age'],
                $_POST['latitude'],
                $_POST['longitude'],
                $_SESSION['id'],
                $_POST['sos_level'],
                $qr_path,
                $_POST['address'] ?? 'not given'
            ]);

            $obj = $this->upload_multiple_images();

            $stmt = $this->pdo->prepare(
                "INSERT INTO rescue_post_image
                (rescue_post_id, rescue_post_image_link)
                VALUES (?,?)"
            );

            foreach ($obj as $value) {
                $stmt->execute([$uniqid_, $value]);
            }

            $stmt = $this->pdo->prepare(
                "INSERT INTO animals
                (
                    name,
                    animal_id,
                    species_type,
                    gender_type,
                    age,
                    health_status
                )
                VALUES (?,?,?,?,?,?)"
            );

            $stmt->execute([
                $_POST['name'],
                $animal_id,
                $_POST['species_type'],
                $_POST['gender'],
                $_POST['age'],
                $_POST['sos_level']
            ]);

            $stmt = $this->pdo->prepare(
                "UPDATE rescue_post
                SET animal_id = ?
                WHERE rescue_post_id = ?"
            );

            $stmt->execute([$animal_id, $uniqid_]);
            if(!empty($_POST['animal_parent_id'])){
                $stmt = $this->pdo->prepare(
                    "UPDATE animals
                    set animal_parent_id = ?
                    where animal_id = ? ; "
                );

                $stmt->execute([$_POST['animal_parent_id'],$animal_id ]);
            }
            $stmt = $this->pdo->prepare(
                "INSERT INTO animal_history
                (
                    animal_id,
                    history_id,
                    level_text,
                    level_description,
                    created_by
                )
                VALUES (?,?,'registered',?,?)"
            );

            $stmt->execute([
                $animal_id,
                $history_id,
                $_POST['post'],
                $_SESSION['id']
            ]);

            $stmt = $this->pdo->prepare(
                "INSERT INTO animal_history_image_upload
                (history_id, image_link)
                VALUES (?,?)"
            );

            foreach ($obj as $value) {
                $stmt->execute([$history_id, $value]);
            }

            if ($_POST['sos_level'] == 2 || $_POST['sos_level'] == 3) {

                $stmt = $this->pdo->prepare(
                    "INSERT INTO animal_history
                    (
                        animal_id,
                        history_id,
                        level_text,
                        level_description,
                        created_by,
                        sos_level
                    )
                    VALUES (?,?,'health Update',?,?,?)"
                );

                $stmt->execute([
                    $animal_id,
                    $second_history,
                    $_POST['post'],
                    $_SESSION['id'],
                    $_POST['sos_level']
                ]);

                $stmt = $this->pdo->prepare(
                    "INSERT INTO animal_history_image_upload
                    (history_id, image_link)
                    VALUES (?,?)"
                );

                foreach ($obj as $value) {
                    $stmt->execute([$second_history, $value]);
                }
            }

            $this->pdo->commit();

            return $uniqid_;

        } catch (Throwable $e) {

            if (
                isset($this->pdo) &&
                $this->pdo instanceof PDO &&
                $this->pdo->inTransaction()
            ) {
                $this->pdo->rollBack();
            }

            exit(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
        }
    }

    public function see_rescue_post($id)
    {

        try {
            $stmt = "WITH recursive animal_family_tree AS (
                SELECT 
                    name ,
                    ani_outer.animal_id as animal_id,
                    species_type ,
                    gender_type,
                    age,
                    0 as node_number,
                    animal_parent_id
                    from rescue_post
                    inner join animals ani_outer on rescue_post.animal_id = ani_outer.animal_id
                    where rescue_post.rescue_post_id = ?
                    
                    UNION ALL
                    (
                    SELECT
                        parent.name,
                        parent.animal_id,
                        parent.species_type,
                        parent.gender_type,
                        parent.age,
                        child.node_number + 1 ,
                        parent.animal_parent_id
                    from animals parent
                    inner join animal_family_tree as child
                    on parent.animal_id = child.animal_parent_id


                    )
            ),
            animal_family_tree_with_image_cte AS (

                SELECT 
                    aft.*,
                    img.image
                FROM animal_family_tree aft

                LEFT JOIN (
                    SELECT 
                        animal_history.animal_id,
                        animal_history_image_upload.image_link AS image
                    FROM animal_history
                    INNER JOIN animal_history_image_upload
                        ON animal_history_image_upload.history_id = animal_history.history_id
                    GROUP BY animal_history.animal_id
                ) img
                    ON img.animal_id = aft.animal_id
            ),   
            animal_history_text_image_cte AS (
                    SELECT 
                    animal_history.animal_id as animal_id, 
                    animal_history.history_id as history_id,
                    level_text
                    ,level_description , animal_history.sos_level as sos_level, 
                    animal_history.created_by as created_by , animal_history.created_by_type as created_by_type,
                    animal_history.created_at,
                    GROUP_CONCAT(DISTINCT animal_history_image_upload.image_link SEPARATOR '---') as history_images
                    
                    from rescue_post
                    inner join animal_history on animal_history.animal_id = rescue_post.animal_id
                    inner join animal_history_image_upload on
                    animal_history.history_id = animal_history_image_upload.history_id 
                    where rescue_post.rescue_post_id = ?
                    GROUP BY
                        animal_id,
                        history_id,
                        level_text,
                        level_description,
                        sos_level,
                        created_by, 
                        created_by_type,
                        created_at
                )
                SELECT 
                    anim.animal_id as animal_id, 
                    anim.name as name,
                    rp.rescue_post_id,
                    rp.rescue_post,
                    rp.animal_species_type,
                    rp.animal_gender_type,
                    rp.animal_age,
                    rp.post_loc_latitude,
                    rp.post_loc_longtitude,
                    rp.post_time_stamp,
                    rp.sos_level,

                    GROUP_CONCAT(DISTINCT rpi.rescue_post_image_link SEPARATOR ';;;') 
                        AS rescue_post_image_link,

                    GROUP_CONCAT(
                        DISTINCT CONCAT(
                            ah.history_id, '||',
                            ah.level_text, '||',
                            ah.level_description, '||',
                            COALESCE(ah.sos_level, ''), '||',
                            ah.created_by, '||',
                            ah.created_by_type, '||',
                            ah.created_at , '||',
                            ah.history_images
                        )
                        SEPARATOR ';;;'
                    ) AS animal_history,

                    GROUP_CONCAT(DISTINCT 
                        CONCAT(
                            COALESCE(ahi.name, 'UNKNOWN'), '||',
                            COALESCE(ahi.animal_id, ''), '||',
                            COALESCE(ahi.species_type, ''), '||',
                            COALESCE(ahi.gender_type, ''), '||',
                            COALESCE(ahi.age, ''), '||',
                            COALESCE(ahi.node_number, ''), '||',
                            COALESCE(ahi.animal_parent_id, ''), '||',
                            COALESCE(ahi.image, '')
                        )
                        ORDER BY ahi.node_number
                        SEPARATOR ';;;'
                    ) AS family_tree,

                rp.qr_image as qr_image

                FROM rescue_post rp

                INNER JOIN animals as anim
                    ON rp.animal_id = anim.animal_id

                INNER JOIN rescue_post_image rpi
                    ON rp.rescue_post_id = rpi.rescue_post_id

                LEFT JOIN animal_history_text_image_cte ah
                    ON rp.animal_id = ah.animal_id

                LEFT JOIN animal_family_tree_with_image_cte ahi
                on 1 = 1 

                WHERE rp.rescue_post_id = ?

                GROUP BY 
                    rp.rescue_post_id,
                    rp.rescue_post,
                    rp.animal_species_type,
                    rp.animal_gender_type,
                    rp.animal_age,
                    rp.post_loc_latitude,
                    rp.post_loc_longtitude,
                    rp.post_time_stamp,
                    rp.sos_level,
                    rp.qr_image

                limit 1
            ;";
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$id, $id,$id]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            return $data;
        } catch (PDOException $e) {
            exit(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
        }
        return "";
    }

    public function see_rescue_post_by_animal_id($id){

        try {
            $stmt = "WITH recursive animal_family_tree AS (
                SELECT 
                    name ,
                    ani_outer.animal_id as animal_id,
                    species_type ,
                    gender_type,
                    age,
                    0 as node_number,
                    animal_parent_id
                    from animals ani_outer
                    where ani_outer.animal_id = ?
                    
                    UNION ALL
                    (
                    SELECT
                        parent.name,
                        parent.animal_id,
                        parent.species_type,
                        parent.gender_type,
                        parent.age,
                        child.node_number + 1 ,
                        parent.animal_parent_id
                    from animals parent
                    inner join animal_family_tree as child
                    on parent.animal_id = child.animal_parent_id


                    )
            ),
            animal_family_tree_with_image_cte AS (

                SELECT 
                    aft.*,
                    img.image
                FROM animal_family_tree aft

                LEFT JOIN (
                    SELECT 
                        animal_history.animal_id,
                        animal_history_image_upload.image_link AS image
                    FROM animal_history
                    INNER JOIN animal_history_image_upload
                        ON animal_history_image_upload.history_id = animal_history.history_id
                    GROUP BY animal_history.animal_id
                ) img
                    ON img.animal_id = aft.animal_id
            ),   
            animal_history_text_image_cte AS (
                    SELECT 
                    animal_history.animal_id as animal_id, 
                    animal_history.history_id as history_id,
                    level_text
                    ,level_description , animal_history.sos_level as sos_level, 
                    animal_history.created_by as created_by , animal_history.created_by_type as created_by_type,
                    animal_history.created_at,
                    GROUP_CONCAT(DISTINCT animal_history_image_upload.image_link SEPARATOR '---') as history_images
                    
                    from animals
                    inner join animal_history on animal_history.animal_id = animals.animal_id
                    inner join animal_history_image_upload on
                    animal_history.history_id = animal_history_image_upload.history_id 
                    where animals.animal_id = ?
                    GROUP BY
                        animal_id,
                        history_id,
                        level_text,
                        level_description,
                        sos_level,
                        created_by, 
                        created_by_type,
                        created_at
                )
                SELECT 
                    anim.animal_id as animal_id, 
                    anim.name as name,
                    rp.rescue_post_id,
                    rp.rescue_post,
                    rp.animal_species_type,
                    rp.animal_gender_type,
                    rp.animal_age,
                    rp.post_loc_latitude,
                    rp.post_loc_longtitude,
                    rp.post_time_stamp,
                    rp.sos_level,

                    GROUP_CONCAT(DISTINCT rpi.rescue_post_image_link SEPARATOR ';;;') 
                        AS rescue_post_image_link,

                    GROUP_CONCAT(
                        DISTINCT CONCAT(
                            ah.history_id, '||',
                            ah.level_text, '||',
                            ah.level_description, '||',
                            COALESCE(ah.sos_level, ''), '||',
                            ah.created_by, '||',
                            ah.created_by_type, '||',
                            ah.created_at , '||',
                            ah.history_images
                        )
                        SEPARATOR ';;;'
                    ) AS animal_history,

                    GROUP_CONCAT(DISTINCT 
                        CONCAT(
                            COALESCE(ahi.name, 'UNKNOWN'), '||',
                            COALESCE(ahi.animal_id, ''), '||',
                            COALESCE(ahi.species_type, ''), '||',
                            COALESCE(ahi.gender_type, ''), '||',
                            COALESCE(ahi.age, ''), '||',
                            COALESCE(ahi.node_number, ''), '||',
                            COALESCE(ahi.animal_parent_id, ''), '||',
                            COALESCE(ahi.image, '')
                        )
                        ORDER BY ahi.node_number
                        SEPARATOR ';;;'
                    ) AS family_tree,

                rp.qr_image as qr_image

                FROM rescue_post rp

                INNER JOIN animals as anim
                    ON rp.animal_id = anim.animal_id

                INNER JOIN rescue_post_image rpi
                    ON rp.rescue_post_id = rpi.rescue_post_id

                LEFT JOIN animal_history_text_image_cte ah
                    ON rp.animal_id = ah.animal_id

                LEFT JOIN animal_family_tree_with_image_cte ahi
                on 1 = 1 

                WHERE anim.animal_id = ?

                GROUP BY 
                    rp.rescue_post_id,
                    rp.rescue_post,
                    rp.animal_species_type,
                    rp.animal_gender_type,
                    rp.animal_age,
                    rp.post_loc_latitude,
                    rp.post_loc_longtitude,
                    rp.post_time_stamp,
                    rp.sos_level,
                    rp.qr_image

                limit 1
            ;";
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$id, $id,$id]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            return $data;
        } catch (PDOException $e) {
            exit(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
        }
        return "";
    }

    public function total_rescue_posts($limit=PHP_INT_MAX , $offset =0)
    {
        $stmt = 'with view_cte as (
            select * from rescue_post limit :offset , :limit
        ) select count(*) from view_cte;';




        $this->pdo_initializer();
        $stmt = $this->pdo->prepare($stmt);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count;

    }

    public function see_rescue_posts($page,$name = "" ,$rank_by='post_time_stamp' , $limit = 10)
    {
        $allowedOrder = ['post_time_stamp', 'sos_level', 'animal_age'];

        if (!in_array($rank_by, $allowedOrder)) {
            $rank_by = 'post_time_stamp';
        }
        $offset = $page * $limit;

        $this->pdo_initializer();

        $stmt = $this->pdo->prepare(
                "WITH view_cte AS (
                    SELECT 
                    rescue_post.rescue_post_id as rescue_post_id,
                    rescue_post.rescue_post as rescue_post,
                    rescue_post.animal_species_type as animal_species_type,
                    rescue_post.animal_gender_type as animal_gender_type,
                    rescue_post.animal_age as animal_age,
                    rescue_post.post_loc_latitude as post_loc_latitude,
                    rescue_post.post_loc_longtitude as post_loc_longtitude,
                    rescue_post.post_time_stamp as post_time_stamp,
                    rescue_post.sos_level as sos_level,
                    animals.name as name
                    FROM rescue_post 
                    inner join animals
                    on animals.animal_id = rescue_post.animal_id
                    WHERE name LIKE CONCAT(SUBSTRING(:name, 1, 1), '%')
                ),
                image_table as 
                (
                    SELECT rescue_post_id , rescue_post_image_link 
                    from rescue_post_image 
                    group by
                    rescue_post_id 
                )       
                 SELECT 
                    view_cte.rescue_post_id as rescue_post_id,
                    rescue_post,
                    animal_species_type,
                    animal_gender_type,
                    animal_age,
                    post_loc_latitude,
                    post_loc_longtitude,
                    post_time_stamp,
                    sos_level,
                    view_cte.name as name,
                    image_table.rescue_post_image_link as rescue_post_image_link
                    FROM view_cte 
                    inner join image_table 
                    on view_cte.rescue_post_id = image_table.rescue_post_id 
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
            'is_right' => count($posts) > 10 ? $page + 1 : -1,
            'page' => $page
        ];
    }

    

    public function update_history($id , $role){
        $images=$this->upload_multiple_images();
        $history_id = $this->UUID_GENERATOR();

        $stmt = "INSERT INTO 
                animal_history(animal_id , history_id, level_text
                ,level_description , sos_level, created_by , 
                created_by_type) values(?,?,?,?,?,?,?)";
        $stmt = $this->pdo->prepare($stmt);

        $stmt->execute([$_POST['animal_id'] , $history_id , $_POST['level_text'],
        $_POST['level_description'] , $_POST['sos_level'] , $id , $role ,  ]);
    
        foreach($images as $image){
            $stmt = "INSERT INTO 
                animal_history_image_upload
                (history_id, image_link) values(?,?)";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$history_id , $image]);
        }

        
    }   


    public function get_children_of_animal($ani_id)
    {
        $stmt = "
            SELECT *
            FROM animals
            WHERE animal_parent_id = ?
        ";

        $query = $this->conn->prepare($stmt);
        $query->execute([$ani_id]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
