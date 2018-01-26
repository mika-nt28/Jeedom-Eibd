
Nommée votre commande de manière a la retrouve facilement dans Jeedom

==== KNX Konfiguration

Diese Konfiguration sind wichtig für die Kommunikation 
* Data Point Type : ce champs est important et indispensable pour l'encodage et décodage de la valeur.
* Groupe d'addresse : ce champs identifi la commande sur le bus et sur jeedom

image::../images/Configuration_commande_knx.jpg[]

====== Flag

include::Flag.asciidoc[]

image::../images/Configuration_commande_flag.jpg[]

====== Wert
* Ajouter une Action: Permet de cree une liste d'action a mené lorsque le bus-monitor vois passer le gad (si le flag Ecrire est actif)
* Retour d'état : Ce paramètre est visible pour une commande de type action, elle permet a jeedom de liée une info a une action
* Valeur : Imposer une valeur a votre commande (lorsque l'on est en type action)
* Inverser : Cette commande permet d'inverser la valeur 

image::../images/Configuration_commande_valeur.jpg[]

====== Parameter
* Type : Selectionez le type de commande
* Sous type automatique : Laissez le plugin choisir le sous-type le plus adapté a votre DPT
* Sous Type : Choisissez le sous type le plus adaptée a la valeur transmis ou a transmettre
* Visible : Permet de rendre visible votre commande sur le dashboard
* Historiser : Permet d'enregistrer la valeur

Enfin pensez sauvegarder.