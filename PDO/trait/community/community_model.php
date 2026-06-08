<?php
trait CommunityModel{
    function createCommunityChallenge($title, $description, $createdBy, $endsAt = null)
    {
        $sql = "INSERT INTO community_challenges 
            (id, title, description, created_by, ends_at)
            VALUES 
            (:id, :title, :description, :created_by, :ends_at)
        ";

        $stmt = $this->pdo->prepare($sql);

        $id = $this->UUID_GENERATOR();

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':created_by', $createdBy);

        if ($endsAt !== null) {
            $stmt->bindParam(':ends_at', $endsAt);
        } else {
            $stmt->bindValue(':ends_at', null, PDO::PARAM_NULL);
        }

        try {
            $stmt->execute();
            return $id; 
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getCommunityChallenges($userId)
    {
        $sql = "
            SELECT 
                cc.*,
                CASE 
                    WHEN cv.user_id IS NULL THEN 0
                    ELSE 1
                END AS vote_left
            FROM community_challenges cc

            LEFT JOIN challenge_votes cv
                ON cc.id = cv.challenge_id
                AND cv.user_id = :user_id

            ORDER BY cc.created_at DESC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':user_id', $userId);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return false;
        }
    }

    public  function challengeDetails($challenge_id, $user_id)
    {
        $sql = "WITH 
        challenge_with_total_vote as(
            SELECT 
                ce.*,
                CASE WHEN COUNT(cv.id) IS NULL THEN 0 
                ELSE COUNT(cv.id) END AS total_votes
            FROM challenge_entries ce
            LEFT JOIN challenge_votes cv
                ON ce.id = cv.entry_id
            GROUP BY ce.id

        ),
        challenge_entry_data as(
            SELECT 
                ce.*, 
                CASE 
                    WHEN cv.user_id IS NULL THEN 0
                    ELSE 1
                END AS is_voted
                FROM challenge_with_total_vote ce
                LEFT JOIN challenge_votes cv
                    ON ce.id = cv.entry_id
                    AND cv.user_id = :user_id
        )
        SELECT 
            ced.*,
            chall.title AS challenge_title,
            chall.description AS challenge_description,
            chall.created_at AS challenge_created_at,
            chall.ends_at AS challenge_ends_at
            FROM community_challenges chall 
            LEFT JOIN challenge_entry_data ced
                ON ced.challenge_id = chall.id
            WHERE chall.id = :challenge_id 
            ORDER BY ced.total_votes DESC;
        ";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':challenge_id', $challenge_id);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return $e->getMessage();
            return false;
        }
    }

    public function upload_challenge_entry($challenge_id, $user_id, $content)
    {
    try {

        $entry_id = $this->UUID_GENERATOR();
        $this->pdo_initializer();

        $this->pdo->beginTransaction();

        $stmt = $this->pdo->prepare(
            "INSERT INTO challenge_entries
            (
                id,
                challenge_id,
                user_id,
                content
            )
            VALUES (?,?,?,?)"
        );

        $stmt->execute([
            $entry_id,
            $challenge_id,
            $user_id,
            $content
        ]);


        
        $images = $this->upload_multiple_images();


        if (!empty($images)) {

            $stmt = $this->pdo->prepare(
                "INSERT INTO challenge_entry_images
                (
                    entry_id,
                    image_link
                )
                VALUES (?,?)"
            );

            foreach ($images as $img) {
                $stmt->execute([
                    $entry_id,
                    $img
                ]);
            }
        }


        $this->pdo->commit();

        return $entry_id;

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
    
    public function getIndividualEntry($entry_id, $user_id)
    {
        $sql = "
            SELECT 
                ce.id,
                ce.challenge_id,
                ce.user_id,
                ce.content,
                ce.created_at,

                GROUP_CONCAT(DISTINCT cei.image_link SEPARATOR ';;;') AS images,

                CASE 
                    WHEN cv.user_id IS NULL THEN 0
                    ELSE 1
                END AS is_voted

            FROM challenge_entries ce

            LEFT JOIN challenge_entry_images cei
                ON ce.id = cei.entry_id

            LEFT JOIN challenge_votes cv
                ON ce.id = cv.entry_id
                AND cv.user_id = :user_id

            WHERE ce.id = :entry_id

            GROUP BY 
                ce.id,
                ce.challenge_id,
                ce.user_id,
                ce.content,
                ce.created_at
        ";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':entry_id', $entry_id);
            $stmt->bindParam(':user_id', $user_id);

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }


    public function vote_challenge_entry()
    {
    try {

        $this->pdo_initializer();
        $vote_id = $this->UUID_GENERATOR();

        $this->pdo->beginTransaction();

        $this->unvote_challenge_entry();


        $stmt = $this->pdo->prepare(
            "INSERT INTO challenge_votes
            (
                id,
                challenge_id,
                entry_id,
                user_id
            )
            VALUES (?,?,?,?)"
        );

        $stmt->execute([
            $vote_id,
            $_POST['challenge_id'],
            $_POST['entry_id'],
            $_SESSION['id']
        ]);

        $this->pdo->commit();

        return true;

    } catch (Throwable $e) {

        if (
            isset($this->pdo) &&
            $this->pdo instanceof PDO &&
            $this->pdo->inTransaction()
        ) {
            $this->pdo->rollBack();
        }

        if (
            $e instanceof PDOException &&
            isset($e->errorInfo[1]) &&
            $e->errorInfo[1] == 1062
        ) {
            return false;
        }

        exit(json_encode(
            $e->getMessage(),
            JSON_PRETTY_PRINT
        ));
    }
    }

    public function unvote_challenge_entry()
    {
        try{
        $stmt = $this->pdo->prepare(
            "DELETE FROM challenge_votes
            WHERE 
                challenge_id = ? AND 
                user_id = ?"
        );

        $stmt->execute([
            $_POST['challenge_id'],
            $_SESSION['id']
        ]);
        } catch (Throwable $e) {
            exit(json_encode(
                $e->getMessage(),
                JSON_PRETTY_PRINT
            ));
        }


    }
    

}






?>
