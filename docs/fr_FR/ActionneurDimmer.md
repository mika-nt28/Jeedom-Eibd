==== Créer un équipement correspondant à votre dimmer KNX :

L'Adresse KNX ( elle doit être identique à votre actionneur dimmer )

Etablir un retour Etat 0/0/11 ( A adapter à votre configuration)

Attention au paramètre type "Numérique".

![introduction01](../images/Eibd_Exemple_Actionneur_dimmer.jpg)

Exemple de config ETS de l'actionneur dimmer :

Objet 0/0/11 Status Brightness value ( Retour d'Etat ) doit avoir au minimum les flags C R et T, comme ci-dessous ( de longeur 1 byte).

![introduction01](../images/Eibd_Exemple_ETS_dimmer.jpg)

==== Créer la commande pour dimmer votre lumière :

La commande Write brightness 5.001 0/0/10 doit avoir le paramètre "Slider" et comme retour état la commande créée précédemment "brightness value"

La commande Diming 3.007 est uniquement là pour que jeedom reconnaissance des variations faites depuis des interrupteurs poussoir KNX.

![introduction01](../images/Eibd_Exemple_dimmer.jpg)
