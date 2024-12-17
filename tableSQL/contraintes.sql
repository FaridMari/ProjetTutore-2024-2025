create or replace table contraintes
(
    id_contrainte int auto_increment
        primary key,
    id_enseignant int          not null,
    jour          varchar(255) not null,
    heure_debut   int          not null,
    heure_fin     int          not null,
    constraint contraintes_ibfk_1
        foreign key (id_enseignant) references enseignants (id_enseignant)
);

create or replace index id_enseignant
    on contraintes (id_enseignant);

