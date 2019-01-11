<?php
/**
 * Gestion du formulaire de d'édition de objets_locations_detail
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018 - 2019
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
 * @param int|string $id_objets_locations_detail
 *     Identifiant du objets_locations_detail. 'new' pour un nouveau objets_locations_detail.
 * @param int $id_objets_location
 *     Identifiant de l'objet parent (si connu)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un objets_locations_detail source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du objets_locations_detail, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_objets_locations_detail_identifier_dist($id_objets_locations_detail = 'new', $id_objets_location = 0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(intval($id_objets_locations_detail)));
}

/**
 * Chargement du formulaire d'édition de objets_locations_detail
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_objets_locations_detail
 *     Identifiant du objets_locations_detail. 'new' pour un nouveau objets_locations_detail.
 * @param int $id_objets_location
 *     Identifiant de l'objet parent (si connu)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un objets_locations_detail source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du objets_locations_detail, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_objets_locations_detail_charger_dist($id_objets_locations_detail = 'new', $id_objets_location = 0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$valeurs = formulaires_editer_objet_charger('objets_locations_detail', $id_objets_locations_detail, $id_objets_location, $lier_trad, $retour, $config_fonc, $row, $hidden);
	if (!$valeurs['id_objets_location']) {
		$valeurs['id_objets_location'] = $id_objets_location;
	}
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de objets_locations_detail
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_objets_locations_detail
 *     Identifiant du objets_locations_detail. 'new' pour un nouveau objets_locations_detail.
 * @param int $id_objets_location
 *     Identifiant de l'objet parent (si connu)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un objets_locations_detail source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du objets_locations_detail, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_objets_locations_detail_verifier_dist($id_objets_locations_detail = 'new', $id_objets_location = 0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('objets_locations_detail', $id_objets_locations_detail, array('id_objets_location', 'objet', 'id_objet', 'id_objets_location'));

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de objets_locations_detail
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_objets_locations_detail
 *     Identifiant du objets_locations_detail. 'new' pour un nouveau objets_locations_detail.
 * @param int $id_objets_location
 *     Identifiant de l'objet parent (si connu)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un objets_locations_detail source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du objets_locations_detail, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_objets_locations_detail_traiter_dist($id_objets_locations_detail = 'new', $id_objets_location = 0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$retours = formulaires_editer_objet_traiter('objets_locations_detail', $id_objets_locations_detail, $id_objets_location, $lier_trad, $retour, $config_fonc, $row, $hidden);
	return $retours;
}
