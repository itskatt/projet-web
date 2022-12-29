<?php

require_once "../_common/handler.php";

class PreviousCartHandler extends LoginRequiredHandler 
{
    protected function handleGET(): void
    {
        if (!isset($_GET["id"])) {
            $this->sendError(
                400,
                "Aucun ID renseigné pour trouver le panier."
            );
        }

        $conn = $this->getConnector();

        $id = $_GET["id"];

        $cart = $conn->query(
            <<<END
            select a.id as "article_id",
                a.name_ "article_name",
                description_ "description",
                rating,
                year,
                supplier_price * 1.08 "base_price",
                image_ "image",
                s.name_ "supplier_name",
                ca.quantity "cart_quantity",
                ca.quantity * supplier_price * 1.08 "price_no_tax",
                ca.quantity * supplier_price * 1.08 * 1.2 "price_tax"
            from article a
                    inner join supplier s on a.supplier_id = s.id
                    inner join cart_article ca on a.id = ca.article_id
                    inner join cart c on ca.cart_id = c.id
                    inner join invoice i on c.id = i.cart_id
            where c.id = :id;
            END,
            ["id" => $id]
        )->fetch(PDO::FETCH_ASSOC);

        if (!$cart) {
            // On verifie si le panier n'existe pas ou est vide
            $invoice = $conn->query(
                "select id from invoice where cart_id = :id;",
                ["id" => $id]
            )->fetch(PDO::FETCH_ASSOC);

            if (!$invoice) {
                $this->sendError(
                    404,
                    "Ce panier commandé n'existe pas ou est vide."
                );
            }

            $cart = [];
        }

        $this->sendOK([
            "cart" => $cart
        ]);
    }
}

$handler = new PreviousCartHandler();
$handler->handle();