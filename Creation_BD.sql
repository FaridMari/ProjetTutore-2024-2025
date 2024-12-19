drop table if exists affectations;
drop table if exists contraintes;
drop table if exists details_cours;
drop table if exists repartition_heures;
drop table if exists historisation;
drop table if exists voeux;
drop table if exists cours;
drop table if exists voeux_hors_iut;
drop table if exists enseignants;
drop table if exists utilisateurs;
drop table if exists groupes;



create table cours
(
    id_cours        int auto_increment
        primary key,
    formation       varchar(255)     not null,
    semestre        varchar(255)     not null,
    nom_cours       varchar(255)     not null,
    code_cours      varchar(255)     not null,
    nb_heures_total double default 0 null,
    nb_heures_cm    double default 0 null,
    nb_heures_td    double default 0 null,
    nb_heures_tp    double default 0 null,
    nb_heures_ei    double default 0 null
);

create table groupes
(
    id_groupe  int auto_increment
        primary key,
    nom_groupe varchar(255) not null,
    niveau     varchar(255) not null
);

create table repartition_heures
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

create index id_cours
    on repartition_heures (id_cours);

create table utilisateurs
(
    id_utilisateur int auto_increment
        primary key,
    nom            varchar(255) not null,
    prenom         varchar(255) not null,
    email          varchar(255) not null,
    mot_de_passe   varchar(255) not null,
    role           varchar(255) not null,
    constraint email
        unique (email)
);

create table enseignants
(
    id_enseignant    int              not null
        primary key,
    id_utilisateur   int              not null,
    heures_affectees double default 0 null,
    statut           varchar(255)     not null,
    total_hetd       double default 0 null,
    constraint enseignants_ibfk_1
        foreign key (id_utilisateur) references utilisateurs (id_utilisateur)
);

create table affectations
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

create index id_cours
    on affectations (id_cours);

create index id_enseignant
    on affectations (id_enseignant);

create index id_groupe
    on affectations (id_groupe);

create table contraintes
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

create index id_enseignant
    on contraintes (id_enseignant);

create table details_cours
(
    id_ressource            int auto_increment
        primary key,
    id_cours                int          not null,
    id_responsable_module   int          not null,
    type_salle              varchar(255) not null,
    equipements_specifiques text         not null,
    details     text         not null,
    constraint detailscours_ibfk_1
        foreign key (id_cours) references cours (id_cours),
    constraint detailscours_ibfk_2
        foreign key (id_responsable_module) references enseignants (id_enseignant)
);

create index id_cours
    on details_cours (id_cours);

create index id_responsable_module
    on details_cours (id_responsable_module);

create table historisation
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

create index id_cours
    on historisation (id_cours);

create index id_enseignant
    on historisation (id_enseignant);

create index id_groupe
    on historisation (id_groupe);

create table voeux
(
    id_voeu       int auto_increment
        primary key,
    id_enseignant int          not null,
    id_cours      int          not null,
    semestre      varchar(255) not null,
    nb_heures     double       not null,
    remarques     text         not null,
    constraint voeux_ibfk_1
        foreign key (id_enseignant) references enseignants (id_enseignant),
    constraint voeux_ibfk_2
        foreign key (id_cours) references cours (id_cours)
);

create index id_cours
    on voeux (id_cours);

create index id_enseignant
    on voeux (id_enseignant);

create table voeux_hors_iut
(
    id_voeu_hi       int auto_increment
        primary key,
    id_enseignant int          not null,
    composant    varchar(255)  null,
    formation    varchar(255)  null,
    module       varchar(255)  null,
    nb_heures_cm double        null,
    nb_heures_td double        null,
    nb_heures_tp double        null,
    nb_heures_ei double        null,
    nb_total     double        null,
    constraint voeux_hors_iut_ibfk_1
        foreign key (id_enseignant) references enseignants (id_enseignant)
);

create index id_enseignant
    on voeux_hors_iut (id_enseignant);


