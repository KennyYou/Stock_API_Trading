# IT 490 Final Project

- [Abstract](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#abstract)
- [General Setup](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#general-setup)
- [Needed Packages](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#needed-packages)
- [Setting Up the General Network](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#setting-up-the-general-network)
  - [Setting a VM’s Bridged Adapter](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#setting-a-vms-bridged-adapter)
  - [Setting a Static IP Address](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#setting-a-static-ip-address)
- [Creating the Back-End](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#creating-the-back-end)
  - [Setting Up a RabbitMQ Server](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#setting-up-a-rabbitmq-server)
  - [Setting Up a MySQL Database](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#setting-up-a-mysql-database)
- [Creating the Front-End](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#creating-the-front-end)
  - [Setting Up an Apache2 Server](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#setting-up-an-apache2-server)
- [Creating the Deployment Server](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#creating-the-deployment-server)
- [Creating the Demilitarized Zone (DMZ)](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#creating-the-demilitarized-zone-dmz)
- [Connecting the AlphaVantage API](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#connecting-the-alphavantage-api)
- [Getting Distributed Logging Set Up](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#getting-distributed-logging-set-up)
- [Using Listeners with systemd](https://github.com/KennyYou/The_Project/blob/experimental-2/README.md#using-listeners-with-systemd)

## Abstract
Potato Situation’s project offers a stock market website that allows users to invest in stocks with fake money they receive when they first sign up. Each user can buy and sell stocks within their own ecosystem or with the general market, and can search for the stock(s) they would like to purchase. It also offers a currency conversion feature, which converts a region’s currency into another region’s exchange rate. Every user has their own profile, and the website graphs out their data to interpret the presented data visually and evaluates their portfolio to let them know how well their stocks are performing.

## General Setup
TBA

## Needed Packages
TBA

## Setting Up the General Network
### Setting a VM’s Bridged Adapter
In order to connect your virtual machine to other virtual machines on a shared local network and to the Internet with VirtualBox, you will have to change your network adapter option to a bridged adapter and set a static IP address. (This guide assumes you have Ubuntu 18.04 LTS installed.)

Open the VirtualBox Manager.

Select your virtual machine.

Click the “settings” gear along the top of the VirtualBox Manager window.

Select “Network” from the sidebar in the pop-up windows.

Click on the drop-down menu and select “bridged adapter”. By default, the network option is set to “NAT”, which emulates an Ethernet connection for your virtual machine.

If you want to use a wireless connection, click on the second drop-down menu and select the “Wireless Adapter” option. By default, this setting is set to the Ethernet adapter, if available for your machine.

Click “OK”.

If the virtual machine in question is running, save your work and restart it. These new settings will not take effect until the next startup.

### Setting a Static IP Address
Setting a static IP address will ensure a machine will use the same IP address every time a given machine restarts

Type `ifconfig`,which will show your virtual machine’s networking information. Take note of the network adapter (which may look like `enp0s3` if using an Ethernet connection), the IP address (designated as `inet`), the network mask (`netmask`), and the broadcast address (`broadcast`).

Type `sudo nano /etc/network/interfaces` and enter your password. This will open the `/etc/network/interfaces` file in Nano, a terminal-based text editor. If you prefer to use Vim (Vi Improved), replace `nano` with `vim`.

Edit the file such that it looks like this:

```
auto (network adapter name)
iface (network adapter name) inet static
address (IP address from inet)
netmask (network mask from netmask)
network 192.168.1.0*
broadcst 192.168.1.255*
gateway 192.168.1.1*

# interfaces(5) file used by ifup(8) and ifdown(8)
auto lo
iface lo inet loopback
```

If using `nano`, press CTRL + X, which will make you quit the program. When prompted to save, hit ‘Y’, then press ENTER. If using `vim`, hit ESC and type ‘:wq’.

Run `sudo service networking restart` which will restart the networking service. Check if the service is running by typing `systemctl status networking`. There should be a green dot next to the service’s name. If there is a red dot and saying it could not raise network interfaces, restart the virtual machine and recheck the service. Restarting may fix the problem. To get out of this command, hit CTRL + C.

Ping another machine connected to the local network. If it doesn’t work, here are some common solutions:
ensure the other machine is powered on and connected
check the previous steps and ensure they have been followed correctly

## Creating the Back-End
### Setting Up a RabbitMQ Server
TBA

### Setting Up a MySQL Database
TBA

## Creating the Front-End
### Setting Up an Apache2 Server
TBA

## Creating the Deployment Server
TBA

## Creating the Demilitarized Zone (DMZ)
TBA

## Connecting the AlphaVantage API
TBA

## Using Listeners with systemd
TBA
