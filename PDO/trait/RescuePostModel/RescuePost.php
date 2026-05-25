<?php
include_once __DIR__."/RescuePostUtility.php";

trait RescuePost
{
    use RescuePostUtility;
    public function upload_rescue_post()
    {
        try {

            $uniqid_ = $this->UUID_GENERATOR();
            $stmt    = "insert into 
            rescue_post(rescue_post_id, rescue_post , animal_species_type ,
                         animal_gender_type , animal_age ,post_loc_latitude ,
                        post_loc_longtitude ,user_id ,sos_level) values(?,?,?,?,?,?,?,?,?);";


            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);

            $stmt
            ->execute([$uniqid_, $_POST['post'], $_POST['species_type'], 
                $_POST['gender'], $_POST['age'], $_POST['latitude'], $_POST['longitude'], 
            $_SESSION['id'] , $_POST['sos_level']]);

            $obj =$this->upload_multiple_images(); 

            foreach($obj as $value ){
                $this->pdo_initializer();

                $stmt = "INSERT INTO rescue_post_image(rescue_post_id ,rescue_post_image_link)
                        values(?,?);";
                $stmt= $this->pdo->prepare($stmt);

                $stmt->execute([$uniqid_ , $value]);
            }

            return $uniqid_;

        } catch (PDOException $e) {
            exit(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
        }

    }

    public function see_rescue_post($id)
    {

        try {
            $stmt = "
                SELECT 
                    rescue_post.rescue_post_id as rescue_post_id,
                    rescue_post,
                    animal_species_type,
                    animal_gender_type,
                    animal_age,
                    post_loc_latitude,
                    post_loc_longtitude,
                    post_time_stamp,
                    sos_level,
                    GROUP_CONCAT(DISTINCT rescue_post_image.rescue_post_image_link SEPARATOR ';;;' ) 
                    as rescue_post_image_link
                    FROM rescue_post 
                    inner join rescue_post_image 
                    on rescue_post.rescue_post_id = rescue_post_image.rescue_post_id 
                    WHERE rescue_post.rescue_post_id =?;
                    GROUP BY
                        rescue_post_id,
                        rescue_post,
                        animal_species_type,
                        animal_gender_type,
                        animal_age,
                        post_loc_latitude,
                        post_loc_longtitude,
                        post_time_stamp,
                        sos_level

            ";
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$id]);

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

    public function see_rescue_posts($offset, $limit = 10)
    {
        $this->pdo_initializer();

        $stmt = $this->pdo->prepare(
                    "SELECT 
                    rescue_post.rescue_post_id as rescue_post_id,
                    rescue_post,
                    animal_species_type,
                    animal_gender_type,
                    animal_age,
                    post_loc_latitude,
                    post_loc_longtitude,
                    post_time_stamp,
                    sos_level,
                    GROUP_CONCAT(DISTINCT rescue_post_image.rescue_post_image_link SEPARATOR ';;;' ) 
                    as rescue_post_image_link
                    FROM rescue_post 
                    inner join rescue_post_image 
                    on rescue_post.rescue_post_id = rescue_post_image.rescue_post_id 
                    GROUP BY
                        rescue_post_id,
                        rescue_post,
                        animal_species_type,
                        animal_gender_type,
                        animal_age,
                        post_loc_latitude,
                        post_loc_longtitude,
                        post_time_stamp,
                        sos_level
                    ORDER BY post_time_stamp asc 
                    LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) ($offset), PDO::PARAM_INT);

        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $count = $this->total_rescue_posts($limit , $offset);

        return ['count' => $count, 'posts' => $posts];
    }
}
