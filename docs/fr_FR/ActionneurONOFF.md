==== Créer un équipement correspondant à votre actionneur KNX :

L'Adresse KNX ( elle doit être identique à votre actionneur )
Notre retour d'état : La Groupe Adresse choisie dans l'exemple : 0/1/0 ( A adapter à votre configuration).

![introduction01](../images/Eibd_Exemple_ListenerONOFF.jpg)

Exemple de config ETS de l'actionneur :
Objet status ( Retour d'Etat ) doit avoir au minimum les flags C R et T, comme ci-dessous.

![introduction01](../images/Eibd_Exemple_ETS_actionneur_onoff.jpg)

==== Créer un équipement qui se comportera comme un intérrupteur KNX :

son Adresse KNX ( elle peut être identique à un vrai intérrupteur KNX qui remplira les même fonctions)

Dans l'exemple ci-dessous : l'actionneur devra réagir sur l'adresse de groupe 0/0/1 ( A adapter à votre configuration)

Lumière ON/OFF est notre action qui fera la bascule (visible coché : sera affiché sur le dashboard)

(Optionel) Lumière ON est une commande qui impose ON (visible décoché : utilisé uniquement via un scénario)

(Optionel) Lumière OFF est une commande qui impose OFF (visible décoché : sert uniquement via un scénario)

Important : Ne pas oublier de choisir dans le champs Retour d'Etat la commande crée précédemment.

![introduction01](../images/Eibd_Exemple_LumONOFF.jpg)
