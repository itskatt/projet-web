<?php

require_once "../_common/handler.php";

class LogoutHandler extends LoginRequiredHandler
{
    protected function handlePOST(?array $data): void
    {
        $token = $_COOKIE["token"];

        // On supprime le cookie
        setcookie("token", "", time() - 10, "/");

        // Et la/les session(s) dans la BDD
        $conn = $this->getConnector();
        $conn->query(
            <<<END
            delete from session
            where client_email = (
                select client_email from session
                where token = :token
                );
            END,
            ["token" => $token]
        );

        $this->sendOK([]);
    }
}

$handler = new LogoutHandler();
$handler->handle();
