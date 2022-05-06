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
cd /usr/local/src/
if [ -d "knxd" ]
then
  sudo rm -R knxd
fi
sudo apt-get autoremove -y knxd
sudo apt-get purge -y knxd
