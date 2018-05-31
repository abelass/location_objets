<?php
/**
 * Fonctions utiles au plugin Location d&#039;objets
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets\Fonctions
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Retourne le champ pour les services extras.
 *
 * @param mixed $service
 *        	Le nom de ou des objets à utiliser comme service extra.
 * @param string $location_objet
 *        	L'objet de la location
 * @param integer $id_location_objet
 *          L'id de l'objet de la location
 * @param array $contexte
 *          Les variables du contexte
 * @param array $options
 *          type_prix: prix_ht ou prix
 *          separateur_prix
 * @return array
 *          La saisies
 */
function objet_location_extras_champs($service, $location_objet_table, $id_location_objet, $contexte = array(), $options = array()) {
	include_spip('action/editer_liens');

	// Cr´er une variable pour chaque option
	foreach ($options as $cle => $valeur) {
		$$cle = $valeur;
	}

	// Le type de prix
	if (!$type_prix) {
		$type_prix = 'prix_ht';
	}

	if (!$separateur_prix) {
		$separateur_prix = '<span class="separateur"> - </span>';
	}

	$objet_associable = objet_associable($service);
	$data = array();

	if ($objet_associable) {
		if (function_exists('prix_par_objet')) {
			$fonction_prix = 'prix_par_objet';
		}

		list ($id_table_objet, $table_liens) = $objet_associable;
		$location_objet = objet_type($location_objet_table);
		$table_extra = table_objet_sql($service);
		$identifiant_extra = id_table_objet($service);
		$objet_extra = objet_type($service);

		$sql = sql_select('*',
			"$table_extra LEFT JOIN $table_liens USING(id_objets_service)",
			'objet=' . sql_quote($location_objet) . ' AND id_objet=' . $id_location_objet);
		while ($row = sql_fetch($sql)) {
			$id_objet = $row[$identifiant_extra];

			if ($prix = $fonction_prix($objet_extra, $id_objet, $contexte, $type_prix)) {
				$prix = $separateur_prix . filtres_prix_formater($prix);
			}
			else {
				$prix = '';
			}

			$titre = isset($row['titre']) ?
				$row['titre'] :
				(
					isset($row['nom']) ?
					$row['nom'] :
					generer_info_entite($objet_extra, $id_objet, 'titre'));

			$data[$id_objet] = $titre . $prix;
		}
	}

	return array(
		array(
			'saisie' => 'checkbox',
			'options' => array(
				'nom' => 'extras_' . $objet_extra,
				'label' => $objet_extra,
				'data' => $data
			)
		)
	);
}