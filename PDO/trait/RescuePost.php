<?php

trait RescuePost
{
    public function upload_rescue_post()
    {
        try {

            $uniqid_ = $this->UUID_GENERATOR();
            $stmt    = "insert into rescue_post(rescue_post_id,rescue_post_image_link , rescue_post , animal_species_type , animal_gender_type , animal_age ,post_loc_latitude , post_loc_longtitude ,user_id) values(?,?,?,?,?,?,?,?,?);";

            $global_path = $this->image_upload() ?? '';

            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);

            $stmt->execute([$this->UUID_TO_BIN($uniqid_), $global_path, $_POST['post'], $_POST['species_type'], $_POST['gender'], $_POST['age'], $_POST['latitude'], $_POST['longitude'], $this->UUID_TO_BIN($_SESSION['id'])]);

            return $uniqid_;

        } catch (PDOException $e) {
            exit(json_encode($e->message(), JSON_PRETTY_PRINT));
        }

    }

    public function see_rescue_post($id)
    {

        try {
            $stmt = "
                SELECT

                    LOWER(CONCAT(
                        SUBSTR(HEX(rescue_post_id), 1, 8), '-',
                        SUBSTR(HEX(rescue_post_id), 9, 4), '-',
                        SUBSTR(HEX(rescue_post_id), 13, 4), '-',
                        SUBSTR(HEX(rescue_post_id), 17, 4), '-',
                        SUBSTR(HEX(rescue_post_id), 21)
                    )) AS rescue_post_id,

                    rescue_post_image_link,
                    rescue_post,
                    animal_species_type,
                    animal_gender_type,
                    animal_age,
                    post_loc_latitude,
                    post_loc_longtitude

                FROM rescue_post

                WHERE rescue_post_id =?;
            ";
            $this->pdo_initializer();

            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute([$this->UUID_TO_BIN($id)]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            return $data;
        } catch (PDOException $e) {
            exit(json_encode($e->message(), JSON_PRETTY_PRINT));
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

        $stmt = $this->pdo->prepare("SELECT 
        LOWER(CONCAT(
                        SUBSTR(HEX(rescue_post_id), 1, 8), '-',
                        SUBSTR(HEX(rescue_post_id), 9, 4), '-',
                        SUBSTR(HEX(rescue_post_id), 13, 4), '-',
                        SUBSTR(HEX(rescue_post_id), 17, 4), '-',
                        SUBSTR(HEX(rescue_post_id), 21)
                    )) as rescue_point_id,
                    rescue_post_image_link,
                    rescue_post,
                    animal_species_type,
                    animal_gender_type,
                    animal_age,
                    post_loc_latitude,
                    post_loc_longtitude,
                    post_time_stamp,
                    sos_level
                    FROM rescue_post ORDER BY post_time_stamp asc LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) ($offset), PDO::PARAM_INT);

        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $count = $this->total_rescue_posts($limit , $offset);

        return ['count' => $count, 'posts' => $posts];
    }
}
