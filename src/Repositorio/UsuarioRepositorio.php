<?php

namespace Repositorio;

use Exception;
use Models\Endereco;
use Models\Usuario;
use PDO;

class UsuarioRepositorio extends Repositorio {
    
    public function __construct(PDO $conexaoBancoDados)
    {
        parent::__construct($conexaoBancoDados);
    }

    // cadastrar usuário na base de dados
    public function cadastrar($usuarioCadastrar) {
        $stmt = $this->bancoDados->prepare("INSERT INTO tb_usuarios(nome_completo, email, senha, status, tipo_usuario)
        VALUES(:nome_completo, :email, :senha, :status, :tipo_usuario)");

        $stmt->bindValue(":nome_completo", $usuarioCadastrar->getNomeCompleto());
        $stmt->bindValue(":email", $usuarioCadastrar->getEmail());
        $stmt->bindValue(":senha", $usuarioCadastrar->getSenha());
        $stmt->bindValue(":status", $usuarioCadastrar->getStatus());
        $stmt->bindValue(":tipo_usuario", $usuarioCadastrar->getTipoUsuario());

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se cadastrar o usuário.");
        }

        $usuarioCadastrar->setUsuarioId($this->bancoDados->lastInsertId());
    }

    // salvar endereço do usuário
    public function salvarEndereco($enderecoUsuario) {

        if ($enderecoUsuario->getEnderecoId() == 0) {
            // cadastrar um endereço novo
            $stmt = $this->bancoDados->prepare("INSERT INTO tb_enderecos(cep, complemento, logradouro, cidade, bairro, estado, numero, usuario_id)
            VALUES(:cep, :complemento, :logradouro, :cidade, :bairro, :estado, :numero, :usuario_id)");
        } else {
            // editar
            $stmt = $this->bancoDados->prepare("UPDATE tb_enderecos SET cep = :cep, complemento = :complemento, logradouro = :logradouro, cidade = :cidade, bairro = :bairro, estado = :estado, numero = :numero, usuario_id = :usuario_id
            WHERE endereco_id = :endereco_id");

            $stmt->bindValue(":endereco_id", $enderecoUsuario->getEnderecoId());
        }

        $stmt->bindValue(":cep", $enderecoUsuario->getCep());
        $stmt->bindValue(":complemento", $enderecoUsuario->getComplemento());
        $stmt->bindValue(":logradouro", $enderecoUsuario->getLogradouro());
        $stmt->bindValue(":cidade", $enderecoUsuario->getCidade());
        $stmt->bindValue(":bairro", $enderecoUsuario->getBairro());
        $stmt->bindValue(":estado", $enderecoUsuario->getEstado());
        $stmt->bindValue(":numero", $enderecoUsuario->getNumero());
        $stmt->bindValue(":usuario_id", $enderecoUsuario->getUsuarioId());

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se salvar o endereço do usuário.");
        }

        if ($enderecoUsuario->getEnderecoId() === 0) {
            $enderecoUsuario->setEnderecoId($this->bancoDados->lastInsertId());
        }

    }

    // buscar usuário pelo e-mail e senha
    public function buscarPeloEmailSenha($email, $senha) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_usuarios WHERE email = :email AND senha = :senha");
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":senha", $senha);
        $stmt->execute();
        $usuarioArr = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($usuarioArr)) {

            return null;
        }

        $usuario = new Usuario();

        $usuario->setUsuarioId($usuarioArr["usuario_id"]);
        $usuario->setNomeCompleto($usuarioArr["nome_completo"]);
        $usuario->setEmail($usuarioArr["email"]);
        $usuario->setStatus($usuarioArr["status"]);
        $usuario->setTipoUsuario($usuarioArr["tipo_usuario"]);

        if ($usuario->getTipoUsuario() == "salão") {
            // buscar endereço do salão
            $usuario->setEndereco($this->buscarEndereco($usuario->getUsuarioId()));
        }

        return $usuario;
    }

    // buscar endereço do usuário(salão)
    private function buscarEndereco($idUsuario) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_enderecos WHERE usuario_id = :usuario_id");
        $stmt->bindValue(":usuario_id", $idUsuario);
        $stmt->execute();
        $enderecoArr = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($enderecoArr)) {

            return null;
        }

        $endereco = new Endereco();
        $endereco->setEnderecoId($enderecoArr["endereco_id"]);
        $endereco->setCep($enderecoArr["cep"]);
        $endereco->setComplemento($enderecoArr["complemento"]);
        $endereco->setLogradouro($enderecoArr["logradouro"]);
        $endereco->setCidade($enderecoArr["cidade"]);
        $endereco->setBairro($enderecoArr["bairro"]);
        $endereco->setEstado($enderecoArr["estado"]);
        $endereco->setNumero($enderecoArr["numero"]);
        $endereco->setUsuarioId($idUsuario);

        return $endereco;
    }

    // buscar usuário pelo e-mail
    public function buscarPeloEmail($email) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_usuarios WHERE email = :email");
        $stmt->bindValue(":email", $email);
        $stmt->execute();
        $usuarioArr = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($usuarioArr)) {

            return null;
        }

        $usuario = new Usuario();

        $usuario->setUsuarioId($usuarioArr["usuario_id"]);
        $usuario->setNomeCompleto($usuarioArr["nome_completo"]);
        $usuario->setEmail($usuarioArr["email"]);
        $usuario->setStatus($usuarioArr["status"]);
        $usuario->setTipoUsuario($usuarioArr["tipo_usuario"]);

        if ($usuario->getTipoUsuario() == "salão") {
            // buscar endereço do salão
            $usuario->setEndereco($this->buscarEndereco($usuario->getUsuarioId()));
        }

        return $usuario;
    }

}