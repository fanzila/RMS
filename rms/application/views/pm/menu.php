<body>
	<div data-role="page" data-theme="a">
			<div data-role="header">
				<a href="/admin/" data-role="button" data-inline="true" data-ajax="false" data-icon="home">Home</a>
				<h1>Reporting | <?=$bu_name?> | <?=$username?></h1>
</div>	
	<div data-role="content" data-theme="a">
	<?php $this->load->helper('url'); ?>
<table width="100%" border="1" style="border-collapse:collapse; border-color:#CCCCCC;" cellpadding="4" cellspacing="4">
	<tr>
    <td style="padding:10px;">
		<a data-ajax="false" href="<?php echo site_url()."/pm"?>">Inbox</a> &nbsp;&nbsp;&nbsp;
		<a data-ajax="false" href="<?php echo site_url()."/pm/messages/".MSG_UNREAD?>">Unread</a> &nbsp;&nbsp;&nbsp; 
		<a data-ajax="false" href="<?php echo site_url()."/pm/messages/".MSG_SENT?>">Sent</a> &nbsp;&nbsp;&nbsp;
		<a data-ajax="false" href="<?php echo site_url()."/pm/messages/".MSG_DELETED?>">Trashed</a> &nbsp;&nbsp;&nbsp;
		<a data-ajax="false" href="<?php echo site_url()."/pm/send"?>">Compose</a>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	</td>
	</tr>
</table>
<br />