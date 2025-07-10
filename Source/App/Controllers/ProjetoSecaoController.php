<?php

namespace Source\App\Controllers;

use Exception;
use Source\App\Models\ProjetoSecao;
use Source\App\Helpers\Funcoes;
use Source\App\Helpers\FunctionsSQL;
use Source\App\Models\Projeto;
use Source\App\Models\ProjetoSecaoSQL;
use Source\App\Models\SQL;

class ProjetoSecaoController extends ControllerBaseAdmin
{
  public function __construct($router)
  {
    parent::__construct($router);
    parent::Logado();
    parent::SemProjetoSelecionado();
  }

  public function index(): void
  {
   echo $this->view->render("projeto/secao/index", [
   "title" => "Administra&ccedil;&atilde;o - ProjetoSecao",
   "dataDB" => ProjetoSecaoSQL::categoriesTreeOrder(),
   ]);
 }

  public function create(): void
  {
    echo $this->view->render("projeto/secao/form", [
    "title" => "Administra&ccedil;&atilde;o - ProjetoSecao",
    "crumb" => "<a href='". url("admin/projetosecao/create") ."'>Adicionar</a>",
    "projetos" => (new Projeto())->find()->fetch(true),
    "secoes" => (new ProjetoSecao())->find('projeto_secaoid is null')->fetch(true),
    ]);
  }

  public function update($data): void
  {
    echo $this->view->render("projeto/secao/form", [
    "title" => "Administra&ccedil;&atilde;o - ProjetoSecao",
    "crumb" => "<a href='". url("admin/projetosecao/". $data['id']."/update") ."'>Atualizar</a>",
    "secoes" => (new ProjetoSecao())->find('projeto_secaoid is null')->fetch(true),
    "dataDB" => (new ProjetoSecao())->findById((int) $data['id']),
    ]);
  }

 public function save($data): void
 {
   try 
   {
    if ($data["id"])
      $dataDB = (new ProjetoSecao())->findById($data["id"]);
    else
      $dataDB = new ProjetoSecao();

    $dataDB->updated_by = $this->usuarioDados->email;
    $dataDB->updated_at = Funcoes::dataHoraMysql();

    $dataDB->nome = $data["nome"];
    $dataDB->projetoid = $data["projetoid"];
    $dataDB->projeto_secaoid = ($data["projeto_secaoid"] == "") ? null : $data["projeto_secaoid"];
    $dataDB->descricao = $data["descricao"];
    $dataDB->ordem = $data["ordem"];
    $dataDB->icone = $data["icone"];
    $dataDB->ativo = $data["ativo"];

    $inicio = false;

    if ($dataDB->save())
    {
      $_SESSION["messageSuccess"] = "Registro salvo com sucesso.";
      $inicio = true;
    }
    else
      $_SESSION["messageError"] = "N&atilde;o foi poss&iacute;vel salvar o registro. {$dataDB->fail}";

   } catch (Exception $exception) {
     $_SESSION["messageError"] = "Erro ao processar requisi&ccedil;&atilde;o : " . $exception->getMessage();
   }

    if ($inicio)
      header('Location: '. url("admin/projetosecao"));
    else
      $this->retorno($data);
 }

 private function retorno(array $data)
 {
   if ($data["id"]) {
     $this->update($data);
   } else {
     $this->create();
   }

   die();
 }

  public function destroy($data): void
  {
    $dataDB = (new ProjetoSecao())->findById($data['id']);

    if ($dataDB == null) {
      $_SESSION["messageError"] = "Registro não localizado.";
    } else {
      if ($dataDB->destroy())
        $_SESSION["messageSuccess"] = "Exclu&iacute;do com sucesso!";
      else
        $_SESSION["messageError"] = "Erro excluindo. {$dataDB->fail}";
    }

    header('Location: '. url("admin"));
  }

  public function destroySelected($data): void
  {
    if ($data == null) {
      $_SESSION["messageError"] = "Registro não localizado.";
    } else {
      try
      {
        $ids = Funcoes::ExtrairIds($data["checkSelected"]);
        $delete = (new FunctionsSQL())->executeSQL(SQL::deleteFromTable("projeto_secao", $ids));

        if ($delete)
          $_SESSION["messageSuccess"] = "Exclu&iacute;do com sucesso!";
        else
          $_SESSION["messageError"] = "Erro excluindo.";

      } catch (Exception $ex) {
        $_SESSION["messageError"] = "Erro excluindo. {$ex->getMessage()}";
      }
    }

    header('Location: '. url("admin/projetosecao"));
  }
}