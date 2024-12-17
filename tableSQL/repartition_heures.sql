create or replace table repartition_heures
(
    id_repartition        int auto_increment
        primary key,
    id_cours              int         not null,
    semaine_debut         int         not null,
    semaine_fin           int         not null,
    type_heure            varchar(20) not null,
    nb_heures_par_semaine int         not null,
    constraint repartition_heures_ibfk_1
        foreign key (id_cours) references cours (id_cours)
);

create or replace index id_cours
    on repartition_heures (id_cours);

