<?php
/**
 * Fonctions spécifiquesdes objets locations
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
 * Calcule le prix et infis relationnées d'un objet
 *
 * @param array $set
 *        	Les valeurs à enregistrer.
 * @param array $contexte
 * 	        Les variables de l'environnement.
 * @return array
 * 	        Les valeurs à enregistrer.
 */
function location_prix_objet($set, $contexte) {
	foreach ($contexte as $cle => $valeur) {
		$$cle = $valeur;
	}

	if (test_plugin_actif('prix_objets')) {
		$prix_objet = TRUE;
		if ($prix_unitaire_ht = prix_par_objet($location_objet, $id_location_objet, array(
			'date_debut' => $date_debut,
			'date_fin' => $date_fin
		))) {
			$set['prix_unitaire_ht'] = $prix_unitaire_ht;
			$prix_ttc = prix_par_objet($location_objet, $id_location_objet, array(
				'date_debut' => $date_debut,
				'date_fin' => $date_fin
			), 'prix');
			$set['taxe'] = $prix_ttc - $prix_unitaire_ht;
			$set['devise'] = devise_defaut_objet($id_location_objet, $location_objet);
			$set['prix_total'] = _request('prix_total');
		}
	}

	$set = pipeline('location_prix_objet', array(
		'data' => $set,
		'args' => $contexte
	));

	return $set;
}
