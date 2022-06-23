#!/bin/bash
echo "*****************************************************************************************************"
echo "*                                         Remove eibd                                               *"
echo "*****************************************************************************************************"
cd /usr/local/src/
if [ -d "eibd" ]
then
  sudo rm -R eibd
fi
sudo rm -R /usr/local/bin/{eibd,knxtool,group*} /usr/local/lib/lib{eib,pthsem}*.so* /usr/local/include/pth*
sudo rm -R /var/log/knxd.log
echo "*****************************************************************************************************"
echo "*                                         Remove knxd                                               *"
echo "*****************************************************************************************************"
sudo sytemctl enable knx.service
sudo sytemctl enable knx.socket
sudo sytemctl status knx.service
sudo sytemctl status knx.socket
sudo apt-get autoremove -y knxd
sudo apt-get purge -y knxd
sudo rm /etc/knxd.conf
sudo rm /etc/knxd.ini
sudo rm -R /usr/lib/knxd
sudo rm -R /usr/local/src/knxd
