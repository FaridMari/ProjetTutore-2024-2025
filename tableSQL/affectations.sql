create or replace table affectations
(
    id_affectation   int auto_increment
        primary key,
    id_enseignant    int          not null,
    id_cours         int          not null,
    id_groupe        int          not null,
    heures_affectees double       not null,
    type_heure       varchar(255) not null,
    constraint affectations_ibfk_1
        foreign key (id_enseignant) references enseignants (id_enseignant),
    constraint affectations_ibfk_2
        foreign key (id_cours) references cours (id_cours),
    constraint affectations_ibfk_3
        foreign key (id_groupe) references groupes (id_groupe)
);

create or replace index id_cours
    on affectations (id_cours);

create or replace index id_enseignant
    on affectations (id_enseignant);

create or replace index id_groupe
    on affectations (id_groupe);

