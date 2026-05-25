<?php

trait PetCenterModel
{

    public function createRescueCenter(
        $name,
        $lat,
        $lng,
        $type,
        $email = null,
        $contact = null
    ) {
        try {
            $id = $this->UUID_GENERATOR();

            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                INSERT INTO PetCenters (
                    id, Name, lat, lng, type, center_email, center_contact_number
                )
                VALUES (
                    :id, :name, :lat, :lng, :type, :email, :contact
                )
            ");

            $stmt->execute([
                ":id" => $id,
                ":name" => $name,
                ":lat" => $lat,
                ":lng" => $lng,
                ":type" => $type,
                ":email" => $email,
                ":contact" => $contact
            ]);

            $this->pdo->commit();

            return $id;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function insertRescueCenterImages($center_id)
    {
        try {
            $path=$this->image_upload();
            
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                INSERT INTO PetCenterImages (
                     id, image_path
                )
                VALUES (
                     :center_id, :image_path
                )
            ");

            $stmt->execute([
                ":center_id" => $center_id,
                ":image_path" => $path
            ]);

            $this->pdo->commit();

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function removeRescueCenterImages($id)
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                DELETE FROM PetCenterImages 
                WHERE ImageID = :id
            ");

            $stmt->execute([
                ":id" => $id
            ]);

            $this->pdo->commit();

            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function ReadRescueCenter($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                PetCenters.id AS id,
                PetCenters.Name AS Name,
                PetCenters.lat AS lat,
                PetCenters.lng AS lng,
                PetCenters.type AS type,
                PetCenters.center_email AS email,
                PetCenters.center_contact_number AS number,

                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        PetCenterImages.ImageID,
                        '---',
                        PetCenterImages.image_path
                    )
                    SEPARATOR ';;;'
                ) AS images

            FROM PetCenters
            LEFT JOIN PetCenterImages 
                ON PetCenters.id = PetCenterImages.id

            WHERE PetCenters.id = :id

            GROUP BY 
                PetCenters.id,
                PetCenters.Name,
                PetCenters.lat,
                PetCenters.lng,
                PetCenters.type,
                PetCenters.center_email,
                PetCenters.center_contact_number
        ");

        $stmt->execute([
            ":id" => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function DeleteRescueCenter($id)
    {
        try {
            $this->pdo->beginTransaction();

            $stmt1 = $this->pdo->prepare("
                DELETE FROM PetCenterImages 
                WHERE center_id = :id
            ");

            $stmt1->execute([
                ":id" => $id
            ]);

            $stmt2 = $this->pdo->prepare("
                DELETE FROM PetCenters 
                WHERE id = :id
            ");

            $stmt2->execute([
                ":id" => $id
            ]);

            $this->pdo->commit();

            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getRescuePoints(){

        $stmt = $this->pdo->prepare("
            SELECT 
                PetCenters.id AS id,
                PetCenters.Name AS Name,
                PetCenters.lat AS lat,
                PetCenters.lng AS lng,
                PetCenters.type AS type,
                PetCenters.center_email AS email,
                PetCenters.center_contact_number AS number,
                CASE 
                    when PetCenters.type = 'gromming center' then 1
                    when PetCenters.type = 'veterenarian hospital' then 2
                    when PetCenters.type =  'park' then 3
                    ELSE 4
                END as value,
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        PetCenterImages.ImageID,
                        '---',
                        PetCenterImages.image_path
                    )
                    SEPARATOR ';;;'
                ) AS images

            FROM PetCenters
            LEFT JOIN PetCenterImages 
                ON PetCenters.id = PetCenterImages.id
            GROUP BY 
                PetCenters.id,
                PetCenters.Name,
                PetCenters.lat,
                PetCenters.lng,
                PetCenters.type,
                PetCenters.center_email,
                PetCenters.center_contact_number,
                value
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>