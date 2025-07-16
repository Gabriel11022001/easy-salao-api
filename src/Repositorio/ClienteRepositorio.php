<?php

namespace Repositorio;

use Exception;
use Models\Cliente;
use PDO;

class ClienteRepositorio extends Repositorio {

    public function __construct(PDO $bancoDados)
    {
        parent::__construct($bancoDados);
    }

    // cadastrar cliente
    public function cadastrar($clienteCadastrar) {
        $stmt = $this->bancoDados->prepare("INSERT INTO tb_clientes(nome_completo, email, telefone, cliente_vip, usuario_salao_id, usuario_id)
        VALUES(:nome_completo, :email, :telefone, :cliente_vip, :usuario_salao_id, :usuario_id)");

        $stmt->bindValue(":nome_completo", $clienteCadastrar->getNomeCompleto());
        $stmt->bindValue(":telefone", $clienteCadastrar->getTelefone());
        $stmt->bindValue(":email", $clienteCadastrar->getEmail());
        $stmt->bindValue(":cliente_vip", $clienteCadastrar->getClienteEhVip(), PDO::PARAM_BOOL);
        $stmt->bindValue(":usuario_salao_id", $clienteCadastrar->getUsuarioSalaoId());
        $stmt->bindValue(":usuario_id", $clienteCadastrar->getUsuarioId());

        if ($stmt->execute()) {
            $clienteCadastrar->setClienteId($this->bancoDados->lastInsertId());
        } else {

            throw new Exception("Erro ao tentar-se cadastrar o cliente na base de dados.");
        }

    }

    // buscar cliente pelo e-mail e pelo usuario_salao_id
    public function buscarClientePeloEmailEIdUsuarioSalao($email, $idUsuarioSalao) {
        $query = "SELECT * FROM tb_clientes WHERE email = :email AND usuario_salao_id = :usuario_salao_id";

        $stmt = $this->bancoDados->prepare($query);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":usuario_salao_id", $idUsuarioSalao);
        $stmt->execute();
        $clienteArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($clienteArray)) {

            return null;
        }

        $cliente = new Cliente();
        $cliente->setClienteId($clienteArray["cliente_id"]);
        $cliente->setNomeCompleto($clienteArray["nome_completo"]);
        $cliente->setTelefone($clienteArray["telefone"]);
        $cliente->setEmail($clienteArray["email"]);
        $cliente->setClienteEhVip($clienteArray["cliente_vip"]);
        $cliente->setUsuarioSalaoId($clienteArray["usuario_salao_id"]);
        $cliente->setUsuarioId($clienteArray["usuario_id"]);

        return $cliente;
    }

    // buscar os clientes do usuário representante do salão
    public function buscarClientesRepresentanteSalao($idRepresentanteSalao) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_clientes WHERE usuario_salao_id = :usuario_salao_id");
        $stmt->bindValue(":usuario_salao_id", $idRepresentanteSalao);
        $stmt->execute();

        $clientesArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $clientes = array();

        foreach ($clientesArray as $clienteArray) {
            $cliente = new Cliente();

            $cliente->setClienteId($clienteArray["cliente_id"]);
            $cliente->setNomeCompleto($clienteArray["nome_completo"]);
            $cliente->setEmail($clienteArray["email"]);
            $cliente->setTelefone($clienteArray["telefone"]);
            $cliente->setClienteEhVip($clienteArray["cliente_vip"]);
            $cliente->setUsuarioId($clienteArray["usuario_id"]);
            $cliente->setUsuarioSalaoId($clienteArray["usuario_salao_id"]);

            $clientes[] = $cliente->toArray();
        }

        return $clientes;
    }

    // buscar cliente pelo id
    public function buscarPeloId($clienteId) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_clientes WHERE cliente_id = :cliente_id");

        $stmt->bindValue(":cliente_id", $clienteId);
        $stmt->execute();
        $clienteArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($clienteArray)) {

            return null;
        }

        $cliente = new Cliente();

        $cliente->setClienteId($clienteArray["cliente_id"]);
        $cliente->setNomeCompleto($clienteArray["nome_completo"]);
        $cliente->setTelefone($clienteArray["telefone"]);
        $cliente->setEmail($clienteArray["email"]);
        $cliente->setClienteEhVip($clienteArray["cliente_vip"]);
        $cliente->setUsuarioId($clienteArray["usuario_id"]);
        $cliente->setUsuarioSalaoId($clienteArray["usuario_salao_id"]);

        return $cliente;
    }

}