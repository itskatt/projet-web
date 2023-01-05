<?php

require_once "model.php";

/**
 * Classe de base pour la gestion de la logique d'une route REST.
 */
abstract class RouteHandler
{
    private ?DatabaseConnector $conn = null;

    /**
     * Renvoie le connecteur vers la base de données, le créant
     * au passage si il n'existe pas.
     */
    protected function getConnector(): DatabaseConnector
    {
        if ($this->conn === null) {
            try {
                $this->conn = new DatabaseConnector();
            } catch (PDOException $e) {
                $this->sendError(
                    500,
                    "Connexion à la base de données impossible ! ({$e->getMessage()})"
                );
            }
        }

        return $this->conn;
    }

    /**
     * Determine si l'utilisateur de la route REST est autorisé
     * a faire son action.
     * 
     * @return true Il est autorisé.
     * @return false Il ne l'est pas.
     */
    abstract protected function authorised(): bool;

    /**
     * Effectue les verifications si la route est utilisée correctement.
     */
    private function check(): void
    {
        if (!$this->authorised()) {
            $this->sendError(
                401,
                "Vous n'êtes pas autorisé a utiliser cette route."
            );
        }
    }

    /**
     * Verifie que les champs sont bien présent dans les données.
     */
    protected function checkFields($fields, $data): void
    {
        foreach ($fields as $field) {
            if (!isset($data[$field])) {
                $this->sendError(
                    400,
                    "Champ obligatoire manquant : $field"
                );
            }
        }
    }

    /**
     * Démare une session après qu'un utilisateur se soit connecté.
     */
    protected function startSession(string $email, bool $rememberMe = false): void
    {
        $token = bin2hex(random_bytes(25));

        if ($rememberMe) {
            // 2 jours
            $cookieTime = 60 * 60 * 24 * 2;
            $sessionTime = "2 0:0";
        } else {
            // 20 min
            $cookieTime = 60 * 20;
            $sessionTime = "0:20";
        }

        $conn = $this->getConnector();
        $conn->query(
            <<<END
            insert into session (client_email, token, expires)
            values (:email, :token, addtime(now(), '$sessionTime'));
            END,
            [
                "email" => $email,
                "token" => $token
            ]
        );

        // On nettoie les sessions expirées
        $conn->query("delete from session where expires < now();");

        setcookie("token", $token, time() + $cookieTime, "/");
    }

    /**
     * Envoie le tableau passé en paramètre sous la forme d'un JSON.
     */
    protected function sendJSON(array $array): void
    {
        // Pour ne pas être géné par des trucs d'avant.
        ob_clean();

        header("Content-type: application/json");
        echo json_encode($array);

        exit();
    }

    /**
     * Notifie d'une erreur.
     */
    protected function sendError(int $code, string $message): void
    {
        http_response_code($code);
        $this->sendJSON([
            "status" => "error",
            "message" => $message
        ]);
    }

    /**
     * Envoie une réponse JSON suite à un succées.
     */
    protected function sendOK(array $array): void
    {
        http_response_code(200);
        $this->sendJSON(array_merge(
            ["status" => "succes"],
            $array
        ));
    }

    /**
     * Gère la route en entier.
     */
    public function handle(): void
    {
        try {
            $this->check();

            // Quelle methode utiliser
            $method = $_SERVER["REQUEST_METHOD"];
            switch ($method) {
                case "PUT":
                    $request_body = file_get_contents("php://input");
                    $data = json_decode($request_body, true);
                    $this->handlePUT($data);
                    break;

                case "POST":
                    $request_body = file_get_contents("php://input");
                    $data = json_decode($request_body, true);
                    $this->handlePOST($data);
                    break;

                case "GET":
                    $this->handleGET();
                    break;

                case "DELETE":
                    $this->handleDELETE();
                    break;

                default:
                    // handle_error($request);
                    break;
            }
        } catch (Throwable $e) {
            $this->sendError(
                500,
                "Erreur (" . get_class($e) . ") lors de la gestion de la route : {$e->getMessage()}\n{$e->getTraceAsString()}"
            );
        }
    }

    /**
     * Gère une appel fait avec une méthode PUT.
     * Par default produit une erreur.
     */
    protected function handlePUT(array $data): void
    {
        $this->sendError(
            405,
            "Cette méthode http n'est pas supportée sur cette route."
        );
    }

    /**
     * Gère une appel fait avec une méthode POST.
     * Par default produit une erreur.
     */
    protected function handlePOST(array $data): void
    {
        $this->sendError(
            405,
            "Cette méthode http n'est pas supportée sur cette route."
        );
    }

    /**
     * Gère une appel fait avec une méthode GET.
     * Par default produit une erreur.
     */
    protected function handleGET(): void
    {
        $this->sendError(
            405,
            "Cette méthode http n'est pas supportée sur cette route."
        );
    }

    /**
     * Gère une appel fait avec une méthode DELETE.
     * Par default produit une erreur.
     */
    protected function handleDELETE(): void
    {
        $this->sendError(
            405,
            "Cette méthode http n'est pas supportée sur cette route."
        );
    }
}

/**
 * Routes sans autorisation.
 */
abstract class PublicHandler extends RouteHandler
{
    protected function authorised(): bool
    {
        return true;
    }
}

/**
 * Routes nécessitant qu'un utilisateur soit connecté pour être utilisées.
 */
abstract class LoginRequiredHandler extends RouteHandler
{
    /**
     * L'adresse email de la personne connectée.
     */
    protected string $email;

    /**
     * Renvoie l'Id du panier courant (non commandé).
     */
    protected function getCurrentCartId(): ?int
    {
        $conn = $this->getConnector();

        $currentCardId = $conn->query(
            <<<END
            select id from cart
            where id not in (
                select c.id from cart c
                inner join invoice i on c.id = i.cart_id
                where c.client_email = :email
            );
            END,
            ["email" => $this->email]
        )->fetch(PDO::FETCH_NUM)[0];

        return $currentCardId;
    }

    protected function authorised(): bool
    {
        if (!isset($_COOKIE["token"])) {
            return false;
        }

        $token = $_COOKIE["token"];

        $conn = $this->getConnector();
        $email = $conn->query(
            <<<END
            select client_email from session where expires > now() and token = :token
            order by expires desc;
            END,
            ["token" => $token]
        )->fetch(PDO::FETCH_NUM)[0];

        if (!$email) {
            return false;
        }

        $this->email = $email;
        return true;
    }
}

/**
 * Routes nécessitant qu'un admininistrateur soit connecté.
 */
abstract class AdminRequiredHandler extends LoginRequiredHandler
{
    protected function authorised(): bool
    {
        if (!parent::authorised()) {
            return false;
        }

        $token = $_COOKIE["token"];

        $conn = $this->getConnector();
        $admin = $conn->query(
            <<<END
            select admin_ from client c
            inner join session s on c.email = s.client_email
            where token = :token;
            END,
            ["token" => $token]
        )->fetch(PDO::FETCH_NUM)[0];

        return boolval($admin);
    }
}
