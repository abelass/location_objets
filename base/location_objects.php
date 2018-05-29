<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objects\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function location_objects_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['objets_locations'] = 'objets_locations';
	$interfaces['table_des_tables']['objets_locations_details'] = 'objets_locations_details';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function location_objects_declarer_tables_objets_sql($tables) {

	$tables['spip_objets_locations'] = array(
		'type' => 'objets_location',
		'principale' => 'oui',
		'table_objet_surnoms' => array('objetslocation'), // table_objet('objets_location') => 'objets_locations'
		'field'=> array(
			'id_objets_location' => 'bigint(21) NOT NULL',
			'id_auteur'          => 'bigint(21) NOT NULL',
			'type'               => 'varchar(20) NOT NULL DEFAULT ""',
			'date_debut'         => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'date_fin'           => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'horaire'            => 'varchar(3) NOT NULL DEFAULT ""',
			'jour_debut'         => 'int(1) NOT NULL DEFAULT "0"',
			'jour_fin'           => 'int(1) NOT NULL DEFAULT "0"',
			'reference'          => 'varchar(255) NOT NULL DEFAULT ""',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_objets_location',
			'KEY statut'         => 'statut',
		),
		'titre' => 'reference AS titre, "" AS lang',
		'date' => 'date',
		'champs_editables'  => array('id_auteur', 'date_debut', 'date_fin', 'jour_debut', 'jour_fin', 'reference', 'horaire'),
		'champs_versionnes' => array('id_auteur', 'date_debut', 'date_fin', 'jour_debut', 'jour_fin', 'reference', 'horaire'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_objets_locations_liens'),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'prop'     => 'texte_statut_propose_evaluation',
			'publie'   => 'texte_statut_publie',
			'refuse'   => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prop,prepa',
				'post_date' => 'date',
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'objets_location:texte_changer_statut_objets_location',


	);

	$tables['spip_objets_locations_details'] = array(
		'type' => 'objets_locations_detail',
		'principale' => 'oui',
		'table_objet_surnoms' => array('objetslocationsdetail'), // table_objet('objets_locations_detail') => 'objets_locations_details'
		'field'=> array(
			'id_objets_locations_detail' => 'bigint(21) NOT NULL',
			'id_objets_location' => 'bigint(21) NOT NULL DEFAULT 0',
			'id_objets_location' => 'int(11) NOT NULL DEFAULT 0',
			'objet'              => 'varchar(50) NOT NULL DEFAULT ""',
			'id_objet'           => 'bigint(21) NOT NULL DEFAULT "0"',
			'titre'              => 'text NOT NULL',
			'quantite'           => 'int(11) NOT NULL DEFAULT "1"',
			'prix_ht'            => 'decimal(20.6) NOT NULL DEFAULT "0"',
			'taxe'               => 'decimal(4,4) NOT NULL DEFAULT "0"',
			'devise'             => 'varchar(3) NOT NULL DEFAULT ""',
			'reduction'          => 'decimal(4,4) not null default 0',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_objets_locations_detail',
			'KEY id_objets_location' => 'id_objets_location',
			'KEY statut'         => 'statut',
			'KEY objet'         => 'objet,id_objet',
		),
		'titre' => 'titre AS titre, "" AS lang',
		'date' => 'date',
		'champs_editables'  => array('id_objets_location', 'objet', 'id_objet', 'titre', 'prix_ht', 'taxe', 'devise', 'reduction', 'id_objets_location'),
		'champs_versionnes' => array('id_objets_location', 'objet', 'id_objet', 'titre', 'prix_ht', 'taxe', 'devise', 'reduction', 'id_objets_location'),
		'rechercher_champs' => array("titre" => 8),
		'tables_jointures'  => array('spip_objets_locations_details_liens'),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'prop'     => 'texte_statut_propose_evaluation',
			'publie'   => 'texte_statut_publie',
			'refuse'   => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prop,prepa',
				'post_date' => 'date',
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'objets_locations_detail:texte_changer_statut_objets_locations_detail',


	);

	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function location_objects_declarer_tables_auxiliaires($tables) {

	$tables['spip_objets_locations_liens'] = array(
		'field' => array(
			'id_objets_location' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_objets_location,id_objet,objet',
			'KEY id_objets_location' => 'id_objets_location',
		)
	);
	$tables['spip_objets_locations_details_liens'] = array(
		'field' => array(
			'id_objets_locations_detail' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_objets_locations_detail,id_objet,objet',
			'KEY id_objets_locations_detail' => 'id_objets_locations_detail',
		)
	);

	return $tables;
}
