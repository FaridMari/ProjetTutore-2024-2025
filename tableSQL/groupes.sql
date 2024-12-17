create or replace table groupes
(
    id_groupe  int auto_increment
        primary key,
    nom_groupe varchar(255) not null,
    niveau     varchar(255) not null
);

