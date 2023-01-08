<?php

require_once "../_common/handler.php";

/**
 * Liste des factures.
 */
class InvoicesHandler extends LoginRequiredHandler
{
    protected function handleGET(): void
    {
        $conn = $this->getConnector();
        $invoices = $conn->query(
            <<<END
            select i.id "invoice_id",
                    i.cart_id,
                    i.created,
                    (select sum(quantity) from cart_article
                        where cart_id = i.cart_id) "num_articles"
                from invoice i
                inner join cart c on i.cart_id = c.id
                where c.client_email = :email;
            END,
            ["email" => $this->email]
        )->fetchAll(PDO::FETCH_ASSOC);

        $this->sendOK([
            "invoices" => $invoices
        ]);
    }
}

$handler = new InvoicesHandler();
$handler->handle();
