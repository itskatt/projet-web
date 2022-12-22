<?php

require_once "../_common/handler.php";

class RegistrationHandler extends PublicHandler
{
    protected function handlePOST($data): void
    {
        $this->checkFields(
            ["email", "password", "last_name", "first_name"],
            $data
        );

        $email = $data["email"];

        // L'email est-elle valide ?
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sendError(
                400,
                "Adresse email invalide."
            );
        }

        $conn = $this->getConnector();

        // L'utilisateur existe déjà ?
        $count = $conn->query(
            <<<END
            select count(email) "nb" from client where email = :email;
            END,
            ["email" => $email]
        )->fetch(PDO::FETCH_NUM)[0];

        if ($count > 0) {
            $this->sendError(
                409,
                "Adresse email ($email) déjà en cours d'utilisation."
            );
        }

        $hash = password_hash($data["password"], PASSWORD_BCRYPT);
        $firstName = $data["first_name"];
        $lastName = $data["last_name"];

        // Tout est bon, on peut créer l'utilisateur
        $conn->query(
            <<<END
            insert into client (email, password_, last_name, first_name, admin_)
            values (:email, :pass, :ln, :fn, 0);
            END,
            [
                "email" => $email,
                "pass" => $hash,
                "ln" => $lastName,
                "fn" => $firstName
            ]
        );

        // TODO : also login the user
        $this->sendOK([
            "email" => $email
        ]);
    }
}

$handler = new RegistrationHandler();
$handler->handle();