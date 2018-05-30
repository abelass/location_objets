<?php
/**
 * Gestion du formulaire de d'édition de objets_location
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_objets_location
 *     Identifiant du objets_location. 'new' pour un nouveau objets_location.
 * @param string $location_objet
 *     L'objet à louer
 * @param integer $id_location_objet
 *     L'identifiant de l'objet à louer
 * @param array $options
 *     Difféntes variables à passer
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le objets_location créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un objets_location source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du objets_location, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_objets_location_identifier_dist(
		$id_objets_location = 'new',
		$location_objet='',
		$id_location_objet='',
		$options,
		$retour = '',
		$associer_objet = '',
		$lier_trad = 0,
		$config_fonc = '',
		$row = array(),
		$hidden = '') {
	return serialize(array(intval($id_objets_location), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de objets_location
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_objets_location
 *     Identifiant du objets_location. 'new' pour un nouveau objets_location.
 * @param string $location_objet
 *     L'objet à louer
 * @param integer $id_location_objet
 *     L'identifiant de l'objet à louer
 * @param array $options
 *     Difféntes variables à passer
 *     disponibilite_debut: integer -> décalage des dates disponibles
 *      par rapport à la première date disponible -> fonction dates_intervalle()
 *     disponibilite_debut integer -> décalage de l'affichages des dates disponibles
 *      par rapport à la dernière date disponible -> fonction dates_intervalle()
 *     location_extras_objets: nom de la ou des tables des objets à ajouter au fo0mulaire comme service extra
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le objets_location créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un objets_location source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du objets_location, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_objets_location_charger_dist(
		$id_objets_location = 'new',
		$location_objet='',
		$id_location_objet='',
		$options = array(),
		$retour = '',
		$associer_objet = '',
		$lier_trad = 0,
		$config_fonc = '',
		$row = array(),
		$hidden = '') {

	include_spip('inc/config');
	$config = lire_config('location_objets');
	$valeurs = formulaires_editer_objet_charger(
			'objets_location',
			$id_objets_location,
			'',
			$lier_trad,
			$retour,
			$config_fonc,
			$row,
			$hidden);

	if ($location_objet) {
		$valeurs['objet'] =  objet_type($location_objet);
	}
	elseif(is_numeric($id_objets_location) and
		$objet = sql_fetsel('objet,id_objet',
		'spip_objets_locations_details',
		'id_objets_locations_detail_source=0 AND id_objets_location=' . $id_objets_location)) {
		$valeurs['objet'] = $objet['objet'];
		$location_objet = table_objet_sql($objet['objet']);
		$id_location_objet = $objet['id_objet'];
	}


	$valeurs['location_objet'] = $location_objet;
	$valeurs['id_location_objet'] = $id_location_objet;

	foreach($options as $index => $valeur) {
		$valeurs[$index] = $valeur;
	}

	if (isset($valeurs['location_extras_objets'])) {
		$objets_extras = $valeurs['location_extras_objets'];
		if (!is_array($objets_extras)) {
			if (match(',', $objets_extras)) {
				$valeurs['_location_extras_objets'] = explode(',', $objets_extras);
			}
			else {
				$valeurs['_location_extras_objets'] = array($objets_extras);
			}
		}
		$objets_extras = $valeurs['_location_extras_objets'];
		unset($valeurs['location_extras_objets']);
	}
	elseif (isset($config['location_extras_objets'])) {
		$valeurs['_location_extras_objets'] = $config['location_extras_objets'];
		$objets_extras = $valeurs['_location_extras_objets'];
	}

	$valeurs['_hidden'] .= '<input type="hidden" name="objets_extras" value="' . implode(',',$objets_extras) . '"/>';

	if (!empty($valeurs['location_objet'] and !empty($valeurs['id_location_objet']))) {
		$valeurs['_hidden'] .= '<input type="hidden" name="location_objet" value="' . $valeurs['location_objet'] . '"/>';
		$valeurs['_hidden'] .= '<input type="hidden" name="id_location_objet" value="' . $valeurs['id_location_objet'] . '"/>';
	}

	if (test_espace_prive()) {
		$valeurs['espace_prive'] = true;
		$valeurs['_hidden'] .= '<input type="hidden" name="espace_prive" value="1"/>';
	}
	else {
		$valeurs['id_auteur'] = session_get('id_auteur');
		$valeurs['_hidden'] .= '<input type="hidden" name="id_auteur" value="' . $valeurs['id_auteur'] . '"/>';
	}

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de objets_location
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_objets_location
 *     Identifiant du objets_location. 'new' pour un nouveau objets_location.
 * @param string $location_objet
 *     L'objet à louer
 * @param integer $id_location_objet
 *     L'identifiant de l'objet à louer
 * @param array $options
 *     Difféntes variables à passer
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le objets_location créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un objets_location source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du objets_location, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_objets_location_verifier_dist(
		$id_objets_location = 'new',
		$location_objet='',
		$id_location_objet='',
		$options = array(),
		$retour = '',
		$associer_objet = '',
		$lier_trad = 0,
		$config_fonc = '',
		$row = array(),
		$hidden = '') {
	$erreurs = array();

	$verifier = charger_fonction('verifier', 'inc');

	/*foreach (array('date_debut', 'date_fin') AS $champ) {
		$normaliser = null;
		if ($erreur = $verifier(_request($champ), 'amj', array('normaliser'=>'datetime'), $normaliser)) {
			$erreurs[$champ] = $erreur;
			// si une valeur de normalisation a ete transmis, la prendre.
		} elseif (!is_null($normaliser)) {
			set_request($champ, $normaliser);
			// si pas de normalisation ET pas de date soumise, il ne faut pas tenter d'enregistrer ''
		} else {
			set_request($champ, null);
		}
	}*/

	$erreurs += formulaires_editer_objet_verifier('objets_location', $id_objets_location, array('id_auteur', 'reference'));

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de objets_location
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_objets_location
 *     Identifiant du objets_location. 'new' pour un nouveau objets_location.
 * @param string $location_objet
 *     L'objet à louer
 * @param integer $id_location_objet
 *     L'identifiant de l'objet à louer
 * @param array $options
 *     Difféntes variables à passer
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le objets_location créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un objets_location source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du objets_location, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_objets_location_traiter_dist(
		$id_objets_location = 'new',
		$location_objet='',
		$id_location_objet='',
		$options = array(),
		$retour = '',
		$associer_objet = '',
		$lier_trad = 0,
		$config_fonc = '',
		$row = array(),
		$hidden = '') {
	$retours = formulaires_editer_objet_traiter(
			'objets_location',
			$id_objets_location,
			'',
			$lier_trad,
			$retour,
			$config_fonc,
			$row,
			$hidden);

	// Enregistrement de l'objet de location

	$id_objets_location = $retours['id_objets_location'];


	$date_debut = strtotime(_request('date_debut'));
	$date_fin = strtotime(_request('date_fin'));
	$location_objet = objet_type(_request('location_objet'));
	$id_location_objet = _request('id_location_objet');
	if ($date_fin >= $date_debut) {
		$difference = $date_fin - $date_debut;
		$nombre_jours = round($difference / (60 * 60 * 24)) + $fin;
	}


	$set = array(
		'id_objets_location' => $id_objets_location,
		'objet' => $location_objet,
		'id_objet' => $id_location_objet,
		'titre' => generer_info_entite($id_location_objet, $location_objet, 'titre'),
		'quantite' => $nombre_jours,
	);

	if (!is_numeric($id_objets_location)) {
		$set['date'] = $date = date('Y-m-d H:i:s',time());
	}

	if (test_plugin_actif('prix_objets')) {
		$set['prix_unitaire_ht'] = prix_par_objet(
				$location_objet,
				$id_location_objet,
				array(
					'date_debut' => _request('date_debut'),
					'date_fin' => _request('date_fin'),
				)
			);
		$prix_ttc = prix_par_objet(
			$location_objet,
			$id_location_objet,
			array(
				'date_debut' => _request('date_debut'),
				'date_fin' => _request('date_fin'),
			),
			'prix'
		);
		$set['taxe'] = $prix_ttc - $set['prix_unitaire_ht'];
		$set['devise'] = devise_defaut_objet($id_location_objet,$location_objet);
	}

		$id_objets_locations_detail = sql_insertq('spip_objets_locations_details', $set);




	// Un lien a prendre en compte ?
	if ($associer_objet and $id_objets_location = $retours['id_objets_location']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');

			objet_associer(array('objets_location' => $id_objets_location), array($objet => $id_objet));

			if (isset($retours['redirect'])) {
				$retours['redirect'] = parametre_url($retours['redirect'], 'id_lien_ajoute', $id_objets_location, '&');
			}
		}
	}

	return $retours;
}
