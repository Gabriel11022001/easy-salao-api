create table tb_usuarios(
    usuario_id serial primary key,
    nome_completo text not null,
    email text not null,
    senha text not null, 
    status text not null,
    tipo_usuario text not null
);