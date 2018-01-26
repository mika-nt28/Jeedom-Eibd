==== Flag Communication

* Actif : Cet objet de communication peut interagir avec le bus (lire,
écrire, etc ...), si un télégramme du bus correspond à cet objet (=
l'objet est lié à l'adresse de groupe de destination du télégramme),
le participant répondra sur le bus avec ACK, NACK ou BUSY selon ce
qu'il convient.
* Inactif : Si un télégramme du bus correspond à cet objet (= l'objet
est lié à l'adresse de groupe de destination du télégramme), le
participant répondra sur le bus avec ACK, NACK ou BUSY selon ce qu'il
convient, MAIS la valeur de l'objet n'est pas modifiée ni transmise,
quoi qu'il arrive.

Ce flag est quasiment toujours "Actif", sinon l'objet ne sert à
rien ...
Ce flag est néanmoins utile durant la phase d'installation /
configuration d'une installation, quand on veut préparer la config de
certain participants mais qu'ils ne doivent pas encore interagir avec
le bus ; ce flag peut aussi être utile pour désactiver certain objets
sans modifier toute leur config, dans le cadre d'une recherche
d'erreur par exemple.


==== Flag  Lecture / Read

* Actif : Si le participant voit sur le bus un télégramme de type
"Lecture de la valeur" qui correspond à cet objet (= l'objet est lié à
l'adresse de groupe de destination du télégramme) alors le participant
va répondre en envoyant sur le bus la valeur actuelle de l'objet.
* Inactif : Le participant ne réagira à aucun télégramme de type
"Lecture de la valeur" qui correspond à cet objet.

Pour chaque adresse de groupe, au maximum UN seul objet doit avoir son
flag "Lecture/Read" actif, tous les autre objet de cette même adresse
de groupe doivent être inactifs, sinon une interrogation de la valeur
donnerait plus d'une réponse et on pourrait même obtenir des réponses
discordantes.

Exemples d'objets pour lesquels le flag "Lecture/Read" est
généralement actif :
* L'objet de commande d'une sortie Tout-ou-Rien (sur un bloc 4
sorties, par exemple).
* L'éventuel objet de "feed-back" de la ligne précédente.
* Tous les objets de "feed-back" en général.
* Les objets représentant la valeur mesurée par un capteur (luminosité
actuelle, température réelle mesurée, état (ouvert/fermé) d'un capteur
du style reed-relais dans une porte ou une fenêtre, ...)

Exemples d'objets pour lesquels le flag "Lecture/Read" est
généralement INACTIF :
* L'objet (ON/OFF) d'un bouton poussoir.

En général, la valeur stockée ou utilisée par les objets faisant
partie d'une même adresse de groupe représente une information
correspondant à quelque chose de réel / physique / mesurable dans
votre maison.
Pour déterminer lequel de tous les objets faisant partie de la même
adresse de groupe doit être celui qui aura son flag "Lecture/Read"
actif, il faut vous demander lequel de tous ces objets a le plus de
chance d'être en phase avec la réalité.
Cas simple : 3 boutons poussoirs et un acteur qui allume ou éteint un
lampe, la valeur de l'objet de l'acteur a de bien plus grandes chances
de réellement représenter l'état (allumé ou éteint) de la lampe,
surtout après une panne de courent ou un problème sur le bus ...


==== Flag  Ecriture / Write

* Actif : La valeur de cet objet sera modifiée si un participant
envoie sur le bus un télégramme de type "Ecriture de la valeur" qui
correspond à cet objet (= l'objet est lié à l'adresse de groupe de
destination du télégramme).
* Inactif : La valeur de cet objet NE sera PAS modifiée, même si un
participant envoie sur le bus un télégramme de type "Ecriture de la
valeur" qui correspond à cet objet.


Pour une valeur d'adresse de groupe, plusieurs objets peuvent avoir
leur flag "Ecriture/Write" actif.
N'importe quel objet dont la valeur doit pouvoir être modifiée par un
autre doit avoir sun flag "Ecriture/Write" actif.

Exemples d'objets pour lesquels le flag "Ecriture/Write" est
généralement actif :
* L'objet de commande d'une sortie Tout-ou-Rien (sur un bloc 4
sorties, par exemple).
* L'objet (ON/OFF) d'un bouton poussoir.
* En général, tous les objets d'une supervision.

Exemples d'objets pour lesquels le flag "Ecriture/Write" est
généralement INACTIF :
* Tous les objets de "feed-back" (d'acteurs) en général.
* Les objets représentant la valeur mesurée par un capteur (luminosité
actuelle, température réelle mesurée, état (ouvert/fermé) d'un capteur
du style reed-relais dans une porte ou une fenêtre, ...).



==== Flag  Transmission/Transmit

* Actif : Si pour une raison quelconque (sauf la réception d'un
télégramme « Ecriture/Write » vers cet objet) la valeur de cet objet
venait à être modifiée, le participant va envoyer sur le bus un
télégramme de type "Ecriture de la valeur" contenant la nouvelle
valeur de l'objet, vers la première adresse de groupe liée à cet
objet.
* Inactif : Le participant n'envoie aucun télégramme sur le bus quand
la valeur de l'objet est modifiée.

Exemples d'objets pour lesquels le flag "Transmission/Transmit" est
généralement actif.
Ce flag est généralement actif pour tous les objets ayant une
information à envoyer sur le bus, c-à-d :
* Tous les capteurs de grandeurs physiques (température, luminosité,
voltage, wattage, courent, humidité, ...) doivent envoyer sur le bus un
télégramme chaque fois que la valeur qu'ils mesurent s'écarte de la
mesure précédente.
* L'objet ON/OFF des boutons poussoirs (quand on pousse dessus, ils
doivent bien envoyer l'info sur le bus ...).

* Tous les objets de "feed-back" (d'acteurs) en général.

Exemples d'objets pour lesquels le flag "Transmission/Transmit" est
généralement inactif.

* L'objet de commande d'une sortie Tout-ou-Rien (sur un bloc 4
sorties, par exemple).
* En général, tous les objets d'une supervision.


Pour rappel : Un objet peut être lié à plusieurs adresses de groupe,
il « recevra » les télégrammes destinés à ces diverses adresses de
groupes MAIS il ne pourra envoyer sa valeur (suite à un flag «
transmit » actif) que vers UNE SEULE adresse de groupe (la première de
la liste.


==== Flag  Mise-à-jour/Update

* Actif : Si un autre participant répond à un télégramme de type

"Lecture de la valeur" qui correspond à cet objet (= l'objet est lié à
l'adresse de groupe de destination du télégramme) en envoyant une
valeur différente de celle actuellement stockée dans l'objet, la
valeur de l'objet est remplacée par celle lue sur le bus dans le
télégramme de réponse. (= Les télégrammes de réponse de valeur sont
interprétés comme instruction d'écriture).
* Inactif : Le participant ne modifie pas la valeur de son objet tant
qu'il ne reçoit pas un télégramme "Ecriture/Write".

En théorie, ce flag ne semble pas très utile, mais en pratique, si il
est actif il permet de "re-synchroniser" plus rapidement tous les
participants d'un bus quand certains ont été redémarrés ou qu'une
coupure est survenue sur le bus (arrêt temporaire d'une liaison entre
2 étages ou 2 bâtiments par exemple), dans ce cas, il suffit de lancer
un script qui lit touts les groupes et hop tout est resynchronisé.

Exemples d'objets pour lesquels le flag "Mise-à-jour/Update" est
généralement actif :
* Tous les objets qui ont le flag "Lecture/Read" inactif.

* En général, tous les objets d'une supervision.

Exemples d'objets pour lesquels le flag "Mise-à-jour/Update" est
généralement inactif :
* Tous les objets qui ont le flag "Lecture/Read" actif.

Il existe encore un flag supplémentaire, il n'est pas présent dans
beaucoup de participants aujourd'hui mais devrait tout doucement se
généraliser je pense, au moins sur les modules de supervision.

==== Flag Read-on-Init

* Actif : Au démarrage du participant, un télégramme de type "Lecture
de la valeur" qui correspond à cet objet sera envoyé sur le bus de
donner à cet objet une valeur initial correcte.
* Inactif : Au démarrage du participant, cet objet recevra une valeur
par défaut.


Exemples d'objets pour lesquels le flag "Read-on-Init" est
généralement actif :
* Tous les objets qui ont le flag "Lecture/Read" inactif.

* En général, tous les objets d'une supervision.

Exemples d'objets pour lesquels le flag "Read-on-Init" est
généralement inactif :
* Tous les objets qui ont le flag "Lecture/Read" actif.

Etude d'un cas particulier : L'objet "Décalage de la consigne de base"
sur un thermostat de type Gira SmartSensor.

Sur cet objet, faut-il activer les flags suivants ?

* COMMUNICATION : oui, c'est évident si on veut que cela marche.
* READ : oui, car le lieu principal de stockage de l'information est
le thermostat lui-même, donc le SmartSensor.
* WRITE : oui, car le but est de pouvoir modifier le décalage à partir
du bus (un Gira HomeServer 3 par ex.)
* TRANSMIT : non, cet objet ne se modifie pas "de lui-même".
Attention, pour "transmit", ce serait le contraire si on utilisait un
Theben RAM713 qui possède lui une molette de décalage manuel.
* UPDATE : non, "read" est actif, donc cet objet est la source
d'information la plus fiable.
(Car c'est le SmartSensor qui contient la valeur par défaut à utiliser
lors d'un reset général du bus).
* READ-ON-INIT : non, pour les mêmes raisons que "Update".