#!/bin/bash
touch /tmp/compilation_eibd_in_progress
echo 0 > /tmp/compilation_eibd_in_progress
pkill eibd  
pkill knxd  
# Check for root priviledges
if [ $(id -u) != 0 ]
then
   echo "Superuser (root) priviledges are required to install eibd"
   echo "Please do 'sudo -s' first"
   exit 1
fi
if [ -f "/etc/eibd/pthsem_VERSION" ]
then
  echo "*****************************************************************************************************"
  echo "*                              Remove PTHSEM V2.0.8 libraries                                       *"
  echo "*****************************************************************************************************"
  sudo rm /etc/eibd/pthsem_VERSION
  sudo echo $LD_LIBRARY_PATH
  sudo export LD_LIBRARY_PATH="/usr/local/lib"
  sudo ldconfig 
fi
echo 5 > /tmp/compilation_eibd_in_progress
echo "*****************************************************************************************************"
echo "*                              Remove BCUSDK V0.0.5 libraries                                       *"
echo "*****************************************************************************************************"
sudo rm bcusdk-0.0.5 
sudo rm /etc/eibd/bcusdk_VERSION
echo 10 > /tmp/compilation_eibd_in_progress
echo "*****************************************************************************************************"
echo "*                                         Remove eibd                                               *"
echo "*****************************************************************************************************"
if [ -f "/etc/logrotate.d/eibd" ]
then
  sudo rm /etc/logrotate.d/eibd
fi
if [ -f "/etc/default/eibd" ]
then
  sudo rm /etc/default/eibd
fi
if [ -f "/etc/log/eibd.log" ]
then
  sudo rm /etc/log/eibd.log
fi
if [ -d "/etc/eibd" ]
then
  sudo rm -R /etc/eibd
fi
if [ -f "/usr/local/lib/libeibclient.so" ]
then
  sudo rm -rf /usr/local/lib/libeibclient.so
fi
if [ -f "/usr/local/lib/libeibclient.so.0" ]
then
  sudo rm -rf /usr/local/lib/libeibclient.so.0
fi
if [ -f "/usr/local/lib/libeibclient.a" ]
then
  sudo rm -rf /usr/local/lib/libeibclient.a
fi
if [ -f "/usr/local/lib/libeibclient.la" ]
then
  sudo rm -rf /usr/local/lib/libeibclient.la
fi
if [ -f "/usr/local/lib/libeibclient.so.0.0.0" ]
then
  sudo rm -rf /usr/local/lib/libeibclient.so.0.0.0
fi
echo 15 > /tmp/compilation_eibd_in_progress
if [ -d "/usr/local/src/Knx/" ] 
then 
  sudo rm -R /usr/local/src/Knx/
fi
sudo mkdir /usr/local/src/Knx/
sudo chmod 777 /usr/local/src/Knx/
cd /usr/local/src/Knx
echo "*****************************************************************************************************"
echo "*                                         Remove knxd                                               *"
echo "*****************************************************************************************************"
apt-get autoremove --yes -y -qq knxd
echo 20 > /tmp/compilation_eibd_in_progress
echo "*****************************************************************************************************"
echo "*                                Installing additional libraries                                    *"
echo "*****************************************************************************************************"
apt-get -qy install build-essential 
echo 25 > /tmp/compilation_eibd_in_progress
echo "*****************************************************************************************************"
echo "*                              Installing PTHSEM V2.0.8 libraries                                   *"
echo "*****************************************************************************************************"
echo "Getting pthsem..."
cd /usr/local/src/Knx
sudo git clone https://github.com/mika-nt28/pthsem.git
echo 30 > /tmp/compilation_eibd_in_progress
sudo chmod 777 -R /usr/local/src/Knx/pthsem
cd /usr/local/src/Knx/pthsem
echo "Compiliing pthsem..." 
architecture=$(uname -m)
if [ "$architecture" = 'aarch64' ]
then
    wget -O config.guess 'http://git.savannah.gnu.org/gitweb/?p=config.git;a=blob_plain;f=config.guess;hb=HEAD'
    wget -O config.sub 'http://git.savannah.gnu.org/gitweb/?p=config.git;a=blob_plain;f=config.sub;hb=HEAD'
fi
sudo ./configure --with-mctx-mth=sjlj --with-mctx-dsp=ssjlj --with-mctx-stk=sas --disable-shared
echo 40 > /tmp/compilation_eibd_in_progress
sudo make
echo 45 > /tmp/compilation_eibd_in_progress
sudo make install
export LD_LIBRARY_PATH="/usr/local/lib"
sudo ldconfig 
sudo mkdir -p /etc/eibd
echo 50 > /tmp/compilation_eibd_in_progress
echo "*****************************************************************************************************"
echo "*                              Installing BCUSDK V0.0.5 libraries                                   *"
echo "*****************************************************************************************************"
echo "Getting bcusdk..."
cd /usr/local/src/Knx
sudo git clone https://github.com/mika-nt28/bcusdk.git
#tar zxvf "$PWDRESSOURCE/bcusdk_0.0.5.tar.gz"
echo 60 > /tmp/compilation_eibd_in_progress
sudo chmod 777 -R /usr/local/src/Knx/bcusdk
cd /usr/local/src/Knx/bcusdk
echo "Compiliing bcusdk..."
if [ "$architecture" = 'aarch64' ]
then
    wget -O config.guess 'http://git.savannah.gnu.org/gitweb/?p=config.git;a=blob_plain;f=config.guess;hb=HEAD'
    wget -O config.sub 'http://git.savannah.gnu.org/gitweb/?p=config.git;a=blob_plain;f=config.sub;hb=HEAD'
fi
sudo ./configure --without-pth-test --enable-onlyeibd --enable-eibnetip --enable-eibnetiptunnel --enable-eibnetipserver --enable-groupcache --enable-usb --enable-ft12 --enable-tpuarts
echo 70 > /tmp/compilation_eibd_in_progress
sudo make
echo 85 > /tmp/compilation_eibd_in_progress
sudo make install
echo 90 > /tmp/compilation_eibd_in_progress
# Add eibd.log to logrotate
echo '/var/log/eibd.log {
        daily
        size=10M
        rotate 4
        compress
        nodelaycompress
        missingok
        notifempty
}' > /etc/logrotate.d/eibd
sudo usermod -a -G www-data eibd
echo 100 > /tmp/compilation_eibd_in_progress
echo "*****************************************************************************************************"
echo "*                              Installing terminé avec succes                                  *"
echo "*****************************************************************************************************"
sudo rm /tmp/compilation_eibd_in_progress
