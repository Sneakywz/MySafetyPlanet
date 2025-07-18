<?php

namespace src\Repository;

use src\Entity\Db;
use src\Entity\User;

class UserRepository {

    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function create(User $user)
    {
        $this->db->query(
            "INSERT INTO users (firstname, lastname, email, password, roles) VALUES (:firstname, :lastname, :email, :password, :roles)", 
            [
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'roles' => json_encode($user->getRoles()),
            ]
        );
    }

    public function delete(User $user)
    {
        $this->db->query(
            "DELETE FROM users WHERE id = :id", 
            [
                'id' => $user->getId(),
            ]
        );
    }

    public function findByEmail(string $email): ?User
    {
        $result = $this->db->query(
            "SELECT * FROM users WHERE email = :email",
            [
                'email' => $email
            ]
        );
        // Si le résultat est un tableau, on hydrate l'objet User (on transforme le tableau en objet)
        if (isset($result[0])) {

            // La fonction hydrate est static, cela signifie quelle est lié à la class et non à l'objet
            return User::hydrate($result[0]);
        }

        // Si le résultat est vide, on retourne null
        // Cela signifie qu'il n'y a pas d'utilisateur avec cet email
        return null;
    }

    /**
     * Retourne tout les utilisateurs de la base de données
     * 
     * @return User[] // Retourne un tableau d'objet User
     */
    public function findAll(): array {
        $result = $this->db->query("SELECT * FROM users"); // tableau
        /**
         * $result = [
         *  0 => [
         *      'firstname' => ...
         *  ],
         *  1 => [
         *      'firsname' => ...
         *  ]
         * ]
         */

        // un tableau d'objets User
        $users = []; // stocker tout les objets users dedans
        foreach($result as $user) {
            $users[] = User::hydrate($user);
        }

        return $users;
    }

    public function findOneById(int $userId): ?User
    {
        $result = $this->db->query("SELECT * FROM users WHERE id = :id", [
            'id' => $userId
        ]);

        if (count($result) > 0) {
            return User::hydrate($result[0]);
        }

        return null;
    }

    public function updateRememberMe(string $token, int $userId): bool
    {
        $user = $this->findOneById($userId);

        if (!$user) {
            throw new \Exception("Cet utilisateur n'existe pas");
        }

        $this->db->query("UPDATE users SET remember_me = :token WHERE id= :userId", [
            'token' => $token,
            'userId' => $userId,
        ]);

        return true;
    }

    /**
     * Récupérer l'utilisateur qui possède le token remember_me
     */
    public function findOneByTokenRememberMe(string $token): ?User
    {
        $result = $this->db->query("SELECT * FROM users WHERE remember_me = :token", [
            'token' => $token
        ]);

        if ($result[0]) {
            return User::hydrate($result[0]);
        }

        return null;
    }

    public function updateUser(User $user): void
    {
        $this->db->query(
            "UPDATE users SET 
                firstname = :firstname,
                lastname = :lastname,
                email = :email,
                password = :password,
                roles = :roles
            WHERE id = :id",
            [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'roles' => json_encode($user->getRoles()),
            ]
        );
    }
}