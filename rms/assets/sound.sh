FILE_LOCK=/tmp/sound_lock

if [ -f $FILE_LOCK ]; then
    echo Lock found, exit\!
    exit 6
fi
touch $FILE_LOCK

FILE_MOM=/tmp/sound_info

if [ ! -f $FILE_MOM ]; then
    echo MOM file not found, one created\!
	touch $FILE_MOM
fi

TIME=$(date +%k%M)
#ZIC=`cat /proc/asound/card0/pcm0p/sub0/status | head -1 | awk '{print $2}'`

MORNING_START=800
MORNING_END=1200
LUNCH_START=1201
LUNCH_END=1400
AFTERN_START=1401
AFTERN_END=1900
DINER_START=1901
DINER_END=2200
EVEN_START=2201
EVEN_END=2300

PLAYLIST_MORNING="hank-generic"
PLAYLIST_LUNCH="hank-generic"
PLAYLIST_AFTERN="hank-coffee"
PLAYLIST_DINER="hank-generic"
PLAYLIST_EVEN="hank-generic"

if [[ $TIME -ge $MORNING_START ]] && [[ $TIME -le $MORNING_END ]];then
     PL=$PLAYLIST_MORNING
     VOL="75"
     MOM="MORNING"
fi
if [[ $TIME -ge $LUNCH_START ]] && [[ $TIME -le $LUNCH_END ]];then
     PL=$PLAYLIST_LUNCH
     VOL="75"
     MOM="LUNCH"
fi
if [[ $TIME -ge $AFTERN_START ]] && [[ $TIME -le $AFTERN_END ]];then
     PL=$PLAYLIST_AFTERN
     VOL="75"
     MOM="AFTERN"
fi
if [[ $TIME -ge $DINER_START ]] && [[ $TIME -le $DINER_END ]];then
    PL=$PLAYLIST_DINER
     VOL="75"
     MOM="DINER"
fi
if [[ $TIME -ge $EVEN_START ]] && [[ $TIME -le $EVEN_END ]];then
     PL=$PLAYLIST_EVEN
     VOL="75"
     MOM="EVEN"
fi

LINE=$(head -n 1 $FILE_MOM)

echo "LINE: $LINE - MOM: $MOM"

if [ "$LINE" != "$MOM" ];then
	/usr/bin/amixer set Master $VOL% > /dev/null 2>&1
	mpc load $PL ; mpc shuffle ; mpc play ; mpc shuffle
	echo $MOM > $FILE_MOM
	echo "ENTER IF SET PL: $PL AND VOL: $VOL"
fi

#if [ "$ZIC" != "RUNNING" ]
#then
#    echo "SOUND CARD OFF"
#	#sleep 20
#fi

rm $FILE_LOCK