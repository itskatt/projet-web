<?php

require_once "../_common/handler.php";

class LogoutHandler extends LoginRequiredHandler
{
    protected function handlePOST(?array $data): void
    {
        // On supprime le cookie
        setcookie("token", "", time() - 10, "/");

        // Et la/les session(s) dans la BDD
        $conn = $this->getConnector();
        $conn->query(
            <<<END
            delete from session
            where client_email = :email;
            END,
            ["email" => $this->email]
        );

        $this->sendOK([]);
    }
}

$handler = new LogoutHandler();
$handler->handle();
