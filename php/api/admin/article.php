<?php

require_once "../_common/handler.php";

/**
 * Opérations d'administration sur les articles.
 */
class AdminArticleHandler extends AdminRequiredHandler
{
    /**
     * Verifie si un élément contenu dans un tableau est un entier valide.
     */
    private function validateInt(array $data, string $name): int
    {
        $object = $data[$name];
        if (!is_int($object)) {
            $this->sendError(
                400,
                "Le paramètre '$name' n'est pas un nombre !"
            );
        }

        return (int) $object;
    }

    protected function handlePOST(array $data): void
    {
        // TODO ? image

        $this->checkFields(
            [
                "article_name", "supplier_name", "description",
                "rating", "year", "supplier_price", "quantity"
            ],
            $data
        );

        $articleName = htmlspecialchars($data["article_name"]);
        $supplierName = htmlspecialchars($data["supplier_name"]);
        $description = htmlspecialchars($data["description"]);

        $rating = $this->validateInt($data, "rating");

        if ($rating > 5 or $rating < 0) {
            $this->sendError(
                400,
                "Le paramètre rating n'est pas compris entre 0 et 5 !"
            );
        }

        $year = $this->validateInt($data, "year");

        $quantity = $this->validateInt($data, "quantity");

        $supplierPrice = $data["supplier_price"];
        if (!(is_int($supplierPrice) or is_float($supplierPrice))) {
            $this->sendError(
                400,
                "Le prix de l'article n'est pas un nombre valide !"
            );
        }
        $supplierPrice = (float) $supplierPrice;

        $conn = $this->getConnector();
        // Premièrement, on s'occupe du fournisseur
        $supplier = $conn->query(
            <<<END
            select id, name_ "name"
            from supplier where upper(name_) = upper(:name);
            END,
            ["name" => $supplierName]
        )->fetch(PDO::FETCH_ASSOC);

        $supplierId = $supplier["id"];

        if (!$supplier) {
            // Si il n'existe pas, on le crée
            $conn->query(
                <<<END
                insert into supplier (name_)
                values (:name);
                END,
                ["name" => $supplierName]
            );

            // On récupère l'id du fournisseur tout juste crée
            $supplierId = $conn->query(
                <<<END
                select auto_increment - 1 "id"
                from information_schema.TABLES
                where TABLE_SCHEMA = database() and TABLE_NAME = 'supplier';
                END
            )->fetch(PDO::FETCH_NUM)[0];
        }

        // On peut enfin créer l'article...
        $conn->query(
            <<<END
            insert into article (supplier_id, name_, description_, rating, year, supplier_price)
            values ($supplierId, :name, :desc, $rating, $year, $supplierPrice);
            END,
            [
                "name" => $articleName,
                "desc" => $description
            ]
        );

        $articleId = $conn->query(
            <<<END
            select auto_increment - 1 "id"
            from information_schema.TABLES
            where TABLE_SCHEMA = database() and TABLE_NAME = 'article';
            END
        )->fetch(PDO::FETCH_NUM)[0];

        // ...et le stock en question
        $conn->query(
            <<<END
            insert into stock (article_id, quantity)
            values ($articleId, $quantity);
            END
        );

        $this->sendOK([
            "article_id" => $articleId
        ]);
    }

    protected function handlePUT(array $data): void
    {
        $this->checkFields(
            ["article_id", "quantity"],
            $data
        );

        $articleId = $this->validateInt($data, "article_id");

        $quantity = $this->validateInt($data, "quantity");

        $conn = $this->getConnector();
        // TODO : aussi ajuster cart_article si nouveau stock < cart_article.quantity
        $conn->query(
            <<<END
            update stock
            set quantity = $quantity
            where article_id = $articleId;
            END
        );

        $this->sendOK([]);
    }

    protected function handleDELETE(): void
    {
        if (!isset($_GET["id"])) {
            $this->sendError(
                400,
                "Aucun ID renseigné pour supprimer l'article."
            );
        }

        $conn = $this->getConnector();
        $conn->query(
            <<<END
            delete from article
            where id = :id;
            END,
            [
                "id" => $_GET["id"]
            ]
        );

        $this->sendOK([]);
    }
}

$handler = new AdminArticleHandler();
$handler->handle();
