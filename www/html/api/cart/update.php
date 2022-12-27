<?php

require_once "../_common/handler.php";

class UpdateCartHandler extends LoginRequiredHandler
{
    protected function handlePUT(array $data): void
    {
        // On recupère le panier actuel
        $currentCardId =  $this->getCurrentCartId();

        if (!$currentCardId) {
            $this->sendError(
                400,
                "Il n'y a pas de panier actuelement modifiable, veillez en créer un."
            );
        }

        $conn = $this->getConnector();

        // On récupère les articles du panier actuel
        $cart = $conn->query(
            <<<END
            select article_id, quantity
            from cart_article
            where cart_id = $currentCardId;
            END
        )->fetchAll(PDO::FETCH_ASSOC);

        // Application des modifications sur le panier courrant
        $toModify = [];
        foreach ($data as $newArticle) {
            $found = false;

            // Si il est déja dans le panier, on change sa valeur
            foreach ($cart as $currentArticle) {
                if ($currentArticle["article_id"] == $newArticle["article_id"]) {
                    $currentArticle["quantity"] = $newArticle["quantity"];
                    $toModify[] = $currentArticle;
                    $found = true;
                }
            }

            // Pour les articles qui ne sont pas déjà dans le panier, on les ajoute
            if (!$found) {
                $toModify[] = $newArticle;
            }
        }

        // On peut maintenant rentrer les données dans la BDD

        $conn->beginTransaction();

        try {
            foreach ($toModify as $articleToModify) {
                // Si on veut supprimer l'article
                if ($articleToModify["quantity"] <= 0) {
                    $conn->query(
                        <<<END
                        delete from cart_article
                            where cart_id = :cid and article_id = :aid;
                        END,
                        [
                            "cid" => $currentCardId,
                            "aid" => $articleToModify["article_id"]
                        ]
                    );
                    continue;
                }

                // Est-ce que on ajoute pas trop d'articles ?
                $stock = $conn->query(
                    <<<END
                    select quantity
                        from stock
                        where article_id = :aid;
                    END,
                    [
                        "aid" => $articleToModify["article_id"]
                    ]
                )->fetch(PDO::FETCH_NUM)[0];

                if (!$stock) {
                    throw new Exception("Cet article n'existe pas ({$articleToModify['article_id']}).");
                }

                if ($articleToModify["quantity"] > $stock) {
                    throw new Exception("Stock insufisant pour l'article ({$articleToModify['quantity']} > $stock).");
                }

                // Est-ce qu'il faut utiliser un insert ou un update ?
                $res = $conn->query(
                    <<<END
                    select count(*) from cart_article
                        where cart_id = :cid and article_id = :aid;
                    END,
                    [
                        "cid" => $currentCardId,
                        "aid" => $articleToModify["article_id"]
                    ]
                )->fetch(PDO::FETCH_NUM)[0];

                if ($res == 0) {
                    //insert
                    $conn->query(
                        <<<END
                        insert into cart_article (cart_id, article_id, quantity)
                            values (:cid, :aid, :qty);
                        END,
                        [
                            "cid" => $currentCardId,
                            "aid" => $articleToModify["article_id"],
                            "qty" => $articleToModify["quantity"]
                        ]
                    );
                } else {
                    // update
                    $conn->query(
                        <<<END
                        update cart_article
                            set quantity = :qty
                            where cart_id = :cid and article_id = :aid;
                        END,
                        [
                            "cid" => $currentCardId,
                            "aid" => $articleToModify["article_id"],
                            "qty" => $articleToModify["quantity"]
                        ]
                    );
                }
            }
        } catch (Exception $e) {
            $conn->rollBack();
            $this->sendError(
                400,
                $e->getMessage()
            );
        }

        $newCart = $conn->query(
            <<<END
            select article_id, quantity
            from cart_article
            where cart_id = $currentCardId;
            END
        )->fetchAll(PDO::FETCH_ASSOC);

        // Si on est la, tout s'est bien passé
        $conn->commit();
        $this->sendOK([
            "cart" => $newCart
        ]);
    }
}

$handler = new UpdateCartHandler();
$handler->handle();
