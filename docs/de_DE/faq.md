[panel,primary]
.Wie wird ein Befehl erstellt, um das Licht anzuschalten, obwohl ich physisch keinen Schalter habe?  
--
Unter Jeedom können wir virtuelle Schalter erstellen, indem Sie einen Befehl der Art Aktion konfigurieren.
Die wichtigen Elemente, um Informationen über den Bus mit jeedom zu senden, sind :

* Gruppen Adresse
* Die TPD, für seine Codierung
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