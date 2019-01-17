#!/bin/bash

#digitemp_DS9097U -q -c /etc/digitemp.conf -a
./demo1wire.sh | while IFS= read -r i
  do
	TEMP=`echo $i | awk '{print $7}'`
	ID=`echo $i | awk '{print $5}'`
    echo "$ID $TEMP"
	echo 'INSERT INTO sensors_temp SET id_sensor='$ID', temp='$TEMP';' |  mysql --defaults-file="/root/.p" hmw
  done

