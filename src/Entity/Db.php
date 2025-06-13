<?php

namespace src\Entity;

use PDO;
use PDOException;

class Db
{
    private $host = "db";
    private $username = "root";
    private $password = "root";
    private $dbName = "blog_cours";
    public $pdo;

    public function __construct(){
        try{
            $this->pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbName, $this->username, $this->password);

            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            echo 'Erreur de connexion : ' . $e->getMessage();
        }
    }

    public function query(string $sql, array $data = []): array
    {
        $request = $this->pdo->prepare($sql);
        $request->execute($data);

        return $request->fetchAll(\PDO::FETCH_ASSOC);
    }
}