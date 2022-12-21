<?php

abstract class RouteHandler
{
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
     * Envoie le tableau passé en paramètre sous la forme d'un JSON.
     */
    protected function sendJSON(array $array): void
    {
        // Pour ne pas être géné par des trucs d'avant.
        ob_clean();
        // header_remove();

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
     * Gère la route en entier.
     */
    public function handle(): void
    {
        $this->check();

        // Quelle methode utiliser
        $method = $_SERVER["REQUEST_METHOD"];
        switch ($method) {
            case "PUT":
                $this->handlePUT();
                break;
            case "POST":
                $this->handlePOST();
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
    }

    /**
     * Gère une appel fait avec une méthode PUT.
     * Par default produit une erreur.
     */
    protected function handlePUT(): void
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
    protected function handlePOST(): void
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

    // sendOK
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
