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
 *     - disponibilite_decalage_debut: integer -> décalage des dates disponibles
 *      par rapport à la première date disponible -> fonction dates_intervalle()
 *     - disponibilite_decalage_fin integer -> décalage de l'affichages des dates disponibles
 *      par rapport à la dernière date disponible -> fonction dates_intervalle()
 *     - location_extras_objets: nom de la ou des tables des objets à ajouter au formulaire comme service extra
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
 *     - disponibilite_decalage_debut: integer -> décalage des dates disponibles
 *      par rapport à la première date disponible -> fonction dates_intervalle()
 *     - disponibilite_decalage_fin integer -> décalage de l'affichages des dates disponibles
 *      par rapport à la dernière date disponible -> fonction dates_intervalle()
 *     - location_extras_objets: nom de la ou des tables des objets à ajouter au formulaire comme service extra
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
	include_spip('inc/autoriser');

	if (!is_numeric($id_objets_location) or $id_objets_location == 0) {
		$new = 'oui';
		$valeurs['_hidden'] .= '<input type="hidden" name="new" value="' . $new . '"/>';
	}
	elseif (!autoriser('modifier', 'objetslocation', $id_objets_location)) {
		return array(
			'editable' => FALSE,
			'message_erreur' => _T('objets_location:erreur_access_formulaire')
		);
	}
	$id_auteur = session_get('id_auteur');
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
	$valeurs['espace_prive'] = '';
	if($espace_prive = test_espace_prive()) {
		$valeurs['_hidden'] .= '<input type="hidden" name="espace_prive" value="' . $espace_prive . '"/>';
	}
	$valeurs['new'] = $new;

	$valeurs['_hidden'] .= '<input type="hidden" name="new" value="' . $new . '"/>';

	$valeurs['nombre_langues'] = count(liste_options_langues('changer_lang'));
	if (empty($VALEURS['lang'])) {
		$VALEURS['lang'] = $lang = $GLOBALS['meta']['langue_site'];
	}
	$valeurs['_hidden'] .= '<input type="hidden" name="lang" value="' . $lang . '"/>';

	// L'objet de loa location.
	if ($location_objet and $id_location_objet) {
		$valeurs['location_objet'] =  objet_type($location_objet);
		$valeurs['id_location_objet'] = $id_location_objet;
	}
	elseif(!$new and
		$objet = sql_fetsel('objet,id_objet',
		'spip_objets_locations_details',
		'id_objets_locations_detail_source=0 AND id_objets_location=' . $id_objets_location) and
		$espace_prive) {
			$valeurs['location_objet'] = $location_objet = table_objet_sql($objet['objet']);
			$valeurs['id_location_objet'] = $id_location_objet = $objet['id_objet'];
	}
	elseif(!$espace_prive) {
		$valeurs['editable'] = FALSE;
		$valeurs['message_erreur'] = _T('objets_location:erreur_access_formulaire');
	}

	// Les valeurs des services extras enregistrés
	if (!$new and
		$objets_extras = sql_allfetsel('objet,id_objet',
			'spip_objets_locations_details',
			'id_objets_locations_detail_source!=0 AND id_objets_location=' . $id_objets_location)) {
			foreach($objets_extras as $objet_extras) {
				$valeurs['extras_' . $objet_extras['objet']][] = $objet_extras['id_objet'];
			}

	}


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
	if($objets_extras) {
		$valeurs['_hidden'] .= '<input type="hidden" name="objets_extras" value="' . implode(',',$objets_extras) . '"/>';
	}

	if (!empty($valeurs['location_objet'] and !empty($valeurs['id_location_objet']))) {
		$valeurs['_hidden'] .= '<input type="hidden" name="location_objet" value="' . $valeurs['location_objet'] . '"/>';
		$valeurs['_hidden'] .= '<input type="hidden" name="id_location_objet" value="' . $valeurs['id_location_objet'] . '"/>';
	}

	if ($espace_prive) {
		$valeurs['espace_prive'] = true;
		$valeurs['_hidden'] .= '<input type="hidden" name="espace_prive" value="1"/>';
	}
	else {
		$valeurs['id_auteur'] = $id_auteur;
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
 *     - disponibilite_decalage_debut: integer -> décalage des dates disponibles
 *      par rapport à la première date disponible -> fonction dates_intervalle()
 *     - disponibilite_decalage_fin integer -> décalage de l'affichages des dates disponibles
 *      par rapport à la dernière date disponible -> fonction dates_intervalle()
 *     - location_extras_objets: nom de la ou des tables des objets à ajouter au formulaire comme service extra
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
	$date_debut = _request('date_debut');
	$date_fin = _request('date_fin');
	$horaire = _request('horaire');

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


	$erreurs += formulaires_editer_objet_verifier(
		'objets_location',
		$id_objets_location,
		array(
			'id_auteur',
			'location_objet',
			'id_location_objet',
			'date_debut',
			'date_fin',
		)
	);

	if (strtotime($date_debut) > strtotime($date_fin)) {
		$erreurs['date_fin'] = _T('objets_location:erreur_date_fin_anterieur_date_debut');
	}
	elseif ($erreur = $verifier(
				array(
					$date_debut,
					$date_fin
				),
				'dates_diponibles',
				array(
					'objet' => _request('location_objet'),
					'id_objet' => _request('id_location_objet'),
					'debut' => 0,
					'fin' => 0,
					'utilisation_squelette' => 'disponibilites/utilisees_objet_location',
					'utilisation_id_exclu' => $id_objets_location,
					'format' => $format,
				)
			)
		) {
			$erreurs['date_fin'] = $erreur;
	}

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
 *     - disponibilite_decalage_debut: integer -> décalage des dates disponibles
 *      par rapport à la première date disponible -> fonction dates_intervalle()
 *     - disponibilite_decalage_fin integer -> décalage de l'affichages des dates disponibles
 *      par rapport à la dernière date disponible -> fonction dates_intervalle()
 *     - location_extras_objets: nom de la ou des tables des objets à ajouter au formulaire comme service extra
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

	$new = _request('new');
	$retours = formulaires_editer_objet_traiter(
			'objets_location',
			$id_objets_location,
			'',
			$lier_trad,
			$retour,
			$config_fonc,
			$row,
			$hidden);

	session_set('id_objets_location', $retours['id_objets_location']);

	// Le messaage de confirmation pour le formulaire public
	if (isset($retours['message_ok']) and !_request('espace_prive')) {
		$message = '<div class="intro"><p>' . _T('objets_location:texte_merci_de_votre_location') . '</p></div>';
		$message .= '<div class="detail_reservation">';
		$message .= '<h3>' . _T('objets_location:texte_details_location') . '</h3>';
		$message .= recuperer_fond('inclure/location', array(
			'id_objets_location' => $retours['id_objets_location']
		));
		$message .= '</div>';
		$retours['message_ok'] = $message;
	}
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
