<?php

require_once "../_common/handler.php";

class SingleArticleHandler extends PublicHandler
{
    protected function handleGET(): void
    {
        if (!isset($_GET["id"])) {
            $this->sendError(
                400,
                "Aucun ID renseigné pour trouver l'article."
            );
        }

        $conn = $this->getConnector();

        $id = $_GET["id"];

        $res = $conn->query(
            <<<END
            select a.id "article_id",
                a.name_ "article_name",
                description_ "description",
                rating,
                year,
                supplier_price,
                image_ "image",
                s.name_ "supplier_name",
                quantity
            from article a
                    inner join supplier s on a.supplier_id = s.id
                    inner join stock s2 on a.id = s2.article_id
            where a.id = :id;
            END,
            ["id" => $id]
        )->fetch(PDO::FETCH_ASSOC);

        if (!$res) {
            $this->sendError(
                404,
                "Cet article n'existe pas."
            );
        }

        $this->sendOK($res);
    }
}

$handler = new SingleArticleHandler();
$handler->handle();
