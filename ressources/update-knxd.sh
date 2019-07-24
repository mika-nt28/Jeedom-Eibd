#!/bin/bash
touch /tmp/compilation_eibd_in_progress
echo 0 > /tmp/compilation_eibd_in_progress
sudo pkill knxd 
echo 10 > /tmp/compilation_eibd_in_progress
cd /usr/local/src/knxd
rm knxd*.deb
echo "*****************************************************************************************************"
echo "*                                      Installation de KnxD                                         *"
echo "*****************************************************************************************************"
cd knxd
git pull
echo 30 > /tmp/compilation_eibd_in_progress
dpkg-buildpackage -b -uc
echo 60 > /tmp/compilation_eibd_in_progress
cd ..
sudo dpkg -i knxd_*.deb knxd-tools_*.deb
echo 90 > /tmp/compilation_eibd_in_progress
sudo service knxd stop
sudo update-rc.d knxd disable
sudo chmod 777 /usr/bin/knxd
echo 100 > /tmp/compilation_eibd_in_progress
sudo rm /tmp/compilation_eibd_in_progress
echo "*****************************************************************************************************"
echo "*                                       Installation terminé                                        *"
echo "*****************************************************************************************************"
