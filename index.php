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
$router->get("/index", "AdminController:index");
$router->get("/cadastro", "AdminController:cadastro");
$router->get("/empresa", "EmpresaController:cadastro_empresa");

$router->get("/logout", ("AuthWebController:logout"));

/*
 * Grupos
 */

$router->get("/grupos", "GroupController:index");
$router->get("/grupos/{id}", "GroupController:open");
$router->get("/grupos/create", "GroupController:create");
$router->post("/grupos", "GroupController:save");
$router->post("/grupos/{id}", "GroupController:destroy");

/*
 * Produtos
 */

$router->get("/produtos", "ProductController:index");
$router->get("/produtos/{id}", "ProductController:update");
$router->get("/produtos/create", "ProductController:create");
$router->post("/produtos", "ProductController:save");
$router->post("/produtos/{id}", "ProductController:destroy");







$router->group("ops");
$router->get("/{errcode}", "WebController:error");

/**
 * PROCESS
 */

$router->dispatch();

if ($router->error()) {
    $router->redirect("/ops/{$router->error()}");
}
