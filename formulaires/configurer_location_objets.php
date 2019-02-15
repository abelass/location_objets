<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;

function formulaires_configurer_location_objets_saisies_dist() {
	include_spip('inc/config');
	include_spip('inc/plugin');

	$liste_objets = lister_tables_objets_sql('spip_objets_locations');
	$statuts = array();
	$statuts_selectionnees = array();
	$config = lire_config('location_objets', array());
	$quand = isset($config['quand']) ? $config['quand'] : array();

	//Le statuts du plugin, sauf en cours
	foreach ($liste_objets['statut_textes_instituer'] AS $statut => $label) {
			$statuts[$statut] = _T($label);
		if (in_array($statut, $quand))
			$statuts_selectionnees[$statut] = _T($label);
	}


	$choix_expediteurs = array(
		'webmaster' => _T('location_objets:notifications_expediteur_choix_webmaster'),
		'administrateur' => _T('location_objets:notifications_expediteur_choix_administrateur'),
		'email' => _T('location_objets:notifications_expediteur_choix_email')
	);

	if (defined('_DIR_PLUGIN_FACTEUR')) {
		$choix_expediteurs['facteur'] = _T('location_objets:notifications_expediteur_choix_facteur');
	}

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_parametres',
				'label' => _T('location_objets:cfg_titre_parametrages')
			),

			'saisies' => array(
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'statut_defaut',
						'datas' => $statuts,
						'defaut' => 'valide',
						'cacher_option_intro' => 'on',
						'label' => _T('location_objets:label_statut_defaut'),
						'defaut' => $config['statut_defaut']
					)
				),
				/*array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'duree_vie',
						'label' => _T('location_objets:duree_vie_label'),
						'explication' => _T('location_objets:duree_vie_explication',
								array(
									'statut_defaut' => $statuts[$config['statut_defaut']]
								)
							),
						'defaut' => $config['duree_vie'],
					)
				),*/
				//$poubelle_duree,
			)
		),
		array (
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'formulaire',
				'label' => _T('location_objets:cfg_titre_formulaire'),
			),
			'saisies' => array(
				array(
					'saisie' => 'choisir_objets',
					'options' => array(
						'nom' => 'location_extras_objets',
						'label' => _T('location_objets:champ_location_extras_objets_label'),
						'defaut' => $config['location_extras_objets'],
					),
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'entite_duree',
						'label' => _T('location_objets:champ_entite_duree_label'),
						'explication' => _T('location_objets:explication_entite_duree'),
						'data' => [
							'jour' => _T('ecrire:jours'),
							'nuit' => _T('dates_outils:nuits'),
						],
						'defaut' => $config['entite_duree'],
					),
				),
			),
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_notifications',
				'label' => _T('location_objets:notifications_cfg_titre')
			),
			'saisies' => array(
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'exp1',
						'texte' => _T('location_objets:notifications_explication')
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'activer',
						'label' => _T('location_objets:notifications_activer_label'),
						'explication' => _T('location_objets:notifications_activer_explication'),
						'defaut' => $config['activer']
					)
				),
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_notifications_parametres',
				'label' => _T('location_objets:notifications_parametres'),
				'afficher_si' => '@activer@ == "on"',
			),
			'saisies' => array(
				array(
					'saisie' => 'selection_multiple',
					'options' => array(
						'nom' => 'quand',
						'label' => _T('location_objets:notifications_quand_label'),
						'explication' => _T('location_objets:notifications_quand_explication'),
						'cacher_option_intro' => 'on',
						'datas' => $statuts,
						'defaut' => $config['quand']
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'expediteur',
						'label' => _T('location_objets:notifications_expediteur_label'),
						'explication' => _T('location_objets:notifications_expediteur_explication'),
						'cacher_option_intro' => 'on',
						'defaut' => $config['expediteur'],
						'datas' => $choix_expediteurs
					)
				),

				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'expediteur_webmaster',
						'label' => _T('location_objets:notifications_expediteur_webmaster_label'),
						'statut' => '0minirezo',
						'cacher_option_intro' => "on",
						'webmestre' => 'oui',
						'defaut' => $config['expediteur_webmaster'],
						'afficher_si' => '@expediteur@ == "webmaster"',
					)
				),
				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'expediteur_administrateur',
						'label' => _T('location_objets:notifications_expediteur_administrateur_label'),
						'statut' => '0minirezo',
						'cacher_option_intro' => "on",
						'defaut' => $config['expediteur_administrateur'],
						'afficher_si' => '@expediteur@ == "administrateur"',
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'expediteur_email',
						'label' => _T('location_objets:notifications_expediteur_email_label'),
						'defaut' => $config['expediteur_email'],
						'afficher_si' => '@expediteur@ == "email"',
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'vendeur',
						'label' => _T('location_objets:notifications_destinataire_label'),
						'explication' => _T('location_objets:notifications_destinataire_explication'),
						'cacher_option_intro' => 'on',
						'defaut' => $config['vendeur'],
						'datas' => array(
							'webmaster' => _T('location_objets:notifications_vendeur_choix_webmaster'),
							'administrateur' => _T('location_objets:notifications_vendeur_choix_administrateur'),
							'email' => _T('location_objets:notifications_vendeur_choix_email')
						)
					)
				),
				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'vendeur_webmaster',
						'label' => _T('location_objets:notifications_vendeur_webmaster_label'),
						'statut' => '0minirezo',
						'cacher_option_intro' => "on",
						'webmestre' => 'oui',
						'multiple' => 'oui',
						'defaut' => $config['vendeur_webmaster'],
						'afficher_si' => '@vendeur@ == "webmaster"',
					)
				),
				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'vendeur_administrateur',
						'label' => _T('location_objets:notifications_vendeur_administrateur_label'),
						'statut' => '0minirezo',
						'multiple' => 'oui',
						'cacher_option_intro' => "on",
						'defaut' => $config['vendeur_administrateur'],
						'afficher_si' => '@vendeur@ == "administrateur"',
					)
				),

				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'vendeur_email',
						'label' => _T('location_objets:notifications_vendeur_email_label'),
						'explication' => _T('location_objets:notifications_vendeur_email_explication'),
						'defaut' => $config['vendeur_email'],
						'afficher_si' => '@vendeur@ == "email"',
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'client',
						'label' => _T('location_objets:notifications_client_label'),
						'explication' => _T('location_objets:notifications_client_explication'),
						'defaut' => $config['client'],
					)
				),
			)
		),
		/*array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_cron',
				'label' => _T('location_objets:formulaire_public')
			),
			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'enregistrement_inscrit',
						'label' => _T('location_objets:label_enregistrement_inscrit'),
						'explication' => _T('location_objets:explication_enregistrement_inscrit'),
						'defaut' => $config['enregistrement_inscrit'],
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'enregistrement_inscrit_obligatoire',
						'label' => _T('location_objets:label_enregistrement_inscrit_obligatoire'),
						'defaut' => $config['enregistrement_inscrit_obligatoire'],
						'afficher_si' => '@enregistrement_inscrit@ == "on"',
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'email_reutilisable',
						'label' => _T('location_objets:label_email_reutilisable'),
						'explication' => _T('location_objets:explication_email_reutilisable'),
						'defaut' => $config['email_reutilisable'],
						'afficher_si' => '@enregistrement_inscrit_obligatoire@ == ""',
					)
				),
			)
		)*/
	);
}
