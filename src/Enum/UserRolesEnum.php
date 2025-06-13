<?php 

namespace src\Enum;

enum UserRolesEnum : string
 {
    case USER = "user";
    case ADMIN = "admin";
    case SUPERADMIN = "superadmin";
    
    public function getLabel() : string
    {
        return match($this)
        {
            self::USER=>"Utilisateur", 
            self::ADMIN=>"Administrateur",
            self::SUPERADMIN=>"Super Administrateur",
        };
    }
}