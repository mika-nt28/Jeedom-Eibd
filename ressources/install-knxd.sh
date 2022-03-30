#!/bin/bash
echo "*****************************************************************************************************"
echo "*                                         Remove eibd                                               *"
echo "*****************************************************************************************************"
sudo rm -R /usr/local/bin/{eibd,knxtool,group*} /usr/local/lib/lib{eib,pthsem}*.so* /usr/local/include/pth*
sudo rm -R /var/log/knxd.log
cd /usr/local/bin/
echo "*****************************************************************************************************"
echo "*                                      Installation de KnxD                                         *"
echo "*****************************************************************************************************"
cd /usr/local/src/
if [ -d "knxd" ]
then
  sudo rm -R knxd
fi
sudo mkdir /usr/local/src/knxd
cd knxd
sudo git clone https://github.com/knxd/knxd.git
cd knxd
sudo git checkout debian
sudo dpkg-buildpackage -b -uc -d
cd /usr/local/src/knxd
sudo dpkg -i knxd_*.deb knxd-tools_*.deb
sudo usermod -a -G dialout knxd
echo "*****************************************************************************************************"
echo "*                                       Installation terminé                                        *"
echo "*****************************************************************************************************"
