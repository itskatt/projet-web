<?php

require_once "../_common/handler.php";

class OrderCartHandler extends LoginRequiredHandler 
{
    protected function handlePOST(?array $data): void
    {
        $currentCardId =  $this->getCurrentCartId();

        if (!$currentCardId) {
            $this->sendError(
                400,
                "Il n'y a pas de panier actuelement commandable, veillez en créer un."
            );
        }

        $conn = $this->getConnector();
        $conn->query(
            <<<END
            insert into invoice (cart_id, created)
            values ($currentCardId, now());
            END
        );

        $invoiceId = $conn->query(
            <<<END
            select auto_increment - 1 "id"
            from information_schema.TABLES
            where TABLE_SCHEMA = database() and TABLE_NAME = 'invoice';
            END
        )->fetch(PDO::FETCH_NUM)[0];

        $this->sendOK([
            "invoice_id" => $invoiceId
        ]);
    }
}

$handler = new OrderCartHandler();
$handler->handle();