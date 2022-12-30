<?php

require_once "../_common/handler.php";

class AdminStatsHandler extends AdminRequiredHandler
{
    protected function handleGET(): void
    {
        $conn = $this->getConnector();

        $salesPerInvoices = $conn->query(
            <<<END
            select i.id,
                   count(ca.article_id) * ca.quantity "sales"
            from invoice i
            inner join cart c on i.cart_id = c.id
            inner join cart_article ca on c.id = ca.cart_id
            group by i.id;
            END
        )->fetchAll(PDO::FETCH_ASSOC);

        $turnover = $conn->query(
            <<<END
            select sum(a.supplier_price * ca.quantity * 0.08)
            from invoice i
            inner join cart c on i.cart_id = c.id
            inner join cart_article ca on c.id = ca.cart_id
            inner join article a on ca.article_id = a.id;
            END
        )->fetch(PDO::FETCH_NUM)[0];

        $mostSoldArticlesAndTurnover = $conn->query(
            <<<END
            select ca.article_id,
                sum(ca.quantity) as total_quantity,
                a.supplier_price * 0.08 * sum(ca.quantity) turnover
            from invoice i
            inner join cart c on i.cart_id = c.id
            inner join cart_article ca on c.id = ca.cart_id
            inner join article a on ca.article_id = a.id
            group by ca.article_id
            order by total_quantity desc;
            END
        )->fetchAll(PDO::FETCH_ASSOC);

        $highestStocks = $conn->query(
            <<<END
            select name_ name, article_id, quantity from stock
            inner join article a on stock.article_id = a.id
            order by quantity desc
            limit 10;
            END
        )->fetchAll(PDO::FETCH_ASSOC);

        $numArticlesPerSupplier = $conn->query(
            <<<END
            select s.name_ "supplier_name",
                count(*) "num_articles"
            from article
            inner join supplier s on article.supplier_id = s.id
            group by s.id;
            END
        )->fetchAll(PDO::FETCH_ASSOC);

        $this->sendOK([
            "sales_per_invoice" => $salesPerInvoices,
            "turnover" => (float) $turnover,
            "most_sold_articles" => $mostSoldArticlesAndTurnover, // TODO peut être ajouter les noms d'articles
            "highest_stocks" => $highestStocks,
            "num_articles_per_supplier" => $numArticlesPerSupplier
        ]);
    }
}

$handler = new AdminStatsHandler();
$handler->handle();
