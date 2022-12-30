<?php

require_once "../_common/handler.php";

class OrderCartHandler extends LoginRequiredHandler
{
    protected function handlePOST(?array $data): void
    {
        $currentCartId =  $this->getCurrentCartId();

        if (!$currentCartId) {
            $this->sendError(
                400,
                "Il n'y a pas de panier actuelement commandable, veillez en créer un."
            );
        }

        $conn = $this->getConnector();
        $conn->query(
            <<<END
            insert into invoice (cart_id, created)
            values ($currentCartId, now());
            END
        );

        // Pour tout les articles dans le panier, faire baisser leur stock
        $cartArticles = $conn->query(
            <<<END
            select article_id, quantity
            from cart_article
            where cart_id = $currentCartId;
            END
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cartArticles as $article) {
            $stock = $conn->query(
                <<<END
                select quantity
                    from stock
                    where article_id = :aid;
                END,
                [
                    "aid" => $article["article_id"]
                ]
            )->fetch(PDO::FETCH_NUM)[0];

            $newQuantity = $stock - $article["quantity"];

            $conn->query(
                <<<END
                update stock
                set quantity = :quant
                where article_id = :aid;
                END,
                [
                    "quant" => $newQuantity,
                    "aid" => $article["article_id"]
                ]
            );
        }

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
