<?php

require_once "../_common/handler.php";

class MultipleArticleHandler extends PublicHandler
{
    protected function handleGET(): void
    {
        if (isset($_GET["page"])) {
            // Par défault on revoie la première page
            $page = 1;
        } else {
            $page = $_GET["page"];
        }

        $stop = $page * 20;
        $start = $stop - 20;

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
            order by a.id
            limit $start, $stop;
            END,
        )->fetchAll(PDO::FETCH_ASSOC);

        if (!$res) {
            $this->sendError(
                404,
                "Cette page n'existe pas."
            );
        }

        $this->sendOK([
            "articles" => array_values($res)
        ]);
    }
}

$handler = new MultipleArticleHandler();
$handler->handle();
