#!/bin/bash

# SCP the tar that was just made to the deploy server
scp /home/jdm68/packages/* jdm68@192.168.2.110:/home/jdm68

#delete local copy once tar has reached server
rm -r /home/jdm68/The_Project/packages/*
