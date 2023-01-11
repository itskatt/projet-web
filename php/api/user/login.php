<?php

require_once "../_common/handler.php";

class LoginHandler extends PublicHandler
{
    protected function handlePOST($data): void
    {
        $this->checkFields(
            ["email", "password", "remember"],
            $data
        );

        $conn = $this->getConnector();

        $res = $conn->query(
            <<<END
            select email,
                   last_name,
                   first_name,
                   password_ "password",
                   admin_ "admin"
            from client
            where email = :email;
            END,
            [
                "email" => $data["email"]
            ]
        )->fetch(PDO::FETCH_ASSOC);

        $hash = $res["password"];

        if (!password_verify($data["password"], $hash)) {
            $this->sendError(
                401,
                "Email ou mot de passe incorect !"
            );
        }

        // Si l'utilisateur est un admin, il reçois un avertissement si le stock d'un article < 10
        $warnings = [];
        if ($res["admin"]) {
            $warnings = $conn->query(
                "select article_id, quantity from stock where quantity < 10;"
            )->fetchAll(PDO::FETCH_ASSOC);
        }

        $this->startSession($data["email"], boolval($data["remember"]));

        $this->sendOK([
            "last_name" => $res["last_name"],
            "first_name" => $res["first_name"],
            "admin" => $res["admin"],
            "warnings" => $warnings
        ]);
    }
}

$handler = new LoginHandler();
$handler->handle();
