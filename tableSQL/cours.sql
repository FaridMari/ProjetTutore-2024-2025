create or replace table cours
(
    id_cours        int auto_increment
        primary key,
    nom_cours       varchar(255)     not null,
    nb_heures_total double default 0 null,
    nb_heures_cm    double default 0 null,
    nb_heures_td    double default 0 null,
    nb_heures_tp    double default 0 null
);

