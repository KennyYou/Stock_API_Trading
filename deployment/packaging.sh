

scp_package="home/jdm68/git/The_Project/packages"

dest="home/jdm68/packages"

current_time=$(date +%m-%d-%Y_%H-%M-%S)
file_copy="bundle-$current_time.tgz"

echo "Making $scp_package to $dest/$file_copy"
date
echo

tar czf $dest/$file_copy --absolute-names $ scp_package

echo
echo "Package Done!" 
date
echo

scp /home/jdm68/packages* jdm68@192.168.2.107:/var/temp

rm -r /home/jdm68/packages*
