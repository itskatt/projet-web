<?php

require_once "../_common/handler.php";

class MultipleArticleHandler extends PublicHandler
{
    protected function handleGET(): void
    {
        if (isset($_GET["random"])) {
            $order = "order by rand()";
        } else {
            $order = "order by a.id";
        }

        $conn = $this->getConnector();

        $res = $conn->query(
            <<<END
            select a.id "article_id",
                a.name_ "article_name",
                description_ "description",
                rating,
                year,
                round(supplier_price * 1.08 * 1.2, 2) "price_tax",
                image_ "image",
                s.name_ "supplier_name",
                quantity
            from article a
                    inner join supplier s on a.supplier_id = s.id
                    inner join stock s2 on a.id = s2.article_id
            where s2.quantity >= 0
            $order;
            END,
        )->fetchAll(PDO::FETCH_ASSOC);

        $this->sendOK([
            "articles" => array_values($res)
        ]);
    }
}

$handler = new MultipleArticleHandler();
$handler->handle();
