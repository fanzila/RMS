<!--Nécessaire à news-->
	<script type="text/javascript" src="/public/tinymce/tinymce.min.js"></script>
	<script type="text/javascript">
		tinymce.init({
			selector: "#<?=TF_PM_BODY?>",
			plugins: "textcolor hr fullscreen",
			toolbar: "forecolor backcolor fullscreen",
			min_height: 275,
			removed_menuitems: 'newdocument'
		});
    </script>