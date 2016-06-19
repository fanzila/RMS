ZIC=`cat /proc/asound/card0/pcm0p/sub0/status | head -1 | awk '{print $2}'`
MPC=`mpc | tr '[:upper:]' '[:lower:]' | grep playing | head -1`

if [ -z "$MPC" ]
then
	echo "MPC OFF"
	mpc stop && mpc clear && mpc load HANK && mpc play && mpc shuffle
fi
 
if [ "$ZIC" != "RUNNING" ]
then
    echo "SOUND CARD OFF"
	/etc/init.d/mopidy restart
	mpc stop && mpc clear && mpc load HANK && mpc play && mpc shuffle
fi
