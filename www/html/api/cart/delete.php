<?php

require_once "../_common/handler.php";

class DeleteCartHandler extends LoginRequiredHandler
{
    protected function handleDELETE(): void
    {
        $currentCartId = $this->getCurrentCartId();

        if (!$currentCartId) {
            // Si il n'y a pas de panier à supprimer, on dit
            // quand même qu'il n'y en a plus
            $this->sendOK([]);
        }

        $this->getConnector()->query("delete from cart where id = $currentCartId;");

        $this->sendOK([]);
    }
}

$handler = new DeleteCartHandler();
$handler->handle();
