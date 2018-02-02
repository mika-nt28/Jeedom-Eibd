Si vous avez activé l'ajout automatique ou l'import ETS4, les equipements et les commandes ont été créer, mais il reste certain parametre a configurer.
La suite de ce paragraphe va vous etre utiles.

=== Équipement

Dans un premier temps, il faut créer un nouvelle équipement et le nommé.
Comme dans tous les plugins Jeedom vous avez un bouton ajouté un equipement sur la gauche de votre fenetre.

![introduction01](../images/Configuration_equipement.jpg)

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

Exemple de configuration

![introduction01](../images/Configuration_commande.jpg)

==== Ajout des commandes par template

include::cmdTemplate.md[]

==== Ajout des commandes manuelement

include::cmdManual.md[]

