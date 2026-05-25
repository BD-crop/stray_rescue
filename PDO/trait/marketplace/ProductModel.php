<?php

trait ProductModel
{
    public function create_product(
        $ProductName,
        $ProductDescription,
        $ProductPrice,
        $ProductStock,
        $type,
        $productImage
    ) {
        try {
            $this->pdo_initializer();
            $id = $this->UUID_GENERATOR();

            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                INSERT INTO products(
                    product_id, name, description, price, stock, rating, rating_count
                )
                VALUES (
                    :id, :name, :description, :price, :stock, 0, 0
                )
            ");

            $stmt->bindValue(':id', $id, PDO::PARAM_STR);
            $stmt->bindValue(':name', $ProductName, PDO::PARAM_STR);
            $stmt->bindValue(':description', $ProductDescription, PDO::PARAM_STR);
            $stmt->bindValue(':price', $ProductPrice, PDO::PARAM_INT);
            $stmt->bindValue(':stock', $ProductStock, PDO::PARAM_INT);
            $stmt->execute();



            foreach ($type as $value) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO product_category(product_id, type)
                    VALUES (:id, :type)
                ");
                $stmt->bindValue(':id', $id, PDO::PARAM_STR);
                $stmt->bindValue(':type', $value, PDO::PARAM_STR);
                $stmt->execute();
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO product_images(product_id, image_path)
                VALUES (:id, :image)
            ");

            $stmt->bindValue(':id', $id, PDO::PARAM_STR);
            $stmt->bindValue(':image', $productImage, PDO::PARAM_STR);
            $stmt->execute();

            $stmt = $this->pdo->prepare("
                INSERT INTO product_history(product_id, old_price, new_price)
                VALUES (:id, NULL, :price)
            ");

            $stmt->bindValue(':id', $id, PDO::PARAM_STR);
            $stmt->bindValue(':price', $ProductPrice, PDO::PARAM_INT);
            $stmt->execute();
            $this->pdo->commit();
            return $id;

        } catch (PDOException $e) {
            echo'-> '. $e->getMessage();
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return "";
        }
    }
    public function get_product($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM products
            WHERE products.product_id = :id
        ");

        $result = [];

        $result['product_detail'] =[];
        $stmt->execute([
            ':id' => $id
        ]);

        $result['product_detail'] = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $stmt = $this->pdo->prepare("
            SELECT *
            FROM product_history
            WHERE product_id = :id
        ");
        $stmt->execute([
            ':id' => $id
        ]);
        $result['result'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM product_images
            WHERE product_id = :id
        ");
        $stmt->execute([
            ':id' => $id
        ]);
        $result['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);


        return $result;
    }

    public function DeleteProduct($id)
    {
        $stmt = $this->pdo->prepare("
            UPDATE products
            SET is_deleted = 1
            WHERE product_id = :id
        ");

        $stmt->execute([
            ':id' => $id
        ]);
    }

    public function updateProduct($id, $description, $price, $stock, $past_price, $image_path)
    {
        try {
            $this->pdo->beginTransaction();

            $this->updateNONprice($id, $description, $stock);

            if ($image_path) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO product_images(product_id, image_path)
                    VALUES (:id, :image)
                ");

                $stmt->execute([
                    ':id' => $id,
                    ':image' => $image_path
                ]);
            }

            if ((float)$price !== (float)$past_price) {
                $this->updatePrice($id, $price);
            }

            $this->pdo->commit();

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
    public function updateNONprice($product_id, $description, $stock)
    {
        $stmt = $this->pdo->prepare("
            UPDATE products
            SET description = :description,
                stock = :stock
            WHERE product_id = :id
        ");

        $stmt->execute([
            ':id' => $product_id,
            ':description' => $description,
            ':stock' => $stock
        ]);
    }

    public function updatePrice($product_id, $price)
    {
        try {
            

            $stmt = $this->pdo->prepare("
                SELECT price
                FROM products
                WHERE product_id = :id
            ");

            $stmt->execute([
                ':id' => $product_id
            ]);

            $oldPrice = $stmt->fetchColumn();

            if ($oldPrice === false) {
                throw new Exception("Product not found");
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO product_history(product_id, old_price, new_price)
                VALUES (:product_id, :old, :new)
            ");

            $stmt->execute([
                ':product_id' => $product_id,
                ':old' => $oldPrice,
                ':new' => $price
            ]);

            $stmt = $this->pdo->prepare("
                UPDATE products
                SET price = :price
                WHERE product_id = :id
            ");

            $stmt->execute([
                ':price' => $price,
                ':id' => $product_id
            ]);

            

            return true;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function getProductsPagination($page, $name = "", $rank_by = 'created_at')
    {
        $allowedOrder = ['created_at', 'price', 'rating'];

        if (!in_array($rank_by, $allowedOrder)) {
            $rank_by = 'created_at';
        }

        $offset = $page * 12;

        $stmt = $this->pdo->prepare("
            WITH view_cte AS (
                SELECT *
                FROM products
                WHERE name LIKE CONCAT(SUBSTRING(:name, 1, 1), '%')
                AND is_deleted = 0
            )
            SELECT 
            view_cte.product_id , view_cte.name , view_cte.description , 
            view_cte.price , view_cte.stock , view_cte.rating , view_cte.rating_count 
            ,view_cte.created_at 
            ,GROUP_CONCAT(DISTINCT product_images.image_path separator ';;' ) as images 
            , GROUP_CONCAT(DISTINCT product_category.type separator ';;') as categories
            FROM view_cte inner join product_images 
            on product_images.product_id = view_cte.product_id
            inner join product_category on
            product_category.product_id = view_cte.product_id 
            group by 
                view_cte.product_id , view_cte.name , view_cte.description , 
                view_cte.price , view_cte.stock , view_cte.rating , view_cte.rating_count 
                ,view_cte.created_at
            ORDER BY
                levenshtein(name, :name) ASC,
                $rank_by DESC
            LIMIT 13 OFFSET :offset
        ");

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("

        ");

        return [
            'products' => $products,
            'is_left' => $page == 0 ? -1 : $page - 1,
            'is_right' => count($products) > 12 ? $page + 1 : -1,
            'page'    => $page
        ];
    }


}

?>