<?php

namespace Source\App\Models;

use CoffeeCode\DataLayer\DataLayer;

class Produtos extends DataLayer
{
       public function __construct()
    {
        parent::__construct("produtos", ["nome", "grupo_id", "preco", "ativo"], "id", false);
    }
}
