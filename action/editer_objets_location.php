<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2017                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Gestion générique de modification des objets éditoriaux
 *
 * @package SPIP\Core\Edition
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Point d'entrée d'édition d'un objet
 *
 * On ne peut entrer que par un appel en fournissant $id et $objet
 * ou avec un argument d'action sécurisée de type "objet/id"
 *
 * @param int $id
 * @param string $objet
 * @param array $set
 * @return array
 */
function action_editer_objets_location_dist($id = null) {
	include_spip('action/editer_objet');
	$objet = 'objets_location';
	// appel direct depuis une url avec arg = "objet/id"
	if (is_null($id) or is_null($objet)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
		list($objet, $id) = array_pad(explode("/", $arg, 2), 2, null);
	}

	// appel incorrect ou depuis une url erronnée interdit
	if (is_null($id) or is_null($objet)) {
		include_spip('inc/minipres');
		echo minipres(_T('info_acces_interdit'));
		die();
	}

	// si id n'est pas un nombre, c'est une creation
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if (!$id = intval($id)) {
		// on ne sait pas si un parent existe mais on essaye
		$id_parent = _request('id_parent');
		$id = objet_inserer($objet, $id_parent);

		// On crée la référence.
		$fonction_reference = charger_fonction('locations_reference', 'inc/');
		$reference = $fonction_reference($id);
		set_request('reference', $reference);
	}

	if (!($id = intval($id)) > 0) {
		return array($id, _L('echec enregistrement en base'));
	}

	// Enregistre l'envoi dans la BD
	$err = objet_modifier($objet, $id, $set);

	return array($id, $err);
}


/**
 * Modifie le statut et/ou la date d'un objet
 *
 * @param string $objet
 * @param int $id_objets_location
 * @param array $c
 *   $c est un array ('statut', 'id_parent' = changement de rubrique)
 *   statut et rubrique sont lies, car un admin restreint peut deplacer
 *   un objet publie vers une rubrique qu'il n'administre pas
 * @param bool $calcul_rub
 * @return string
 */
