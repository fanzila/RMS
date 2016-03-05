<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>HANK - <?=$title?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="msapplication-tap-highlight" content="no" />
	<link rel="stylesheet" href="/public/jqm/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" href="/public/jqm/themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="/public/jqm/jquery.mobile.structure-1.4.5.min.css" />
	<link rel="stylesheet" href="/public/jqm/themes/hmw.min.css" />
	
	<script type="text/javascript" src="/public/tinymce/tinymce.min.js"></script>
	
	<script type="text/javascript">
        tinymce.init({
            selector: "#text",
		    plugins: "textcolor hr fullscreen",
		    toolbar: "forecolor backcolor fullscreen",
			removed_menuitems: 'newdocument'
        });
		
    </script>

</head>