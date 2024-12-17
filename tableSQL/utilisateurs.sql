create or replace table utilisateurs
(
    id_utilisateur int auto_increment
        primary key,
    nom            varchar(255) not null,
    email          varchar(255) not null,
    mot_de_passe   varchar(255) not null,
    role           varchar(255) not null,
    constraint email
        unique (email)
);

