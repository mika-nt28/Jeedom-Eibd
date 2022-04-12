#!/bin/bash
echo "*****************************************************************************************************"
echo "*                                         Remove eibd                                               *"
echo "*****************************************************************************************************"
sudo rm -R /usr/local/bin/{eibd,knxtool,group*} /usr/local/lib/lib{eib,pthsem}*.so* /usr/local/include/pth*
sudo rm -R /var/log/knxd.log
echo 10 > /tmp/jeedom_install_in_progress_eibd
echo "*****************************************************************************************************"
echo "*                                         Remove knxd                                               *"
echo "*****************************************************************************************************"
suso systemctl stop knxd.socket
suso systemctl stop knxd.service
sudo dpkg --purge knxd
sudo dpkg --remove knxd
sudo apt-get autoremove -y knxd
echo 20 > /tmp/jeedom_install_in_progress_eibd
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
echo 30 > /tmp/jeedom_install_in_progress_eibd
cd knxd
sudo git checkout debian
sudo dpkg-buildpackage -b -uc -d
echo 60 > /tmp/jeedom_install_in_progress_eibd
cd /usr/local/src/knxd
sudo dpkg -i knxd_*.deb knxd-tools_*.deb
echo 90 > /tmp/jeedom_install_in_progress_eibd
sudo usermod -a -G dialout knxd
echo "*****************************************************************************************************"
echo "*                                       Installation terminé                                        *"
echo "*****************************************************************************************************"
