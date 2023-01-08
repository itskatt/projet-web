<?php

require_once "../_common/handler.php";

/**
 * Donne des informations sur le panier courant.
 */
class CurrentCartHandler extends LoginRequiredHandler
{
    protected function handleGET(): void
    {
        $currentCartId = $this->getCurrentCartId();

        if (!$currentCartId) {
            $this->sendError(
                404,
                "Il n'y a pas de panier actuelement modifiable, veillez en créer un."
            );
        }

        $conn = $this->getConnector();
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
                    s2.quantity "stock_quantity",
                    ca.quantity "cart_quantity",
                    ca.quantity * supplier_price * 1.08 "price_no_tax",
                    ca.quantity * supplier_price * 1.08 * 1.2 "price_tax"
            from article a
                    inner join supplier s on a.supplier_id = s.id
                    inner join stock s2 on a.id = s2.article_id
                    inner join cart_article ca on a.id = ca.article_id
            where cart_id = $currentCartId;
            END
        )->fetchAll(PDO::FETCH_ASSOC);

        $priceNoTax = 0;
        foreach ($cart as $article) {
            $priceNoTax += $article["price_no_tax"];
        }

        $this->sendOK([
            "cart_id" => $currentCartId,
            "articles" => $cart,
            "price_no_tax" => $priceNoTax,
            "price_tax" => $priceNoTax * 1.2
        ]);
    }
}

$handler = new CurrentCartHandler();
$handler->handle();
