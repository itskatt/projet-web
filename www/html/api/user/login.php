<?php

require_once "../_common/handler.php";

class LoginHandler extends PublicHandler
{
    protected function handlePOST($data): void
    {
        $this->checkFields(
            ["email", "password"],
            $data
        );

        $conn = $this->getConnector();

        $res = $conn->query(
            <<<END
            select email,
                   password_ "password"
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

        $this->startSession($data["email"]);

        $this->sendOK([]);
    }
}

$handler = new LoginHandler();
$handler->handle();