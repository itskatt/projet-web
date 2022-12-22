<?php

require_once "../_common/handler.php";

class AdminStatsHandler extends AdminRequiredHandler 
{
    protected function handleGET(): void
    {
        echo "TODO"; // attendre d'avoir plus de données dans la BDD
    }
}

$handler = new AdminStatsHandler();
$handler->handle();
