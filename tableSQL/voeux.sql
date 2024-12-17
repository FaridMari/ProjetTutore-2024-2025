create or replace table voeux
(
    id_voeu       int auto_increment
        primary key,
    id_enseignant int          not null,
    id_cours      int          not null,
    id_groupe     int          not null,
    semestre      varchar(255) not null,
    nb_heures     double       not null,
    constraint voeux_ibfk_1
        foreign key (id_enseignant) references enseignants (id_enseignant),
    constraint voeux_ibfk_2
        foreign key (id_cours) references cours (id_cours),
    constraint voeux_ibfk_3
        foreign key (id_groupe) references groupes (id_groupe)
);

create or replace index id_cours
    on voeux (id_cours);

create or replace index id_enseignant
    on voeux (id_enseignant);

create or replace index id_groupe
    on voeux (id_groupe);

