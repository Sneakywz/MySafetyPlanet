<?php

namespace src\Repository;

use src\Entity\Db;
use src\Entity\Article;

class ArticleRepository {

    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(Article $article)
    {
        $this->db->query(
            "INSERT INTO articles (title, user_id, content, createdAt, updatedAt) VALUES (:title, :user_id, :content, :createdAt, :updatedAt)", 
            [
                'title' => $article->getTitle(),
                'user_id' => $article->getUser()->getId(),
                'content' => $article->getContent(),
                'createdAt' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $article->getUpdatedAt()->format('Y-m-d H:i:s'),
            ]
        );
    }

    public function delete(Article $article)
    {
        $this->db->query(
            "DELETE FROM articles WHERE id = :id", 
            [
                'id' => $article->getId(),
            ]
        );
    }

    /**
     * Retourne tout les utilisateurs de la base de données
     * 
     * @return Article[] // Retourne un tableau d'objet Article
     */
    public function findAll(): array {
        $result = $this->db->query("SELECT 
            articles.*, users.id as user_id, users.firstname, users.lastname, users.email, users.password, users.roles
         FROM articles INNER JOIN users ON articles.user_id = users.id"); // tableau

        // un tableau d'objets Article
        $articles = []; // stocker tout les objets articles dedans
        foreach($result as $article) {
            $articles[] = Article::hydrate($article);
        }

        return $articles;
    }

    public function findOneById(int $id): ?Article
    {
        $result = $this->db->query("SELECT 
            a.*, u.id as user_id, u.firstname, u.lastname, u.email, u.password, u.roles
            FROM articles as a INNER JOIN users as u ON a.user_id = u.id WHERE a.id = :id",
        [
            'id' => $id
        ]);

        if (count($result) > 0) {
            return Article::hydrate($result[0]);
        }

        return null;
    }

    public function updateArticle(Article $article): void
    {
        $this->db->query(
            "UPDATE articles SET 
                title = :title,
                content = :content,
                updatedAt = :updatedAt
            WHERE id = :id",
            [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
                'updatedAt' => $article->getUpdatedAt()->format('Y-m-d H:i:s'),
            ]
        );
    }
}