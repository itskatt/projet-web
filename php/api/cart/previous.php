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

        $articles = $conn->query(
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
        )->fetchAll(PDO::FETCH_ASSOC);

        if (!$articles) {
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

            $articles = [];
        }

        // On récupère l'id de la facture, nombre total d'articles...
        $invoice = $conn->query(
            <<<END
            select i.id as                        "id",
                (select sum(quantity) from cart_article
                    where cart_id = i.cart_id) "num_articles"
            from invoice i
            inner join cart c on i.cart_id = c.id
            where c.id = :id;
            END,
            ["id" => $id]
        )->fetch(PDO::FETCH_ASSOC);

        $priceNoTax = 0;
        foreach ($articles as $article) {
            $priceNoTax += $article["price_no_tax"];
        }

        $this->sendOK([
            "articles" => $articles,
            "invoice_id" => $invoice["id"],
            "num_articles" => $invoice["num_articles"],
            "price_no_tax" => $priceNoTax,
            "price_tax" => $priceNoTax * 1.2
        ]);
    }
}

$handler = new PreviousCartHandler();
$handler->handle();
