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
		$get = "SELECT u.username,nc.status, nc.date_confirmed FROM news_confirm AS nc JOIN users AS u ON nc.id_user = u.id WHERE nc.id_news = $news_item->news_id";
		$res = $this->db->query($get) or die($this->mysqli->error);
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