<?php

namespace src\Service;

class Authentification
{

    /**
     * Vérifie si l'utilisateur connecté a le rôle spécifié.
     * 
     * @param string $role Le rôle à vérifier.
     * @return bool True si l'utilisateur possède le rôle, sinon false.
     */
    public function hasUserRole(string $role): bool
    {
        return isset($_SESSION['user_roles']) && in_array($role, $_SESSION['user_roles']);
    }

    public function hasRole(string $role, array $roles) : bool
    {
        return in_array($role, $roles);
    }
    
    /**
     *  Protège une page en exigeant un rôle spécifique.
     *  Redirige vers une page d'accès refusé si l'utilisateur ne possède pas ce rôle.
     * 
     * @param string $role Le rôle requis.
     * @param string $redirect La page vers laquelle rediriger en cas d'accès refusé.
     * @return void
     */
    public function requireRole(string $role, string $redirect = "index.php"): void
    {
        // Si l'utilisateur n'a pas le rôle requis, on le redirige vers une page "no_access"
        if(!$this->hasUserRole($role)){
            header("Location: ../../" . $redirect);
            exit;
        }
    }

    /**
     * Protège une page en exigeant au moins un rôle parmi une liste.
     * Redirige vers une page d'accès refusé si l'utilisateur ne possède pas ce rôle.
     * 
     * @param array $roles Liste des rôles requis.
     * @param string $redirect La page vers laquelle rediriger en cas d'accès refusé.
     * @return void
     */
    public function requireAnyRole(array $roles, string $redirect = "index.php"): void
    {
        // On boucle sur les rôles autorisés. Si l'utilisateur en a un, on le laisse passer.
        // ex: $roles = ['admin', 'editor']
        foreach($roles as $role){
            if($this->hasUserRole($role)) return; // Si l'utilisateur a le rôle admin ou editor, on le laisse passer
        }

        // Si l'utilisateur n'a pas le rôle requis, on le redirige vers une page "no_access"
        header("Location: ../../" . $redirect);
        exit;
    }
}