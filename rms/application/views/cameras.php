<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="msapplication-tap-highlight" content="no" />
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="-1">
	<meta http-equiv="pragma" content="no-cache">
</head>
<body>
<?if(isset($cam1)) { ?>
	<SCRIPT LANGUAGE="JavaScript">
	// Set the BaseURL to the URL of your camera
	//Example: var BaseURL = "http://172.21.1.122/";

	// The two following lines need to be changed if an an AXIS 241S(A)/241Q(A)/240Q/243SA is used:
	var Camera = ""; // If you use an AXIS 240Q/241S(A)/241Q(A)/243SA, comment away this line by inserting "//"
	// This is the path to the image generating file inside the camera itself
	var File = "<?=$cam1?>";
	// No changes required below this point
	if (Camera != "") {File += "&camera=" + Camera;}
	var output = "";
	if ((navigator.appName == "Microsoft Internet Explorer") &&
	(navigator.platform != "MacPPC") && (navigator.platform != "Mac68k"))
	{
	// If Internet Explorer under Windows then use ActiveX
	output = '<OBJECT ID="Player" width='
	output += DisplayWidth;
	output += ' height=';
	output += DisplayHeight;
	output += ' CLASSID="CLSID:DE625294-70E6-45ED-B895-CFFA13AEB044" ';
	output += 'CODEBASE="';
	output += 'activex/AMC.cab">';
	output += '<PARAM NAME="MediaURL" VALUE="';
	output += File + '">';
	output += '<param name="MediaType" value="mjpeg-unicast">';
	output += '<param name="ShowStatusBar" value="0">';
	output += '<param name="ShowToolbar" value="0">';
	output += '<param name="AutoStart" value="1">';
	output += '<param name="StretchToFit" value="1">';
	output += '<BR><B>Axis Media Control</B><BR>';
	output += 'The AXIS Media Control, which enables you ';
	output += 'to view live image streams in Microsoft Internet';
	output += ' Explorer, could not be registered on your computer.';
	output += '<BR></OBJECT>';
	} else {
	// If not IE for Windows use the browser itself to display
	theDate = new Date();
	output = '<IMG SRC="';
	output += File;
	output += '&dummy=' + theDate.getTime().toString(10);
	output += '" HEIGHT="';
	output += '" WIDTH="';
	output += '" ALT="Camera Image">';
	}
	document.write(output);
	</SCRIPT>
<? } ?>

<?if(isset($cam2)) { ?>
	<SCRIPT LANGUAGE="JavaScript">
	// Set the BaseURL to the URL of your camera
	//Example: var BaseURL = "http://172.21.1.122/";

	// The two following lines need to be changed if an an AXIS 241S(A)/241Q(A)/240Q/243SA is used:
	var Camera = ""; // If you use an AXIS 240Q/241S(A)/241Q(A)/243SA, comment away this line by inserting "//"
	// This is the path to the image generating file inside the camera itself
	var File = "<?=$cam2?>";
	// No changes required below this point
	if (Camera != "") {File += "&camera=" + Camera;}
	var output = "";
	if ((navigator.appName == "Microsoft Internet Explorer") &&
	(navigator.platform != "MacPPC") && (navigator.platform != "Mac68k"))
	{
	// If Internet Explorer under Windows then use ActiveX
	output = '<OBJECT ID="Player" width='
	output += DisplayWidth;
	output += ' height=';
	output += DisplayHeight;
	output += ' CLASSID="CLSID:DE625294-70E6-45ED-B895-CFFA13AEB044" ';
	output += 'CODEBASE="';
	output += 'activex/AMC.cab">';
	output += '<PARAM NAME="MediaURL" VALUE="';
	output += File + '">';
	output += '<param name="MediaType" value="mjpeg-unicast">';
	output += '<param name="ShowStatusBar" value="0">';
	output += '<param name="ShowToolbar" value="0">';
	output += '<param name="AutoStart" value="1">';
	output += '<param name="StretchToFit" value="1">';
	output += '<BR><B>Axis Media Control</B><BR>';
	output += 'The AXIS Media Control, which enables you ';
	output += 'to view live image streams in Microsoft Internet';
	output += ' Explorer, could not be registered on your computer.';
	output += '<BR></OBJECT>';
	} else {
	// If not IE for Windows use the browser itself to display
	theDate = new Date();
	output = '<IMG SRC="';
	output += File;
	output += '&dummy=' + theDate.getTime().toString(10);
	output += '" HEIGHT="';
	output += '" WIDTH="';
	output += '" ALT="Camera Image">';
	}
	document.write(output);
	</SCRIPT>
<? } ?>

<?if(isset($cam3)) { ?><img src="<?=$cam3?>"><? } ?>
<?if(isset($cam4)) { ?><img src="<?=$cam4?>"><? } ?>
</body>
</html>