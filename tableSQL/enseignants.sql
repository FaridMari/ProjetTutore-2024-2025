create or replace table enseignants
(
    id_enseignant    int              not null
        primary key,
    heures_affectees double default 0 null,
    statut           varchar(255)     not null,
    total_hetd       double default 0 null,
    constraint enseignants_ibfk_1
        foreign key (id_enseignant) references utilisateurs (id_utilisateur)
);

