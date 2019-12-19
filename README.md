# Potato Situation's Final Systems Integration Project

- [Abstract](https://github.com/KennyYou/The_Project/README.md#abstract)
- [General Setup](https://github.com/KennyYou/The_Project/README.md#general-setup)
- [Needed Packages](https://github.com/KennyYou/The_Project/README.md#needed-packages)
- [Setting Up the General Network](https://github.com/KennyYou/The_Project/README.md#setting-up-the-general-network)
  - [Setting a VM’s Bridged Adapter](https://github.com/KennyYou/The_Project/README.md#setting-a-vms-bridged-adapter)
  - [Connecting to Machines on the Network](https://github.com/KennyYou/The_Project/README.md#connecting-to-machines-on-the-network)
- [Setting Up a RabbitMQ Server](https://github.com/KennyYou/The_Project/README.md#setting-up-a-rabbitmq-server)
  - [Install Erlang](https://github.com/KennyYou/The_Project/README.md#install-erlang)
  - [Install RabbitMQ](https://github.com/KennyYou/The_Project/README.md#install-rabbitmq)
- [Setting Up a MySQL Database](https://github.com/KennyYou/The_Project/README.md#setting-up-a-mysql-database)

- [Creating the Back-End](https://github.com/KennyYou/The_Project/README.md#creating-the-back-end)
- [Creating the Front-End](https://github.com/KennyYou/The_Project/README.md#creating-the-front-end)
  - [Setting Up an Apache2 Server](https://github.com/KennyYou/The_Project/README.md#setting-up-an-apache2-server)
- [Creating the Deployment Server](https://github.com/KennyYou/The_Project/README.md#creating-the-deployment-server)
- [Creating the Demilitarized Zone (DMZ)](https://github.com/KennyYou/The_Project/README.md#creating-the-demilitarized-zone-dmz)
- [Connecting the AlphaVantage API](https://github.com/KennyYou/The_Project/README.md#connecting-the-alphavantage-api)
- [Getting Distributed Logging Set Up](https://github.com/KennyYou/The_Project/README.md#getting-distributed-logging-set-up)
- [Using Listeners with systemd](https://github.com/KennyYou/The_Project/README.md#using-listeners-with-systemd)

## Abstract
Potato Situation’s project offers a stock market website that allows users to invest in stocks with fake money they receive when they first sign up. Each user can buy and sell stocks within their own ecosystem or with the general market, and can search for the stock(s) they would like to purchase. It also offers a currency conversion feature, which converts a region’s currency into another region’s exchange rate. Every user has their own profile, and the website graphs out their data to interpret the presented data visually and evaluates their portfolio to let them know how well their stocks are performing.

## General Setup
This project requires VirtualBox (a free virtualization application) and Ubuntu Linux 18.04 LTS (the latest long-term support release at the time of this writing). It also requires four main virtual machines: the front-end (FE), the back-end (BE), the demilitarized zone (DMZ), and the deployment server (DEP). A router will be necessary to connect said virtual machines to each other; the speed and transfer rate of the router do not matter, but a hard-wired Ethernet connection is recommended as it allows a more consistent connection as opposed to Wi-Fi.

There are also three main branches: development (Dev), quality assurance (QA), and production (Prod). The FE, BE, and DMZ all use these three branches and require a hot standby (HSB) on the Prod branch. DEP, on the other hand, does not follow this rule as it needs to deploy the packages from Dev to QA and QA to Prod.

## Needed Packages
TBA

## Setting Up the General Network
### Setting a VM’s Bridged Adapter
In order to connect your virtual machine to other virtual machines on a shared local network and to the Internet, you will have to change your network adapter option to a bridged adapter.

Open the VirtualBox Manager and select your virtual machine.

Click the “settings” gear along the top of the VirtualBox Manager window.

Select “Network” from the sidebar in the pop-up windows.

Click on the “Attached to:” drop-down menu and select “bridged adapter”. This ensures you will be able to ping other machines on the local network. By default, the network option is set to “NAT”, which emulates an Ethernet connection for your virtual machine.

If you want to use a wireless connection, click on the second drop-down menu and select the “Wireless Adapter” option. By default, this setting is set to the Ethernet adapter, if available for your machine.

Click “OK”.

If the virtual machine in question is running, save your work and restart it. These new settings will not take effect until the next startup.

### Connecting to Machines on the Network
Setting a static IP address will ensure a machine will use the same IP address every time a given machine restarts. This will be useful as said machine will be deployed as a server, and the other machines on your network will need the IP address to stay consistent for use in listener scripts and RabbitMQ traffic (which is detailed later in this document). 

Open a terminal window and type `ifconfig`, which will show your virtual machine’s networking information. Take note of the network adapter (which may look like `enp0s3` if using an Ethernet connection), the IP address (designated as `inet`) and the network mask (`netmask`).

**NOTE**: If you cannot use the `ifconfig` command, you may need to install the net-tools package first. Do this with `sudo apt install net-tools -y` and enter your password.

Type the following commands:
```
sudo touch /etc/NetworkManager/conf.d/10-globally-managed-devices.conf
sudo systemctl restart NetworkManager
```
The system’s Network Manager has a missing configuration file. (This is not a bug from our project: rather, this is an Ubuntu-specific issue.) The first command creates the needed missing file for networking to work properly while on bridged adapter mode, and the second one restarts the networking service. Check if the service is running by typing `systemctl status networking`. To get out of this command, hit CTRL + C.
Ping another machine connected to the local network and connect to an external website (e.g., Google, Facebook, etc.). If it doesn’t work, here are some solutions:
1. ensure the other machine is powered on and connected to the same network
2. check the previous steps and ensure they have been followed correctly

Open the Settings application, and select “Network” from the left sidebar. On the right side, a section named “Wired” should appear. Click the gear icon.

Click on “IPv4” along the top of the window. Change the IPv4 method from “Automatic (DHCP)” to “Manual”. Under the “Addresses” section, add the IP address and network mask from the `ifconfig` command and enter the router’s IP address in the “Gateway” field (most routers have `192.168.1.1` as a default address). For DNS, Google’s Public DNS address (`8.8.8.8`) works well. When this is complete, click “Apply”. Open another terminal window and restart the networking service (`sudo systemctl restart NetworkManager`) and enter your password.

## Setting Up a RabbitMQ Server
Each of the four primary machines requires the use of RabbitMQ, a messaging queue service that utilizes queues and exchanges to send messages for each machine to perform a given task. This service works well to increase the overall security of this project and is especially useful if and when one or more of the machines goes down, as the messages are stored in memory and transmit the next time the machine(s) in question come back online. This is a critical piece of both the BE and DEP machines.

### Install Erlang
Erlang is a necessary dependency that needs to be installed before installing RabbitMQ. In order to install it, the `curl` and `apt-transport-https` packages needs to be installed prior so it is possible to add Erlang’s repository key to the system’s repository sources and get its data over HTTPS.

Open a terminal window and type `sudo apt install curl -y` and enter your password; this will install the `curl` package. Then type `curl -fsSL https://github.com/rabbitmq/signing-keys/releases/download/2.0/rabbitmq-release-signing-key.asc | sudo apt-key add -`, which adds Erlang’s key into your system’s repository key list.

Next, install the apt-transport-https package by entering `sudo apt install apt-transport-https -y`. After that completes, you will need to add a line in the Erlang list to specify which Linux distribution and Erlang version you want to install. Type `sudo nano /etc/apt/sources.list.d/bintray.erlang.list`, which opens the file in Nano. (If Vim is more preferable, replace `nano` with `vim`.)

Add the following line into the file: `deb https://dl.bintray.com/rabbitmq-erlang/debian bionic erlang` (where *bionic* is the corresponding version of Ubuntu your machine is running—in this case, Ubuntu 18.04—and *erlang* is the latest version).

If using `nano`, press CTRL + X, which will make you quit the program. When prompted to save, hit ‘Y’, then press ENTER. If using `vim`, hit ESC and type ‘:wq’.

Lastly, update the system’s package list by running `sudo apt update` and install Erlang with `sudo apt install erlang-base -y`.

### Install RabbitMQ
With Erlang installed, it is now possible to install RabbitMQ. Like Erlang, you will need to get the repository key and add it to the system’s repository list before installation.

Get the RabbitMQ key by running `wget -O - "https://packagecloud.io/rabbitmq/rabbitmq-server/gpgkey" | sudo apt-key add -`. Next, add it to the system’s repository list with `echo "deb https://dl.bintray.com/rabbitmq/debian bionic main" | sudo tee /etc/apt/sources.list.d/bintray.rabbitmq.list`. Then, update the package list with `sudo apt update` and install RabbitMQ with `sudo apt install rabbitmq-server -y`.

Once RabbitMQ is installed, the background service should start automatically. To start the service as root, type `sudo service rabbitmq-server start`.

RabbitMQ has plugins that enable additional features. For this guide, we will enable the Management UI plugin, as that allows a graphical user interface (GUI) for viewing traffic data and setting up necessary exchanges and queues. To do this, type `sudo rabbitmq-plugins enable rabbitmq_management`, which enables the Management plugin. Once that finishes, you’ll now be able to access a GUI dashboard in your installed web browser. By default, this dashboard can be accessed at `https://localhost:15672/` and both the username and password to log in are **guest**.

## Setting Up a MySQL Database
A database is necessary to store users’ credentials and information on the packages needed to deploy on QA and Prod machines. This is another critical piece of both the BE and DEP machines.

Install the MySQL server package by running the command `sudo apt install mysql-server -y`.

Once the installation completes, run the MySQL server as root by running `sudo mysql -u root` and entering your password. You will reach a MySQL prompt at this point; all the commands entered from this point must end with a semicolon (;).

To create a database, enter `CREATE DATABASE database_name`, where ‘database_name’ is the name you wish to use. (Capitalization is not required for commands to work; it is frequently used to distinguish commands from objects.)

To create a user, enter `CREATE USER ‘user_name’@’localhost’ IDENTIFIED BY ‘password’`, where ‘user_name’ is the user’s name you want to use and ‘password’ as the user’s associated password. ‘Localhost’ in this command allows a connection to this MySQL instance from only the local machine and will deny queries if the connecting user uses another machine.

To give permissions over the database to the newly created user, enter `GRANT ALL PRIVILEGES ON database_name . * to ‘user_name’@’localhost’`. For this instance, use ‘users’ as the database and ‘backend’ as the username.
**(From this point forward, it is recommended to log in as the created user from the terminal for future console connections with the following syntax: `mysql -u user_name -p database_name`.)**

Clone the GitHub project  to your given machine with `git clone https://github.com/KennyYou/The_Project.git` and import the database’s SQL file into your MySQL installation. Type `mysql -u user_name -p database_name < file.sql`, where user_name is the newly created user’s name, database_name is the name of the database, and file.sql is the full path to the SQL file you wish to use.

## Setting up an SSH Server 
For each machine to transfer files over the local network, they need to have OpenSSH installed and allow file transfer over port 22 from the firewall. Download openssh-server simply by the commands:
```
sudo apt-get update 
sudo apt install openssh-server
sudo ufw allow 22
```
These three commands combined perform the following functions: update the system's package list, install OpenSSH on the system, and allow the system's firewall to allow SSH traffic over port 22.

When you use the command `ssh user@ip_address`, you will be able to access the port through the permissions of the user along with transfer files as such with this command: `cat file | ssh user@ip_address “ > ” file`.
  
This will give access to the server to send a file to the user needing it. For instance, a .txt file can be transferred from one endpoint to the next, the host will send to the user and it will be in their local directory.

## Creating the Back-End
TBA

## Creating the Front-End
### Setting Up an Apache2 Server
All files for apache2 that need to be displayed on the web and hosted need to go into files.
```
/var/www/
```
Whereas editing the `/etc/hosts` file will be able to change the website name to match the IP address of the machine that is hosting the website allowing you to connect. 

In order to enable the website on the hosted machine run the commands:
```
a2ensite example.com
a2dissite example.com
```

## Creating the Deployment Server
TBA

## Creating the Demilitarized Zone (DMZ)
TBA

## Connecting the AlphaVantage API
TBA

## Getting Distributed Logging Set Up
TBA

## Using Listeners with systemd
Running a script automatically is used with the code: 
```
[Unit]
Description=RMQ Startup
StartLimitIntervalSec=6

[Service]
Restart=always
RestartSec=6
ExecStart=/usr/bin/php -> Name of script you want to autorun <-

[Install]
WantedBy=multi-user.target
```
Where even when crashing the script will auto run, in order to prevent stalling on systems. 
