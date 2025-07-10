<?php

namespace Source\App\Models;

use CoffeeCode\DataLayer\DataLayer;

class Grupos extends DataLayer
{
    public function __construct()
    {
        parent::__construct("grupos", ["nome", "ativo"], "id", false);
    }
}
