<?php

require __DIR__ . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";


use CoffeeCode\Router\Router;

$router = new Router(urlBase());
$router->namespace("Source\App\Controllers");

/*
 * WEB
* Não precisa estar logado para acessar
*
*/

$router->group(null);
// $router->get("/", "WebController:index");
$router->get("/", "WebController:login");
$router->post("/", "AuthWebController:login");
// $router->get("/index", "AuthWebController:index");
$router->get("/negado", "WebController:negado");


/*
 * ADMIN
 * Necessário estar logado para acessar
 *
*/

$router->group("admin");
$router->get("/", "AdminController:index");
$router->get("/admin12", "AdminController:index123");
$router->get("/logout", ("AuthWebController:logout"));

$router->group("admin/grupos");

$router->get("/", "GroupController:index");
$router->get("/{id}", "GroupController:open");
$router->get("/create", "GroupController:create");
$router->post("/", "GroupController:save");
$router->post("/{id}/delete", "GroupController:destroy");


/*
 * Produtos
 */

$router->group("admin/produtos");

$router->get("/", "ProductController:index");
$router->get("/{id}", "ProductController:update");
$router->get("/create", "ProductController:create");
$router->post("/", "ProductController:save");
$router->post("/{id}", "ProductController:destroy");

/*
* Importar CSV
*/
$router->group("admin/excel");

$router->get("/", "ImportController:index");
$router->post("/importar", "ImportController:processar");
$router->get("/processar", "ImportController:processar");


/*
* Configurações
*/

$router->group("admin/configuracoes");
$router->get("/", "ConfigController:index");

/*
* Contato
*/

$router->group("admin/contato");
$router->get("/", "ContactController:index");
$router->post("/", "ContactController:index");


/*
 * Tabela de Preços
 */
$router->group("admin/tabela-precos");
$router->get("/", "TableController:index");

/*
 * ERROR
 */

$router->group("ops");
$router->get("/{errcode}", "WebController:error");


/**
 * PROCESS
 */

$router->dispatch();

if ($router->error()) {
    $router->redirect("/ops/{$router->error()}");
}
