If you have enabled automatic addition or ETS4 import, the equipments and commands have been created, but certain parameters remain to be configured.
The rest of this paragraph will be useful.

=== Équipement

Dans un premier temps, il faut créer un nouvelle équipement et le nommé.
Comme dans tous les plugins Jeedom vous avez un bouton ajouté un equipement sur la gauche de votre fenetre.

image::../images/Configuration_equipement.jpg[]

Ce nouvelle équipement a besoin d'être paramétré.

* Nom de l'équipement KNX : Le nom a déjà été paramétrée mais vous avez la possibilité de la changer
* Adresse Physique de l'équipement : cette element n'est pas tres important et peut etre laissé vide
* Objet parent : Ce paramétré permet d'ajouter l'équipement dans un objet Jeedom
* Catégorie : Déclare l'équipement dans une catégorie
* Visible : Permet de rendre l'équipement visible dans le Dashboard
* Activer : Permet d'activer l'équipement
* Délai max entre 2 messages: ce champs permet est utile pour les equipements qui fonctionne sur batterie, il indique a jeedom le delais qu'il doit laissé entre 2 messages avant de vous informé d'un risque de panne.

=== Commande

Maintenant que votre équipement est crée et configurée, on vas pouvoir y ajouter des commandes.

Settings examples

image::../images/Configuration_commande.jpg[]

==== Ajout des commandes par template

include::cmdTemplate.asciidoc[]

==== Ajout des commandes manuelement

include::cmdManual.asciidoc[]
