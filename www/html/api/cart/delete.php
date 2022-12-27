<?php

require_once "../_common/handler.php";

class DeleteCartHandler extends LoginRequiredHandler 
{
    protected function handleDELETE(): void
    {
        $currentCartId = $this->getCurrentCartId();

        $this->getConnector()->query("delete from cart where id = $currentCartId;");

        $this->sendOK([]);
    }
}

$handler = new DeleteCartHandler();
$handler->handle();