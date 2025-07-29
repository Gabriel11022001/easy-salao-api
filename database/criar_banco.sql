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

create table tb_horarios(
    horario_id serial primary key,
    ano integer not null,
    mes text not null,
    dia integer not null,
    horario_de text not null,
    horario_ate text not null,
    reservado boolean not null default false,
    usuario_salao_id integer not null,
    foreign key(usuario_salao_id) references tb_usuarios(usuario_id)
);

create table tb_reservas(
    reserva_id serial primary key,
    usuario_id integer not null,
    usuario_salao_id integer not null,
    horario_salao_id integer not null,
    valor_total decimal not null,
    foreign key(usuario_id) references tb_usuarios(usuario_id),
    foreign key(usuario_salao_id) references tb_usuarios(usuario_id),
    foreign key(horario_salao_id) references tb_horarios(horario_id)
);

create table tb_reserva_servico(
    servico_id integer not null,
    reserva_id integer not null,
    preco_servico_momento_reserva decimal not null,
    foreign key(servico_id) references tb_servicos_salao(servico_salao_id),
    foreign key(reserva_id) references tb_reservas(reserva_id)
);