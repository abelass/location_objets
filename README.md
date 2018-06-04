# Location d'objets

Gère la location d'objets

## Nécessite
- [saisies](https://plugins.spip.net/saisies.html)
- [verifier](https://plugins.spip.net/verifier.html)
- [prix_objets_periodes](https://github.com/abelass/prix_objets_periodes)
- [objets_disponibilites](https://github.com/abelass/objets_disponibilites)
- [facteur](https://plugins.spip.net/facteur.html)


## Configuration

### Location objets

- Gestion de statuts
- Gestion des notifications
- Définir les service extras

### SPIP
sous Configuration / Interactivité. (ecrire/?exec=configurer_interactions)

Activez :
- Accepter les inscriptions
- Accepter l’inscription de visiteurs du site public

## Utilisation

Prenez votre objet que vous aimeriez louer et ajoutez lui des prix avec les deux plugins
[prix_objets](https://plugins.spip.net/prix_objets.html) et [prix_objets_periodes](https://github.com/abelass/prix_objets_periodes)

### Dans vos squelettes placez la balise

	#FORMULAIRE_EDITER_OBJETS_LOCATION{#ENV{id_objets_location},#ENV{location_objet},#ENV{id_location_objet}, #ENV{options}}

Prenez l'example dans La fonction `squelettes/modeles/formulaire_location.html`

- la première variable `id_objets_location` est l'identifiant de la location
- la deuxième `location_objet`désigne l'objet à louer. Utilisez le nom complet de
	la table, donc par example pour l'objet espace `location_objet=spip_espaces`
- la troisième variable prend l'identifiant de l'objet à réserver
- la quatrième variable est un tableau avec toutes les variables à passer dans l'environnement
- les autres variables son les typiques d'un formulaire cvt de spip

### Noisette
utiliser la noisette `squelettes/content/location.html`en lui passant le variables
nécessaires.

### Modèle
vous pouvez également utiliser le modèle `formulaire_location` en y passant le variables
souhaitées.


## Les services extras
Il s'agit de tout service s'ajoutant à location de base. N'importe quel objet spip
peut servir comme service extra, il suffit de le définir dans la configuration et de
lui ajouter un prix, si nécessaire. Le choix dans la config peut être surchargé via
la variable `options` du formulaire en utilisant `location_extras_objets`, par example
	#FORMULAIRE_EDITER_OBJETS_LOCATION{
		#ENV{id_objets_location},
		#ENV{location_objet},
		#ENV{id_location_objet},
		#ARRAY{location_extras_objets=objets_service}}

# Examples

le plugin [location_immeubles](https://github.com/abelass/location_immeubles), propose
une location pour des immeubles/espaces




