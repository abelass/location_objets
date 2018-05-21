<?php

/**
 *  Fichier généré par la Fabrique de plugin v6
 *   le 2018-05-21 15:24:39
 *
 *  Ce fichier de sauvegarde peut servir à recréer
 *  votre plugin avec le plugin «Fabrique» qui a servi à le créer.
 *
 *  Bien évidemment, les modifications apportées ultérieurement
 *  par vos soins dans le code de ce plugin généré
 *  NE SERONT PAS connues du plugin «Fabrique» et ne pourront pas
 *  être recréées par lui !
 *
 *  La «Fabrique» ne pourra que régénerer le code de base du plugin
 *  avec les informations dont il dispose.
 *
**/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$data = array (
  'fabrique' => 
  array (
    'version' => 6,
  ),
  'paquet' => 
  array (
    'prefixe' => 'location_objects',
    'nom' => 'Location d\'objets',
    'slogan' => 'Louer vos objets',
    'description' => 'Gère la location d\'objets',
    'logo' => 
    array (
      0 => '',
    ),
    'version' => '1.0.0',
    'auteur' => 'Rainer Müller',
    'auteur_lien' => 'https://websimple.be',
    'licence' => 'GNU/GPL v3',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.0;3.2.*]',
    'documentation' => 'https://github.com/abelass/location_objets',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => '',
    'fichiers' => 
    array (
      0 => 'autorisations',
      1 => 'fonctions',
      2 => 'pipelines',
    ),
    'inserer' => 
    array (
      'paquet' => '',
      'administrations' => 
      array (
        'maj' => '',
        'desinstallation' => '',
        'fin' => '',
      ),
      'base' => 
      array (
        'tables' => 
        array (
          'fin' => '',
        ),
      ),
    ),
    'scripts' => 
    array (
      'pre_copie' => '',
      'post_creation' => '',
    ),
    'exemples' => 'on',
  ),
  'objets' => 
  array (
    0 => 
    array (
      'nom' => 'Locations',
      'nom_singulier' => 'location',
      'genre' => 'feminin',
      'logo' => 
      array (
        0 => '',
        32 => '',
        24 => '',
        16 => '',
        12 => '',
      ),
      'logo_variantes' => 'on',
      'table' => 'spip_objets_locations',
      'cle_primaire' => 'id_objets_location',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'objets_location',
      'champs' => 
      array (
        2 => 
        array (
          'nom' => 'Type',
          'champ' => 'type',
          'sql' => 'varchar(20) NOT NULL DEFAULT \'\'',
          'recherche' => '',
          'saisie' => 'selection',
          'explication' => '',
          'saisie_options' => '',
        ),
        5 => 
        array (
          'nom' => 'Date debut',
          'champ' => 'date_debut',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'date',
          'explication' => '',
          'saisie_options' => '',
        ),
        6 => 
        array (
          'nom' => 'Date fin',
          'champ' => 'date_fin',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'date',
          'explication' => '',
          'saisie_options' => '',
        ),
        7 => 
        array (
          'nom' => 'Jour debut',
          'champ' => 'jour_debut',
          'sql' => 'int(1) NOT NULL DEFAULT \'0\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'selection',
          'explication' => '',
          'saisie_options' => '',
        ),
        8 => 
        array (
          'nom' => 'Jour fin',
          'champ' => 'jour_fin',
          'sql' => 'int(1) NOT NULL DEFAULT \'0\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'selection',
          'explication' => '',
          'saisie_options' => '',
        ),
        9 => 
        array (
          'nom' => 'Référence',
          'champ' => 'reference',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'reference',
      'champ_date' => 'date',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Locations',
        'titre_objet' => 'Location',
        'info_aucun_objet' => 'Aucune location',
        'info_1_objet' => 'Une location',
        'info_nb_objets' => '@nb@ locations',
        'icone_creer_objet' => 'Créer une location',
        'icone_modifier_objet' => 'Modifier cette location',
        'titre_logo_objet' => 'Logo de cette location',
        'titre_langue_objet' => 'Langue de cette location',
        'texte_definir_comme_traduction_objet' => 'Cette location est une traduction de la location numéro :',
        'titre_\\objets_lies_objet' => 'Liés à cette location',
        'titre_objets_rubrique' => 'Locations de la rubrique',
        'info_objets_auteur' => 'Les locations de cet auteur',
        'retirer_lien_objet' => 'Retirer cette location',
        'retirer_tous_liens_objets' => 'Retirer toutes les locations',
        'ajouter_lien_objet' => 'Ajouter cette location',
        'texte_ajouter_objet' => 'Ajouter une location',
        'texte_creer_associer_objet' => 'Créer et associer une location',
        'texte_changer_statut_objet' => 'Cette location est :',
        'supprimer_objet' => 'Supprimer cette location',
        'confirmer_supprimer_objet' => 'Confirmez-vous la suppression de cette location ?',
      ),
      'liaison_directe' => '',
      'table_liens' => 'on',
      'vue_liens' => 
      array (
        0 => 'spip_immeubles',
      ),
      'afficher_liens' => 'on',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => 'on',
      'fichiers' => 
      array (
        'echafaudages' => 
        array (
          0 => 'prive/squelettes/contenu/objets.html',
          1 => 'prive/objets/infos/objet.html',
          2 => 'prive/squelettes/contenu/objet.html',
        ),
        'explicites' => 
        array (
          0 => 'action/supprimer_objet.php',
        ),
      ),
      'saisies' => 
      array (
        0 => 'objets',
      ),
      'autorisations' => 
      array (
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => '',
        'objet_supprimer' => '',
        'associerobjet' => '',
      ),
    ),
    1 => 
    array (
      'nom' => 'Location details',
      'nom_singulier' => 'Location detail',
      'genre' => 'masculin',
      'logo' => 
      array (
        0 => '',
        32 => '',
        24 => '',
        16 => '',
        12 => '',
      ),
      'logo_variantes' => '',
      'table' => 'spip_objets_locations_details',
      'cle_primaire' => 'id_objets_locations_detail',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'objets_locations_detail',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Id Location',
          'champ' => 'id_objets_location',
          'sql' => 'int(11) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'objets_locations',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Objet',
          'champ' => 'objet',
          'sql' => 'varchar(50) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Id objet',
          'champ' => 'id_objet',
          'sql' => 'bigint(21) NOT NULL DEFAULT \'0\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'text NOT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '8',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'Quantite',
          'champ' => 'quantite',
          'sql' => 'int(11) NOT NULL DEFAULT \'1\'',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        5 => 
        array (
          'nom' => 'Prix ht',
          'champ' => 'prix_ht',
          'sql' => 'decimal(20.6) NOT NULL DEFAULT \'0\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        6 => 
        array (
          'nom' => 'Taxe',
          'champ' => 'taxe',
          'sql' => 'decimal(4,4) NOT NULL DEFAULT \'0\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        7 => 
        array (
          'nom' => 'Devise',
          'champ' => 'devise',
          'sql' => 'varchar(3) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        8 => 
        array (
          'nom' => 'Réduction',
          'champ' => 'reduction',
          'sql' => 'decimal(4,4) not null default 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => 'date',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Location details',
        'titre_objet' => 'Location detail',
        'info_aucun_objet' => 'Aucun location detail',
        'info_1_objet' => 'Un location detail',
        'info_nb_objets' => '@nb@ location details',
        'icone_creer_objet' => 'Créer un location detail',
        'icone_modifier_objet' => 'Modifier ce location detail',
        'titre_logo_objet' => 'Logo de ce location detail',
        'titre_langue_objet' => 'Langue de ce location detail',
        'texte_definir_comme_traduction_objet' => 'Ce location detail est une traduction du location detail numéro :',
        'titre_\\objets_lies_objet' => 'Liés à ce location detail',
        'titre_objets_rubrique' => 'Location details de la rubrique',
        'info_objets_auteur' => 'Les location details de cet auteur',
        'retirer_lien_objet' => 'Retirer ce location detail',
        'retirer_tous_liens_objets' => 'Retirer tous les location details',
        'ajouter_lien_objet' => 'Ajouter ce location detail',
        'texte_ajouter_objet' => 'Ajouter un location detail',
        'texte_creer_associer_objet' => 'Créer et associer un location detail',
        'texte_changer_statut_objet' => 'Ce location detail est :',
        'supprimer_objet' => 'Supprimer cet location detail',
        'confirmer_supprimer_objet' => 'Confirmez-vous la suppression de cet location detail ?',
      ),
      'liaison_directe' => 'spip_objets_locations',
      'table_liens' => 'on',
      'afficher_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'autorisations' => 
      array (
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => '',
        'objet_supprimer' => '',
        'associerobjet' => '',
      ),
    ),
  ),
  'images' => 
  array (
    'paquet' => 
    array (
      'logo' => 
      array (
        0 => 
        array (
          'extension' => 'png',
          'contenu' => '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',
        ),
      ),
    ),
    'objets' => 
    array (
      0 => 
      array (
        'logo' => 
        array (
          0 => 
          array (
            'extension' => 'png',
            'contenu' => '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',
          ),
        ),
      ),
      1 => 
      array (
      ),
    ),
  ),
);
