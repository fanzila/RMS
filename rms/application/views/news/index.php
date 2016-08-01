		<?php if($user_groups->level >= 2) { ?><a href="/news/create/" class="ui-btn ui-btn-right" rel="external" data-ajax="false" data-icon="plus"><i class="zmdi zmdi-plus zmd-2x"></i></a><? } ?>
	</div>
	<div data-role="content" data-theme="a">
		<ul data-role="listview" data-inset="true" data-filter="true">	
		<? if(!empty($results)) { ?>
		<? foreach ($results as $news_item): ?>
			<?  
			$this->db->select('u.username, nc.status, nc.date_confirmed, ne.id')->from('news_confirm as nc')->join('users as u', 'nc.id_user = u.id')->join('news as ne', 'nc.id_news = ne.id')->where('nc.id_news', $news_item->news_id);
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
		<li><img class="ui-li-icon"/>
			<?if($news_item->picture){?>
				<? $image_properties = array(
						'src'   => 'public/pictures/'.$news_item->picture,
						'class' => 'img-responsive',
						'style' => "max-height: 300px; max-width: 300px;"
				);?>
				<?echo img($image_properties);?>
			<?}?>
		<?php echo $news_item->text; ?></li>
		<?php endforeach; ?>
		<? } ?>
</ul>

<p><?php echo $links; ?></p>

</div>
</div>
<style>
.ui-btn-icon-notext,
.ui-header button.ui-btn.ui-btn-icon-notext,
.ui-footer button.ui-btn.ui-btn-icon-notext {
	white-space: normal !important;
}
.ui-checkbox .ui-btn,
.ui-radio .ui-btn {
	white-space: normal; /* normal + ellipsis doesn't work on label. Issue #1419. */
}
/* We set the rules for the span as well to fix an issue on Chrome with text-overflow ellipsis for the button in combination with text-align center. */
.ui-select .ui-btn > span:not(.ui-li-count) {
	white-space: normal;
}
.ui-listview > .ui-li-static,
.ui-listview > .ui-li-divider,
.ui-listview > li > a.ui-btn {
	white-space: normal;
}
.ui-listview > li h1,
.ui-listview > li h2,
.ui-listview > li h3,
.ui-listview > li h4,
.ui-listview > li h5,
.ui-listview > li h6 {
	white-space: normal;
}
.ui-listview > li p {
	white-space: normal;
}
.ui-slider-switch .ui-slider-label {
	white-space: normal;
}
.ui-li-thumb {
    left: 1px;
    max-height: 300px; <-- height
    max-width: 300px; <-- width
    position: absolute;
    top: 0;
}
</style>

<?if($login==1){?>
<script type="text/javascript">
		$(document).on('pagebeforeshow', '', function(){
			$( "#adminpanel" ).panel( "open");
		});
	</script>
<?}?>