<?php

require_once "../_common/handler.php";

/**
 * Effectue la commande du panier courrant.
 */
class OrderCartHandler extends LoginRequiredHandler
{
    private function sendEmail(string $email, int $invoiceId, string $body): void
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.sendgrid.com/v3/mail/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                "personalizations" => [["to" => [["email" => $email]]]],
                "from" => [
                    "email" => "no-reply@tel.extracursus.live"
                ],
                "subject" => "Confirmation de votre commande n°$invoiceId",
                "content" => [
                    [
                        "type" => "text/plain",
                        "value" => $body
                    ]
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . EMAIL_API_KEY,
                "Content-Type: application/json"
            ],
        ]);
        curl_exec($curl);
        curl_close($curl);
    }

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

        // On envoie le mail
        $invoiceArticles = $conn->query(
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
            where i.id = $invoiceId;
            END
        )->fetchAll(PDO::FETCH_ASSOC);

        $body = "Voici le contenu de votre commande :\n";
        foreach ($invoiceArticles as $article) {
            $body .= "    * " . $article["article_name"] . " - x" . $article["cart_quantity"] . " : " . $article["price_tax"] . " €\n";
        }

        $body .= "\nMerci d'avoir commandé chez TelTech !";

        $this->sendEmail($this->email, $invoiceId, $body);

        $this->sendOK([
            "invoice_id" => $invoiceId
        ]);
    }
}

$handler = new OrderCartHandler();
$handler->handle();
