cd /var/temp 

ssh jdm68@192.168.2.107 'rm -rf /home/jdm68/packages/*'

pv $1 | ssh jdm68@192.168.2.107 'cat |tar xz -C /home/jdm68/packages/'