Um näher am KNX zu sein, kann sich das Plugin wie ein Teilnehmer verhalten.
Sie können das Plugin so konfigurieren, dass es automatisch Aktionen ausführt.

=== Senden Sie einen Wert auf dem Bus. 
Sie haben in Jeedom einen Sensor, der kein KNX ist, aber Sie möchten ihn direkt mit Ihrem Netzwerk verbinden?
Konfigurieren Sie dazu einfach Ihren Befehl wie folgt:

* Erstellen Sie einen Befehl des Typs "Aktion"
* Geben Sie die GAD ein, die dem KNX-Objekt entspricht, das Sie aktualisieren möchten
* Aktivieren Sie das Flag "Übertragen"
* Als Antwort auf den Status erhalten Sie den Befehl Ihres Sensors.

=== Exécuter des actions lors de la mise à jour.

Vous avez un interrupteur KNX et vous voulez déclancher un scénario ou une commande jeedom ?
Konfigurieren Sie dazu einfach Ihren Befehl wie folgt:

* Créer une commande de type "info"
* Saisir le GAD qui correspond à l'objet KNX que vous souhaitez surveiller.
* Activer le flag "Ecriture"
* Saisir la liste des actions à mener.
* Ajouter le tag #value# dans les options des actions, qui sera remplacé par la valeur recu

=== Répondre à une commande "Read" en provenance du bus

Le plugin est capable de répondre à un interrogation du bus.
Konfigurieren Sie dazu einfach Ihren Befehl wie folgt:

* Créer une commande de type "info"
* Saisir le GAD qui correspond à l'objet KNX que vous souhaitez surveiller
* Activer le flag "Lecture"