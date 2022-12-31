<?php

require_once "../_common/handler.php";

/**
 * Crée un panier.
 */
class CreateCartHandler extends LoginRequiredHandler
{
    protected function handlePOST(?array $data): void
    {
        // Est-ce qu'il y a déja un panier non-commandé ?
        $currentCardId = $this->getCurrentCartId();

        if ($currentCardId) {
            $this->sendError(
                409,
                "Ce compte possède déjà un panier non-commandé, pas la peine d'en créer un nouveau."
            );
        }

        $conn = $this->getConnector();

        $conn->query(
            "insert into cart (client_email) values (:email);",
            ["email" => $this->email]
        );

        $currentCardId =  $this->getCurrentCartId();

        $this->sendOK([
            "cart_id" => $currentCardId
        ]);
    }
}

$handler = new CreateCartHandler();
$handler->handle();
