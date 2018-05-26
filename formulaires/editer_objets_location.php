<?php
/**
 * Gestion du formulaire de d'édition de objets_location
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objects\Formulaires
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
		$objet='',
		$id_objet='',
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
		$objet='',
		$id_objet='',
		$options = array(),
		$retour = '',
		$associer_objet = '',
		$lier_trad = 0,
		$config_fonc = '',
		$row = array(),
		$hidden = '') {
	$valeurs = formulaires_editer_objet_charger(
			'objets_location',
			$id_objets_location,
			'',
			$lier_trad,
			$retour,
			$config_fonc,
			$row,
			$hidden);

	$valeurs['objet'] = $objet;
	$valeurs['id_objet'] = $id_objet;

	foreach($options as $index => $valeur) {
		$valeurs[$index] = $valeur;
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
		$objet='',
		$id_objet='',
		$options = array(),
		$retour = '',
		$associer_objet = '',
		$lier_trad = 0,
		$config_fonc = '',
		$row = array(),
		$hidden = '') {
	$erreurs = array();

	$verifier = charger_fonction('verifier', 'inc');

	foreach (array('date_debut', 'date_fin') AS $champ) {
		$normaliser = null;
		if ($erreur = $verifier(_request($champ), 'date', array('normaliser'=>'datetime'), $normaliser)) {
			$erreurs[$champ] = $erreur;
		// si une valeur de normalisation a ete transmis, la prendre.
		} elseif (!is_null($normaliser)) {
			set_request($champ, $normaliser);
		// si pas de normalisation ET pas de date soumise, il ne faut pas tenter d'enregistrer ''
		} else {
			set_request($champ, null);
		}
	}

	$erreurs += formulaires_editer_objet_verifier('objets_location', $id_objets_location, array('reference'));

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
		$objet='',
		$id_objet='',
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
			$objet,
			$id_objet,
			$options,
			'',
			$lier_trad,
			$retour,
			$config_fonc,
			$row,
			$hidden);

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
