<!DOCTYPE html>
<html lang="en">
<head>
	<title>HANK - <?=$title?></title>
	<meta charset="utf-8">
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

	<style>

.ui-btn-icon-notext,
.ui-header button.ui-btn.ui-btn-icon-notext,
.ui-footer button.ui-btn.ui-btn-icon-notext {
	padding: 0;
	width: 1.75em;
	height: 1.75em;
	text-indent: -9999px;
	white-space: normal !important;
}
.ui-checkbox .ui-btn,
.ui-radio .ui-btn {
	margin: 0;
	text-align: left;
	white-space: normal; /* normal + ellipsis doesn't work on label. Issue #1419. */
	z-index: 2;
}
/* We set the rules for the span as well to fix an issue on Chrome with text-overflow ellipsis for the button in combination with text-align center. */
.ui-select .ui-btn > span:not(.ui-li-count) {
	display: block;
	text-overflow: ellipsis;
	overflow: hidden !important;
	white-space: normal;
}
.ui-listview > .ui-li-static,
.ui-listview > .ui-li-divider,
.ui-listview > li > a.ui-btn {
	margin: 0;
	display: block;
	position: relative;
	text-align: left;
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: normal;
}
.ui-listview > li h1,
.ui-listview > li h2,
.ui-listview > li h3,
.ui-listview > li h4,
.ui-listview > li h5,
.ui-listview > li h6 {
	font-size: 1em;
	font-weight: bold;
	display: block;
	margin: .45em 0;
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: normal;
}
.ui-listview > li p {
	font-size: .75em;
	font-weight: normal;
	display: block;
	margin: .6em 0;
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: normal;
}
.ui-slider-switch .ui-slider-label {
	position: absolute;
	text-align: center;
	width: 100%;
	overflow: hidden;
	font-size: 16px;
	top: 0;
	line-height: 2;
	min-height: 100%;
	white-space: normal;
	cursor: pointer;
}


	</style>
</head>
<div data-role="page" data-theme="a">
	<div data-role="header">
		<? if(!$keylogin) { ?><a href="/admin/" data-role="button" data-inline="true" data-ajax="false" data-icon="home">Home</a><? } ?>
		<h1>News | <?=$bu_name?> | <?=$username?></h1>
		<?php if($user_groups->level >= 2) { ?><a href="/news/create" data-role="button" data-inline="true"  data-ajax="false" data-icon="plus">Create</a><? } ?>
	</div>
	<div data-role="content" data-theme="a">
		<ul data-role="listview" data-inset="true" data-filter="true">	
		<? if(!empty($results)) { ?>
		<? foreach ($results as $news_item): ?>
			<?  
			$this->db->select('u.username, nc.status, nc.date_confirmed')->from('news_confirm as nc')->join('users as u', 'nc.id_user = u.id')->where('nc.id_news', $news_item->news_id);
			$res = $this->db->get() or die($this->mysqli->error);
			$ret = $res->result_array();
		?>
		<li data-role="list-divider"><?php echo $news_item->title; ?> - <?php echo $news_item->date; ?> | <?php echo $news_item->username; ?> | <?php echo $news_item->name; ?> | 
			<a href="#popupBasic_<?=$news_item->id?>" data-rel="popup">View confirmations</a>
			<div data-role="popup" id="popupBasic_<?=$news_item->id?>" style="padding:7px">
				<table style="padding:6px">
					<?php foreach ($ret as $conf): ?> 
						<tr style="padding:4px"><td style="padding:4px"><?=$conf['username']?></td><td style="padding:4px"><? $color="red"; if($conf['status'] == 'confirmed') { $color = "green"; } ?> <b><font color="<?=$color?>"><?=$conf['status']?></font></b></td><td style="padding:4px"><?=$conf['date_confirmed']?></td></tr>
					<?php endforeach; ?>
				</table>
			</div>
		</li>
		<li><?php echo $news_item->text; ?></li>
<?php endforeach; ?>
<? } ?>
</ul>

<p><?php echo $links; ?></p>

</div>
</div>
