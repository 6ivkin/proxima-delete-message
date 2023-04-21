<?php

/**
 * MODEL
 * A class for connecting and querying from a database
 * Class DAO
 */
class DAO
{
    private PDO $connection;

    /**
     * Class instance constructor
     */
    public function __construct()
    {
        $this->connection = new PDO('mysql:host=localhost;dbname=ruskon;port=3306', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    /**
     * @param string $product
     * @return int
     * Getting data from a database
     */
    public function getAvgPrice(string $product): int
    {
        try {
            $sql = "SELECT AVG(price) from products WHERE name = '$product'";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $price = $stmt->fetch();
        } catch (PDOException $ex) {
            var_dump($ex->getMessage());
        }
        return round($price[0]);
    }

    /**
     * @param array $data
     * @return void
     * Uploading data to the database
     */
    public function makeUpload(array $data): void
    {
        $this->clearDatabase();

        try {
            $sql = "INSERT INTO products(id, name, company, price) VALUES (null, :name, :company, :price)";
            $stmt = $this->connection->prepare($sql);
            foreach ($data as $item) {
                $stmt->execute(['name' => $item['name'], 'company' => $item['company'], 'price' => $item['price']]);
            }

            $dropRuskon = "DELETE FROM products WHERE name = 'РусКон-С'";
            $stmt = $this->connection->prepare($dropRuskon);
            $stmt->execute();
        } catch (PDOException $ex) {
            var_dump($ex->getMessage());
        }
    }

    /**
     * @return void
     * Clearing the database of data
     */
    public function clearDatabase(): void
    {
        try {
            $sql = "DELETE FROM products";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
        } catch (PDOException $ex) {
            var_dump($ex->getMessage());
        }
    }

    /**
     * @param string $name
     * @return mixed
     * Returns count of rows
     */
    public function getCount(string $name): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM products WHERE name = '$name'";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $count = $stmt->fetch();
        } catch (PDOException $ex) {
            var_dump($ex->getMessage());
        }
        return $count[0];
    }

    /**
     * @param string $name
     * @return array
     * User assistance in the request
     */
    public function requestHelper(string $name): array
    {
        try{
            $sql = "SELECT DISTINCT name FROM products WHERE name LIKE '$name%'";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll();
        } catch (PDOException $ex) {
            var_dump($ex->getMessage());
        }
        return $data;
    }

    /**
     * @param string $name
     * @return array
     * Returns a list of the names of the products that are in the database
     */
    public function getHelp(string $name): array {
        $helper = [];

        if($this->getCount($name) == 0) {
            $req = $this->requestHelper($name);
            $helper = array_merge($helper, $req);
        }
        return $helper;
    }
}

