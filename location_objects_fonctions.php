<?php
/**
 * Fonctions utiles au plugin Location d&#039;objets
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objects\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function objet_location_services_champs($service, $location_objet, $id_location_objet, $contexte= array(), $type_prix = 'ht') {
	include_spip('action/editer_liens');
	$objet_associable = objet_associable($service);
	$data = array();

	if ($objet_associable) {
		if (function_exists('prix_par_objet')) {
			$fonction_prix = 'prix_par_objet';
		}

		list($id_table_objet, $table_liens) = $objet_associable;
		$objet = objet_type($location_objet);
		$sql = sql_select('*',"$location_objet,$table_liens", 'objet=' .sql_quote($objet) . ' AND id_objet=' . $id_location_objet);



		while ($row = sql_fetch($sql)) {
			$id_objet = $row[$id_table_objet];
			$prix = '';
			if ($prix = $fonction_prix($objet, $id_objet, $contexte, $type_prix)) {
				$prix = ' - ' . $prix;
			}

			$titre = isset($row['titre']) ?
			$row['titre'] :
				(
					isset($row['nom']) ?
					$row['nom'] :
					generer_info_entite($objet, $id_objet, 'titre')
				);

			$data[$id_objet] = $titre . $prix;
		}

	}

	return 	array(
		array(
		'saisie' => 'checkbox',
		'options' => array(
			'nom' => 'services_' . $objet,
			'label' => $objet,
			'data' => $data
		),
	)
	);
}