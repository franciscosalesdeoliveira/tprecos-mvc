<?php

namespace Source\App\Controllers;

use Exception;
use PDO;
// use Source\App\helpers\Funcoes;
use Source\App\Models\Grupos;
use Source\App\Models\GruposSQL;

class GroupController extends BaseControllerAdmin
{
    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {

        echo $this->view->render("grupos/index", [
            "title" => "Administra&ccedil;&atilde;o - ProjetoSecao",
            "dataDB" => GruposSQL::categoriesTreeOrder(),
        ]);
    }


    public function create(): void
    {
        echo $this->view->render("grupos/form", [

            "title" => "Administra&ccedil;&atilde;o - ProjetoSecao",
            "crumb" => "<a href='" . url("admin/grupos/create") . "'>Adicionar</a>",
            "projetos" => (new Grupos())->find()->fetch(true),
            // "secoes" => (new Grupos())->find('id is null')->fetch(true),
            "grupos" => (new Grupos())->find()->fetch(true),
        ]);
    }

    public function open($data): void
    {
        //UPDATE 
        // Verifica se o ID foi passado
        if (!isset($data['id']) || empty($data['id']) || !is_numeric($data['id'])) {
            $_SESSION["messageError"] = "ID do grupo não informado ou inválido.";
            header('Location: ' . url("admin/grupos"));
            return;
        }
        // Busca o grupo pelo ID
        $dataDB = (new Grupos())->findById((int) $data['id']);

        // Verifica se o grupo foi encontrado
        if (!$dataDB) {
            $_SESSION["messageError"] = "Grupo n&atilde;o encontrado.";
            header('Location: ' . url("admin/grupos"));
            return;
        }
        echo $this->view->render("grupos/form", [
            "title" => "Administra&ccedil;&atilde;o - ProjetoSecao",
            "crumb" => "<a href='" . url("admin/grupos/" . $data['id']) . "'>Atualizar</a>",
            "projetos" => (new Grupos())->find()->fetch(true),
            "dataDB" => $dataDB
        ]);
    }

    public function save($data): void
    {
        try {
            if ($data["id"])
                $dataDB = (new Grupos())->findById($data["id"]);
            else
                $dataDB = new Grupos();

            // $dataDB->updated_by = $this->grupos->id;
            // $dataDB->updated_at = Funcoes::dataHoraMysql();

            $dataDB->nome = $data["nome"];

            $dataDB->ativo = $data["ativo"];

            $inicio = false;

            if ($dataDB->save()) {
                $_SESSION["messageSuccess"] = "Registro salvo com sucesso.";
                $inicio = true;
            } else
                $_SESSION["messageError"] = "N&atilde;o foi poss&iacute;vel salvar o registro. {$dataDB->fail}";
        } catch (Exception $exception) {
            $_SESSION["messageError"] = "Erro ao processar requisi&ccedil;&atilde;o : " . $exception->getMessage();
        }

        if ($inicio)
            header('Location: ' . url("admin/grupos"));
        else
            $this->retorno($data);
    }


    private function retorno(array $data)
    {
        if ($data["id"]) {
            $this->open($data);
        } else {
            $this->create();
        }

        die();
    }

    public function destroy($data): void
    {
       
        $dataDB = (new Grupos())->findById($data['id']);

        if ($dataDB == null) {
            $_SESSION["messageError"] = "Registro não localizado.";
        } else {
            if ($dataDB->destroy())
                $_SESSION["messageSuccess"] = "Exclu&iacute;do com sucesso!";
            else
                $_SESSION["messageError"] = "Erro excluindo. {$dataDB->fail}";
        }

        header('Location: ' . url("admin/grupos"));
    }
}
