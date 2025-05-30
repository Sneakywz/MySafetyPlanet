<?php 

namespace src\Entity;

class User {

    private ?int $id;
    private string $firstname;
    private string $lastname;
    private string $email;
    private string $password;
    private array $roles;

    public static function hydrate(array $data): User
    {
        $user = new User();
        $user->setId($data['id'] ?? null); // Si pas d'id, on le met à null
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);

        // convertir le json en tableau
        $roles = json_decode($data['roles'], true);
        $user->setRoles($roles); // Si pas de role, on lui donne le role par défaut

        return $user;
    }

    public function getId(): ?int{
        return $this->id;
    }

    public function setId(?int $id){
        $this->id = $id;
    }

    public function getFirstname():string{
        return $this->firstname;
    }

    public function setFirstname(string $firstname){
        $this->firstname = $firstname;
    }

    public function getLastname():string{
        return $this->lastname;
    }

    public function setLastname(string $lastname){
        $this->lastname = $lastname;
    }

    public function getEmail():string{
        return $this->email;
    }

    public function setEmail(string $email){
        $this->email = $email;
    }

    public function getPassword():string{
        return $this->password;
    }

    public function setPassword(string $password){
        $this->password = $password;
    }

    public function getRoles(): array {
        $roles = $this->roles;

        return array_unique($roles); // Permet de ne pas avoir de doublons
    }

    public function setRoles(array $roles){
        $this->roles = $roles;
    }
}


