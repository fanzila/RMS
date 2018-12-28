FILE_LOCK=/tmp/chacun_lock

if [ -f $FILE_LOCK ]; then
    echo Lock found, exit\!
    exit 6
fi
touch $FILE_LOCK

FILE_MOM=/tmp/chacun_info

if [ ! -f $FILE_MOM ]; then
    echo MOM file not found, one created\!
    touch $FILE_MOM
fi

TIME=$(date +%k%M)

MORNING_START=800
MORNING_END=1100
LUNCH_START=1101
LUNCH_END=1400
AFTERN_START=1401
AFTERN_END=1900
DINER_START=1901
DINER_END=2200
EVEN_START=2201
EVEN_END=2300
NIGHT_START=2301
NIGHT_END=759

if [[ $TIME -ge $MORNING_START ]] && [[ $TIME -le $MORNING_END ]];then
    MOM="MORNING"
	LISTDO="tdtool -v 255 -d 3; tdtool --on 13"
fi
if [[ $TIME -ge $LUNCH_START ]] && [[ $TIME -le $LUNCH_END ]];then
    MOM="LUNCH"
	LISTDO"tdtool -v 255 -d 1 ; tdtool -v 255 -d 2 ; tdtool -v 255 -d 3 ; tdtool -v 255 -d 4 ; tdtool -v 255 -d 5 ; tdtool -v 255 -d 6 ; tdtool --on 7 ;  tdtool --on 12"
fi
if [[ $TIME -ge $AFTERN_START ]] && [[ $TIME -le $AFTERN_END ]];then
    MOM="AFTERN"
fi
if [[ $TIME -ge $DINER_START ]] && [[ $TIME -le $DINER_END ]];then
    MOM="DINER"
	LISTDO"tdtool -v 210 -d 3 ; tdtool -v 170 -d 4 ; tdtool -v 210 -d 5 ; tdtool -v 210 -d 6 ; tdtool --on 11; tdtool --on 11"
fi
if [[ $TIME -ge $EVEN_START ]] && [[ $TIME -le $EVEN_END ]];then
    MOM="EVEN"
	LISTDO="tdtool --off 12 ; tdtool --off 7 ; tdtool --off 11 ; tdtool --off 13"
fi
if [[ $TIME -ge $NIGHT_START ]] && [[ $TIME -le $NIGHT_END ]];then
    MOM="NIGHT"
	LISTDO="tdtool --off 1 ; tdtool --off 2 ; tdtool --off 3 ; tdtool --off 4 ; tdtool --off 5 ; tdtool --off 6 ; tdtool --off 7 ; tdtool --off 8 ; tdtool --off 9 ; tdtool --off 10 ; tdtool --off 11 ; tdtool --off 12; tdtool --off 13; tdtool --off 14; tdtool --off 15; tdtool --off 16"
fi
LINE=$(head -n 1 $FILE_MOM)

echo "LINE: $LINE - MOM: $MOM"

if [ "$LINE" != "$MOM" ];then
    echo $MOM > $FILE_MOM
    echo "DO: $LISTDO"
	$LISTDO #> /dev/null 2>&1
else
    #do nothing
fi

rm $FILE_LOCK

