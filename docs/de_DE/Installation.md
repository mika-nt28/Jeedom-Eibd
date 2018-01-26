=== Abhängigkeiten Installation 
Um die Einrichtung von Abhängigkeiten zu erleichtern, wird Jeedom nur die Installation der EIBD-Software-Suite verwalten.

Dans la cadre réservé aux dépendances, vous allez avoir le statut de l'installation.
Sie haben auch die Möglichkeit, das Installationprotokoll in Echtzeit anzuzeigen
Die Installation von EIBD kann lange dauern, abhängig von der Leistung der Maschine, auf der sie ausgeführt wird.
Achtung, die Zusammenstellung ist Ressourcen intensiv und kann Verlangsamungen in Ihrem Jeedom verursachen

image::../images/Installation_dependance.jpg[]

=== Konfiguration des Plugins und seine Abhängigkeiten
image::../images/eibd_screenshot_Configuration.jpg[]

Während oder nach der Installation von Abhängigkeiten können Sie das Plugin und EIBD Verbindung zu Ihrem Gateway konfigurieren.

* Geben Sie die IP-Adresse des Geräts ein, auf dem EIBD ausgeführt wird (Local 127.0.0.1).
* Geben Sie den EIBD-Verbindungsport an (Standard 6720)
* Geben Sie den Typ des Gateways an
* Geben Sie die Adresse des Gateways ein
* Passen Sie die physikalische Adresse des Daemon in Ihrem KNX Netzwerk an
* Wählen Sie, ob Ihre GADs 2 oder 3 Level haben
* Sie haben die Wahl, ob Jeedom Ihre Geräte und Befehle suchen und hinzufügen soll
* Vous avez le choix de laisser Jeedom interroger le bus pour initialiser les valeurs des informations
* Schließlich, vergessen Sie nicht zu speichern.

Sie können den Konfigurations- und Aktivierungsstatus der EIBD im  "Demon" Rahmen sehen

image::../images/Status_Demon.jpg[]
Wenn alle Lichter grün sind, können Sie weitermachen