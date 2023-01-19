<?php

require_once "../_common/handler.php";

const IMAGE_HANDLERS = [
    IMAGETYPE_JPEG => [
        'load' => 'imagecreatefromjpeg',
        'save' => 'imagejpeg',
        'quality' => 95
    ],
    IMAGETYPE_PNG => [
        'load' => 'imagecreatefrompng',
        'save' => 'imagepng',
        'quality' => 0
    ]
];

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
        if ($object !== strval((int) $object)) {
            $this->sendError(
                400,
                "Le paramètre '$name' n'est pas un nombre !"
            );
        }

        return (int) $object;
    }

    /**
     * Compresse une image pour qu'elle ai les dimentions souhaitée.
     * Source : https://pqina.nl/blog/creating-thumbnails-with-php/
     */
    private function createThumbnail(string $src, string $dest, int $targetWidth, ?int $targetHeight = null)
    {
        $type = exif_imagetype($src);

        if (!$type || !IMAGE_HANDLERS[$type]) {
            return null;
        }

        $image = call_user_func(IMAGE_HANDLERS[$type]['load'], $src);

        if (!$image) {
            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        if ($targetHeight == null) {
            $ratio = $width / $height;

            if ($width > $height) {
                $targetHeight = floor($targetWidth / $ratio);
            } else {
                $targetHeight = $targetWidth;
                $targetWidth = floor($targetWidth * $ratio);
            }
        }

        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {
            imagecolortransparent(
                $thumbnail,
                imagecolorallocate($thumbnail, 0, 0, 0)
            );

            if ($type == IMAGETYPE_PNG) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }
        }

        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        return call_user_func(IMAGE_HANDLERS[$type]['save'], $thumbnail, $dest, IMAGE_HANDLERS[$type]['quality']);
    }

    /**
     * Sauvegarde et traite l'image qui a été envoyé sur cette route et lui attribue un id.
     * Renvoie null si aucune image n'est envoyée.
     */
    private function processImage(): ?string
    {
        if (!isset($_FILES["upl_img"])) {
            // Aucune image n'a été envoyé
            return null;
        }

        if ($_FILES["upl_img"]["error"] !== UPLOAD_ERR_OK) {
            // Erreur lors de l'envoie de l'image
            return null;
        }

        $fileName = $_FILES["upl_img"]['name'];
        $fileTmpName  = $_FILES["upl_img"]['tmp_name'];
        $fileType = $_FILES["upl_img"]['type'];

        $boom = explode('.', $fileName);
        $fileExtension = strtolower(end($boom));

        if (
            !in_array($fileExtension, ["png", "jpeg", "jpg"]) or
            !in_array($fileType, ['image/jpeg', 'image/jpg', 'image/png']) or
            !in_array(getimagesize($fileTmpName)["mime"], ['image/jpeg', 'image/jpg', 'image/png'])
        ) {
            // On autorise que les jpeg et les png
            $this->sendError(
                400,
                "Format d'image invalide"
            );
            return null;
        }

        $destDir = realpath(__DIR__ . "/../../../uploads");
        if (!is_dir($destDir)) {
            $this->sendError(500, "Merci de créer le dossier d'images...");
        }

        $imageId = uniqid() . "." . $fileExtension;

        $tempDest = $destDir . "/TMP-" . $imageId;
        if (!move_uploaded_file($fileTmpName, $tempDest)) {
            $this->sendError(
                500,
                "Impossible de deplacer l'image, verifiez les permissions du dossier hôte."
            );
            return null;
        }

        // Ensuite on redimentionne l'image dans le format souhaité
        $res = $this->createThumbnail($tempDest, $destDir . "/" . $imageId, 400);

        // On supprime l'image non-redimentionée
        unlink($tempDest);

        if ($res != null) {
            return $imageId;
        } else {
            // Si la redimention a eu un problème, il n'y aura pas d'image...
            return null;
        }
    }

    protected function handlePOST(?array $data): void
    {
        $data = $_POST; // pour pouvoir accepter l'image, on remplace le json par du multipart/form-data

        $this->checkFields(
            [
                "article_name", "supplier_name", "description",
                "rating", "year", "supplier_price", "quantity"
            ],
            $data
        );

        // L'anti-XSS se fait coté client avec angular
        $articleName = $data["article_name"];
        $supplierName = $data["supplier_name"];
        $description = $data["description"];

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
        if ($supplierPrice !== strval(floatval($supplierPrice))) {
            $this->sendError(
                400,
                "Le prix de l'article n'est pas un nombre valide !"
            );
        }
        $supplierPrice = (float) $supplierPrice;

        $image = $this->processImage();

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
            $supplierId = $conn->lastInsertId();
        }

        // On peut enfin créer l'article...
        $conn->query(
            <<<END
            insert into article (supplier_id, name_, description_, rating, year, supplier_price, image_)
            values ($supplierId, :name, :desc, $rating, $year, $supplierPrice, '$image');
            END,
            [
                "name" => $articleName,
                "desc" => $description
            ]
        );

        $articleId = $conn->lastInsertId();

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

        if ($quantity < 0) {
            $this->sendError(
                400,
                "Le stock d'articles ne peut pas être négatif."
            );
        }

        $conn = $this->getConnector();
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
            update stock
            set quantity = -1
            where article_id = :id;
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
