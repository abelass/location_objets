<?php
/**
 * Gestion de modification des objets locations
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets\Actions
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
 * Appelle toutes les fonctions de modification d'un objet
 * $err est un message d'erreur eventuelle
 *
 * @param string $objet
 * @param int $id
 * @param array|null $set
 * @return mixed|string
 */
function objets_location_modifier($id_objets_location, $set = null) {
	include_spip('inc/config');
	$config = lire_config('location_objets');
	$objet = 'objets_location';

	$table_sql = table_objet_sql($objet);
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table($table_sql);
	if (!$desc or !isset($desc['field'])) {
		spip_log("Objet $objet inconnu dans objet_modifier", _LOG_ERREUR);

		return _L("Erreur objet $objet inconnu");
	}
	include_spip('inc/modifier');

	$champ_date = '';
	if (isset($desc['date']) and $desc['date']) {
		$champ_date = $desc['date'];
	} elseif (isset($desc['field']['date'])) {
		$champ_date = 'date';
	}

	$white = array_keys($desc['field']);
	// on ne traite pas la cle primaire par defaut, notamment car
	// sur une creation, id_x vaut 'oui', et serait enregistre en id_x=0 dans la base
	$white = array_diff($white, array($desc['key']['PRIMARY KEY']));

	if (isset($desc['champs_editables']) and is_array($desc['champs_editables'])) {
		$white = $desc['champs_editables'];
	}
	$c = collecter_requests(
		// white list
		$white,
		// black list
		array($champ_date, 'statut', 'id_parent', 'id_secteur'),
		// donnees eventuellement fournies
		$set
		);

	// Si l'objet est publie, invalider les caches et demander sa reindexation
	if (objet_test_si_publie($objet, $id_objets_location)) {
		$invalideur = "id='$objet/$id_objets_location'";
		$indexation = true;
	} else {
		$invalideur = "";
		$indexation = false;
	}

	if ($err = objet_modifier_champs($objet, $id_objets_location,
		array(
			'data' => $set,
			'nonvide' => '',
			'invalideur' => $invalideur,
			'indexation' => $indexation,
			// champ a mettre a date('Y-m-d H:i:s') s'il y a modif
			'date_modif' => (isset($desc['field']['date_modif']) ? 'date_modif' : '')
		),
		$c)
		) {
			return $err;
		}

		$date_debut = _request('date_debut');
		$date_fin = _request('date_fin');
		$_date_debut = strtotime($date_debut);
		$_date_fin = strtotime($date_fin);
		$nombre_jours = 0;
		$objets_extras = array_filter(explode(',', _request('objets_extras')));
		$new = _request('new');
		$prix_objet = test_plugin_actif('prix_objets') ? TRUE : FALSE;

		$location_objet = objet_type(_request('location_objet'));
		$id_location_objet = _request('id_location_objet');

		if ($_date_fin >= $_date_debut) {
			$difference = $_date_fin - $_date_debut;
			$nombre_jours = round($difference / (60 * 60 * 24)) + $fin;
		}

		$editer_objet = charger_fonction('editer_objet', 'action');
		// Création d'une location.
		if ($new) {
			$statut_defaut = $c['statut'] = isset($config['statut_defaut'])? $config['statut_defaut'] : 'attente';
			set_request('statut', $statut_defaut);
			// Enregistrement de l'objet de location
			$set = array(
				'id_objets_location' => $id_objets_location,
				'objet' => $location_objet,
				'id_objet' => $id_location_objet,
				'titre' => generer_info_entite($id_location_objet, $location_objet, 'titre'),
				'jours' => $nombre_jours,
				'statut' => $statut_defaut,
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

			// Enregistrement de des service extras
			if (isset($objet_location[0]) and
				$id_objets_locations_detail = $objet_location[0]) {
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
										'statut' => $statut_defaut,
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
		/* Modifier  les détails*/
		elseif ($date_debut and $date_fin) {
			$objet_location = sql_fetsel(
				'id_objets_locations_detail,objet,id_objet',
				'spip_objets_locations_details',
				'id_objets_location=' .$id_objets_location . ' AND id_objets_locations_detail_source=0');

			$id_source = $id_objets_locations_detail = $objet_location['id_objets_locations_detail'];
			$objet = $objet_location['objet'];
			$id_objet = $objet_location['id_objet'];

			$set = array(
				'jours' => $nombre_jours,
			);

			if ($objet != $location_objet or $id_objet != $id_location_objet) {
				$set = array_merge(
					$set,
					array(
						'objet' => $location_objet,
						'id_objet' => $id_location_objet,
						'titre' => generer_info_entite($id_location_objet, $location_objet, 'titre'),
						'jours' => $nombre_jours,
					)
					);
			}

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

			$objet_location = $editer_objet($id_objets_locations_detail, 'objets_locations_detail', $set);


			sql_delete(
				'spip_objets_locations_details',
				'id_objets_location=' .$id_objets_location . ' AND id_objets_locations_detail_source!=0');

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
								'statut' => $statut_defaut,
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

		// Modification de statut, changement de rubrique ?
		// FIXME: Ici lorsqu'un $set est passé, la fonction collecter_requests() retourne tout
		//         le tableau $set hors black liste, mais du coup on a possiblement des champs en trop.
		$c = collecter_requests(array($champ_date, 'statut', 'id_parent'), array(), $set);
		$err = objet_instituer($objet, $id_objets_location, $c);

		return $err;
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
	include_spip('inc/config');

	$config = lire_config('location_objets');
	$objet = 'objets_location';
	$table_sql = 'spip_objets_locations';
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table($table_sql);

	if (!$desc or !isset($desc['field'])) {
		return _L("Impossible d'instituer $objet : non connu en base");
	}

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
	$sel[] = 'id_auteur';

	$row = sql_fetsel($sel, $table_sql, id_table_objet($objet) . '=' . intval($id_objets_location));

	$id_rubrique = $row['id_rubrique'];
	$statut_ancien = $statut = $row['statut'];
	$date_ancienne = $date = $row['date'];

	$champs = array();

	$d = ($date and isset($c[$champ_date])) ? $c[$champ_date] : null;
	$s = (isset($desc['field']['statut']) and isset($c['statut'])) ? $c['statut'] : $statut;

	// cf autorisations dans inc/instituer_objet
	if ($s != $statut or ($d and $d != $date)) {
		if (autoriser('instituer', 'objetslocations', $id_objets_location, null, array('statut' => $s))
		) {
			$statut = $champs['statut'] = $s;
		} else {
			if ($s != 'publie' and autoriser('modifier', 'objetslocations', $id_objets_location)) {
				$statut = $champs['statut'] = $s;
			} else {
				spip_log("editer_objet $id_objets_location refus " . join(' ', $c));
			}
		}
	}
	//$statut = $champs['statut'] = $s;
	// Envoyer aux plugins
	$champs = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => $table_sql,
				'id_objet' => $id_objets_location,
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
	objet_editer_heritage($objet, $id_objets_location, $id_rubrique, $statut_ancien, $champs, $calcul_rub);

	// Changement de statut pour les détails.
	if($details) {
		foreach ($details as $detail) {
			$objet = $detail['objet'];
			$id_objet = $detail['id_objet'];
			$editer_objet($detail['id_objets_locations_detail'], 'objets_locations_detail', array('statut' => $statut));
		}
	}

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='$objet/$id_objets_location'");

	// Notifications
	$notifications = charger_fonction('objets_location_notifications', 'inc');
	$notifications($id_objets_location, $statut, $statut_ancien, $config, $row['id_auteur'], $date, $date_ancienne);


	return ''; // pas d'erreur
}
