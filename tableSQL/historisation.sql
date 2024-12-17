create or replace table historisation
(
    id_historique int auto_increment
        primary key,
    id_enseignant int not null,
    id_cours      int not null,
    id_groupe     int not null,
    annee         int not null,
    constraint historisation_ibfk_1
        foreign key (id_enseignant) references enseignants (id_enseignant),
    constraint historisation_ibfk_2
        foreign key (id_cours) references cours (id_cours),
    constraint historisation_ibfk_3
        foreign key (id_groupe) references groupes (id_groupe)
);

create or replace index id_cours
    on historisation (id_cours);

create or replace index id_enseignant
    on historisation (id_enseignant);

create or replace index id_groupe
    on historisation (id_groupe);

