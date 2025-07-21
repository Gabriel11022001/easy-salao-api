create table tb_usuarios(
    usuario_id serial primary key,
    nome_completo text not null,
    email text not null,
    senha text not null, 
    status text not null,
    tipo_usuario text not null
);

create table tb_enderecos(
    endereco_id serial primary key,
    cep text not null,
    logradouro text not null,
    bairro text not null, 
    cidade text not null,
    numero text not null,
    estado text not null,
    complemento text,
    usuario_id integer not null,
    foreign key(usuario_id) references tb_usuarios(usuario_id)
);

create table tb_clientes(
    cliente_id serial primary key,
    nome_completo text not null,
    email text not null,
    telefone text, 
    cliente_vip boolean not null,
    usuario_salao_id integer,
    usuario_id integer,
    foreign key(usuario_salao_id) references tb_usuarios(usuario_id)
);

create table tb_servicos_salao(
    servico_salao_id serial primary key,
    nome_servico text not null,
    preco_servico decimal not null default 0,
    salao_fornece_servico boolean not null,
    usuario_salao_id integer not null,
    foreign key(usuario_salao_id) references tb_usuarios(usuario_id)
);