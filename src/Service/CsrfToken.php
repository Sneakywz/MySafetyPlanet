<?php 

namespace src\Service;

class CsrfToken{

    private int $ttl = 3600; // Durée de vie du token en secondes (1 heure)

    // Génère un token CSRF unique et le stocke dans la session
    public function generate(string $key){
        $token = bin2hex(random_bytes(32));
        $_SESSION[$this->tokenKey($key)] = $token; // Le token lui-même
        $_SESSION[$this->timeKey($key)] = time(); // La date de création du token
        return $token;
    }

    // Vérifie si le token est valide
    // Le token est valide s'il n'est pas expiré et correspond à celui stocké
    public function isValid(string $token, string $key): bool {
        $storedToken = $_SESSION[$this->tokenKey($key)] ?? null; // Récupère le token stocké avec la clé
        return !$this->isExpired($key) && $storedToken !== null && hash_equals($storedToken, $token); // Que le token n'est pas expiré et correspond à celui stocké
    }

    // Invalide le token en le supprimant de la session
    public function invalidate(string $key): void {
        unset($_SESSION[$this->tokenKey($key)]); // Supprime le token de la session
        unset($_SESSION[$this->timeKey($key)]); // Supprime la date de création du token
    }

    // Vérifie si le token est expiré
    public function isExpired(string $key): bool {
        $createdAt = $_SESSION[$this->timeKey($key)] ?? null;
        return !$createdAt || (time() - $createdAt > $this->ttl); // (15:05 - 15:00 > 1H)
    }

    public function tokenKey(string $key): string {
        return "csrf_token_" . $key;
    }

    public function timeKey(string $key): string {
        return "csrf_token_" . $key . '_time';
    }
}