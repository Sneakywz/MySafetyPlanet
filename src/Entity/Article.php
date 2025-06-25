<?php 

namespace src\Entity;

use src\Entity\User;

class Article {

    private ?int $id;
    private string $title;
    private User $user;
    private string $content;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public static function hydrate(array $data): Article
    {
        $article = new Article();
        $article->setId($data['id'] ?? null);
        $article->setTitle($data['title']);
        $article->setContent($data['content']);
        $article->setCreatedAt(new \DateTime($data['createdAt']));
        $article->setUpdatedAt(new \DateTime($data['updatedAt']));
        
        $userData = [
            'id' => $data['user_id'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => $data['password'],
            'roles' => $data['roles'],
        ];

        $article->setUser(User::hydrate($userData));

        return $article;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}


