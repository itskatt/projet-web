<?php

require_once "../_common/handler.php";

class WhoAmIHandler extends PublicHandler
{
    protected function handleGET(): void
    {
        $token = $_COOKIE["token"];

        if (!$token) {
            $this->sendOK(
                ["message" => "Personne n'est connecté (pas de cookies)."]
            );
        }

        $conn = $this->getConnector();
        $email = $conn->query(
            <<<END
            select client_email from session where expires > now() and token = :token
            order by expires desc;
            END,
            ["token" => $token]
        )->fetch(PDO::FETCH_NUM)[0];
        
        if (!$email) {
            $this->sendOK(
                ["message" => "Personne n'est connecté (session expirée)."]
            );
        }

        $this->sendOK(
            ["message" => "Utilisateur connecté : $email"]
        );
    }
}

$handler = new WhoAmIHandler();
$handler->handle();