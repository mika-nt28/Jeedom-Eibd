[panel,primary]
. How to create a command to turn on the light as I have no real switch?  
--
Sous jeedom, nous pouvons créer des interrupteurs virtuels en configurant une commande de type action.
Les éléments importants pour envoyer des informations sur le bus avec jeedom sont :

* Adresse de groupe
* Le DPT pour son encodage
On verra apparaitre sur le bus monitor la commande envoyée avec l'adresse physique d'eibd
--
[panel,primary]
.Je n'arrive pas a émetre une information avec ma passerelle Hager th102 ?
--
Le script de démarage fonctionne mal avec cette passerelle.
Il faut utiliser cette ligne pour lancer eibd
[source,]
----
eibd -D -S -T -t1023 -i usb:1:6:1:0:0 -e 1.1.128 -R -u
----
--