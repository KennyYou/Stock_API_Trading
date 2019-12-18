#!/bin/bash

# SCP the tar that was just made to the deploy server
cat /home/jdm68/The_Project/packages/*| ssh jdm68@192.168.2.110 cat ">" *
ssh 192.168.2.110 cat * > package.tar.gz 

#delete local copy once tar has reached server
rm -r /home/jdm68/The_Project/packages/*
