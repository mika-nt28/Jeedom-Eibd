Pour être au plus proche du KNX, le plugin peut se comporter comme un participant.
On peut donc configurer le plugin pour qu'il réalise des actions automatiquement.

=== Envoyer une valeur sur le bus. 
Vous avez sur jeedom un capteur qui n'est pas KNX, mais vous souhaiteriez le lier directement à votre réseau ?
Pour cela il suffit de configurer votre commande ainsi:

* Créer une commande de type "action"
* Saisir le GAD qui correspond à l'objet KNX que vous souhaitez mettre à jour
* Activer le Flag "Transmettre"
* En retour d'état allez chercher la commande de votre capteur.

=== Exécuter des actions lors de la mise à jour.

Vous avez un interrupteur KNX et vous voulez déclancher un scénario ou une commande jeedom ?
Pour cela il suffit de configurer votre commande ainsi:

* Créer une commande de type "info"
* Saisir le GAD qui correspond à l'objet KNX que vous souhaitez surveiller.
* Activer le flag "Ecriture"
* Saisir la liste des actions à mener.
* Ajouter le tag #value# dans les options des actions, qui sera remplacé par la valeur recu

=== Répondre à une commande "Read" en provenance du bus

Le plugin est capable de répondre à un interrogation du bus.
Pour cela il suffit de configurer votre commande ainsi:

* Créer une commande de type "info"
* Saisir le GAD qui correspond à l'objet KNX que vous souhaitez surveiller
* Activer le flag "Lecture"