create or replace table detailscours
(
    id_ressource            int auto_increment
        primary key,
    id_cours                int          not null,
    id_responsable_module   int          not null,
    type_salle              varchar(255) not null,
    equipements_specifiques text         not null,
    repartition_heures      text         not null,
    constraint detailscours_ibfk_1
        foreign key (id_cours) references cours (id_cours),
    constraint detailscours_ibfk_2
        foreign key (id_responsable_module) references enseignants (id_enseignant)
);

create or replace index id_cours
    on detailscours (id_cours);

create or replace index id_responsable_module
    on detailscours (id_responsable_module);