function objets_location_instituer($id_objets_location, $c, $calcul_rub = true) {
	include_spip('inc/autoriser');
	include_spip('inc/rubriques');
	include_spip('inc/modifier');

	$objet = 'objets_location';
	$table_sql = 'spip_objets_locations';
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table($table_sql);
	if (!$desc or !isset($desc['field'])) {
		return _L("Impossible d'instituer $objet : non connu en base");
	}

	$new = _request('new');
	$prix_objet = test_plugin_actif('prix_objets') ? TRUE : FALSE;

	$location_objet = objet_type(_request('location_objet'));
	$id_location_objet = _request('id_location_objet');

	$sel = array();
	$sel[] = (isset($desc['field']['statut']) ? "statut" : "'' as statut");

	$champ_date = '';
	if (isset($desc['date']) and $desc['date']) {
		$champ_date = $desc['date'];
	} elseif (isset($desc['field']['date'])) {
		$champ_date = 'date';
	}

	$sel[] = ($champ_date ? "$champ_date as date" : "'' as date");
	$sel[] = (isset($desc['field']['id_rubrique']) ? 'id_rubrique' : "0 as id_rubrique");

	$row = sql_fetsel($sel, $table_sql, id_table_objet($objet) . '=' . intval($id_objets_location));

	$id_rubrique = $row['id_rubrique'];
	$statut_ancien = $statut = $row['statut'];
	$date_ancienne = $date = $row['date'];
	$date_debut = _request('date_debut');
	$date_fin = _request('date_fin');
	$_date_debut = strtotime($date_debut);
	$_date_fin = strtotime($date_fin);
	$nombre_jours = 0;

	if ($_date_fin >= $_date_debut) {
		$difference = $_date_fin - $_date_debut;
		$nombre_jours = round($difference / (60 * 60 * 24)) + $fin;
	}

	$champs = array();
	if ($new) {
		$editer_objet = charger_fonction('editer_objet', 'action');
		// Enregistrement de l'objet de location
		$set = array(
			'id_objets_location' => $id_objets_location,
			'objet' => $location_objet,
			'id_objet' => $id_location_objet,
			'titre' => generer_info_entite($id_location_objet, $location_objet, 'titre'),
			'jours' => $nombre_jours,
		);

		if ($prix_objet) {
			$prix_objet = TRUE;
			if ($prix_unitaire_ht = prix_par_objet(
				$location_objet,
				$id_location_objet,
				array(
					'date_debut' => $date_debut,
					'date_fin' => $date_fin,
				)
				)) {
					$set['prix_unitaire_ht'] = $prix_unitaire_ht;
					$prix_ttc = prix_par_objet(
						$location_objet,
						$id_location_objet,
						array(
							'date_debut' => $date_debut,
							'date_fin' => $date_fin,
						),
						'prix'
						);
					$set['taxe'] = $prix_ttc - $prix_unitaire_ht;
					$set['devise'] = devise_defaut_objet($id_location_objet, $location_objet);
					$set['prix_total'] = _request('prix_total');
				}
		}

		$objet_location = $editer_objet('oui', 'objets_locations_detail', $set);

		// Enregistrement de  des service extras
		if (isset($objet_location[0]) and
			$id_objets_locations_detail = $objet_location[0] and
			$objets_extras = array_filter(explode(',', _request('objets_extras')))) {

				foreach ($objets_extras as $table_extra) {
					$objet_extra = objet_type($table_extra);
					$set = array();
					if ($extras = _request('extras_' . $objet_extra) and is_array($extras)) {
						foreach($extras as $index => $id_extra) {
							if ($id_extra) {
								$set = array(
									'id_objets_location' => $id_objets_location,
									'id_objets_locations_detail_source' => $id_objets_locations_detail,
									'objet' => $objet_extra,
									'id_objet' => $id_extra,
									'titre' => generer_info_entite($id_extra, $objet_extra, 'titre'),
									'jours' => $nombre_jours,
								);
								if ($prix_objet) {
									$set['prix_unitaire_ht'] = prix_par_objet(
											$objet_extra,
											$id_extra,
											array(
												'date_debut' => $date_debut,
												'date_fin' => $date_fin,
											)
										);
									$prix_ttc = prix_par_objet(
										$objet_extra,
										$id_extra,
										array(
											'date_debut' => $date_debut,
											'date_fin' => $date_fin,
										),
										'prix'
										);
									$set['prix_total'] = _request('prix_total');
									$set['taxe'] = $prix_ttc - $set['prix_unitaire_ht'];
									$set['devise'] = devise_defaut_objet($id_extra, $objet_extra);
								}
								$editer_objet('oui', 'objets_locations_detail', $set);
							}
						}
					}
				}
			}

	}
	else {
		$details = sql_allfetsel(
			'id_objets_locations_detail,objet,id_objet',
			'spip_objets_locations_details',
			'id_objets_location=' .$id_objets_location);

		foreach ($details as $detail) {
			$objet = $detail['objet'];
			$id_objet = $detail['id_objet'];
			$set = array('jours' => $nombre_jours);

			if ($prix_objet) {
				$set['prix_unitaire_ht'] = prix_par_objet(
						$objet,
						$id_objet,
						array(
							'date_debut' => $date_debut,
							'date_fin' => $date_fin,
						)
						);
				$prix_ttc = prix_par_objet(
						$objet,
						$id_objet,
						array(
							'date_debut' => $date_debut,
							'date_fin' => $date_fin,
						),
						'prix'
						);
				$set['prix_total'] = _request('prix_total');
				$set['taxe'] = $prix_ttc - $set['prix_unitaire_ht'];
				$set['devise'] = devise_defaut_objet($id_objet, $objet);
			}
			sql_updateq(
				'spip_objets_locations_details',
				$set,
				'id_objets_locations_detail=' . $detail['id_objets_locations_detail']);
		}
	}


	$d = ($date and isset($c[$champ_date])) ? $c[$champ_date] : null;
	$s = (isset($desc['field']['statut']) and isset($c['statut'])) ? $c['statut'] : $statut;

	// cf autorisations dans inc/instituer_objet
	if ($s != $statut or ($d and $d != $date)) {
		if ($id_rubrique ?
			autoriser('publierdans', 'rubrique', $id_rubrique)
			:
			autoriser('instituer', $objet, $id_objets_location, null, array('statut' => $s))
		) {
			$statut = $champs['statut'] = $s;
		} else {
			if ($s != 'publie' and autoriser('modifier', $objet, $id_objets_location)) {
				$statut = $champs['statut'] = $s;
			} else {
				spip_log("editer_objet $id_objets_location refus " . join(' ', $c));
			}
		}

		// En cas de publication, fixer la date a "maintenant"
		// sauf si $c commande autre chose
		// ou si l'objet est deja date dans le futur
		// En cas de proposition d'un objet (mais pas depublication), idem
		/*if ($champ_date) {
			if ($champs['statut'] == 'publie'
				or ($champs['statut'] == 'prop' and !in_array($statut_ancien, array('publie', 'prop')))
				or $d
			) {
				if ($d or strtotime($d = $date) > time()) {
					$champs[$champ_date] = $date = $d;
				} else {
					$champs[$champ_date] = $date = date('Y-m-d H:i:s');
				}
			}
		}*/
	}

	// Verifier que la rubrique demandee existe et est differente
	// de la rubrique actuelle
	/*if ($id_rubrique
		and isset($c['id_parent'])
		and $id_parent = $c['id_parent']
		and $id_parent != $id_rubrique
		and (sql_fetsel('1', "spip_rubriques", "id_rubrique=" . intval($id_parent)))
	) {
		$champs['id_rubrique'] = $id_parent;

		// si l'objet etait publie
		// et que le demandeur n'est pas admin de la rubrique
		// repasser l'objet en statut 'propose'.
		if ($statut == 'publie'
			and !autoriser('publierdans', 'rubrique', $id_rubrique)
		) {
			$champs['statut'] = 'prop';
		}
	}*/

	spip_log($champs, 'teste');
	// Envoyer aux plugins
	$champs = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => $table_sql,
				'id_objet' => $id,
				'action' => 'instituer',
				'statut_ancien' => $statut_ancien,
				'date_ancienne' => $date_ancienne,
				'id_parent_ancien' => $id_rubrique,
			),
			'data' => $champs
		)
	);

	if (!count($champs)) {
		return '';
	}

	// Envoyer les modifs.
	objet_editer_heritage($objet, $id, $id_rubrique, $statut_ancien, $champs, $calcul_rub);

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='$objet/$id'");

	/*
	if ($date) {
		$t = strtotime($date);
		$p = @$GLOBALS['meta']['date_prochain_postdate'];
		if ($t > time() AND (!$p OR ($t < $p))) {
			ecrire_meta('date_prochain_postdate', $t);
		}
	}*/

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => $table_sql,
				'id_objet' => $id,
				'action' => 'instituer',
				'statut_ancien' => $statut_ancien,
				'date_ancienne' => $date_ancienne,
				'id_parent_ancien' => $id_rubrique,
			),
			'data' => $champs
		)
	);

	// Notifications
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications("instituer$objet", $id,
			array('statut' => $statut, 'statut_ancien' => $statut_ancien, 'date' => $date, 'date_ancienne' => $date_ancienne)
		);
	}

	return ''; // pas d'erreur
}
