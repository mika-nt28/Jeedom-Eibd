==== Präsentation

Ce DPT permet l'emission ou réception des informations "Choix de tarif" et "Energie réactive". Il est utiliser pour récupérer l'index du compteur ainsi que les états HP/HC ...

Les valeurs renvoyé dans le tarif sont les suivantes :

image::../images/valeur_objet_tarif.PNG[]

==== Composition du DPT 235.001

Il est sur 6 octets découpé comme suit :

* Entier signé 32 bits Active energy measured in the tariff indicated in the field Tariff (13.010) (Wh)  
* Entier non signé 8 bits Tariff associated to the energy indicated in the field ActiveElectricalEnergy   
* Binaire 8 bits b0 =0 si Tarif valide b1=0 si Active energy valide le reste est réservé.

image::../images/presentation_dpt.PNG[]

==== Befehls Konfiguration

Créer un équipement (Lien vers doc)

Cliquer sur "Ajouter un commande knx" et completer la commande comme ci dessous.

image::../images/Commande_jeedom.PNG[]