<?php

require_once "../_common/handler.php";

class CreateCartHandler extends LoginRequiredHandler
{
    protected function handlePOST(?array $data): void
    {
        $conn = $this->getConnector();

        // Est-ce qu'il y a déja un panier non-commandé ?
        $currentCardId = $conn->query(
            <<<END
            select id from cart
            where id not in (
                select c.id from cart c
                inner join invoice i on c.id = i.cart_id
                where c.client_email = :email
            );
            END,
            ["email" => $this->email]
        )->fetch(PDO::FETCH_NUM)[0];

        if ($currentCardId) {
            $this->sendError(
                409,
                "Ce compte possède déjà un panier non-commandé, pas la peine d'en créer un nouveau."
            );
        }

        $conn->query(
            "insert into cart (client_email) values (:email);",
            ["email" => $this->email]
        );

        $currentCardId = $conn->query(
            <<<END
            select id from cart
            where id not in (
                select c.id from cart c
                inner join invoice i on c.id = i.cart_id
                where c.client_email = :email
            );
            END,
            ["email" => $this->email]
        )->fetch(PDO::FETCH_NUM)[0];

        $this->sendOK([
            "cart_id" => $currentCardId
        ]);
    }
}

$handler = new CreateCartHandler();
$handler->handle();
