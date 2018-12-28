	</div>
	<div data-role="content" data-theme="a">
		<ul data-role="listview" data-inset="true">
			<li><a rel="external" data-ajax="false" href="/webcashier/movement/close">Close</a></li>
			<?php if($this->ion_auth_acl->has_permission('view_safe')) { ?>
				<li><a rel="external" data-ajax="false" href="/webcashier/safe/">Safe</a></li>
			<?php } if($this->ion_auth_acl->has_permission('view_report')) { ?>
			<li><a rel="external" data-ajax="false" href="/webcashier/report/">Report</a></li>
			<?php } 
			if($this->ion_auth_acl->has_permission('view_sales_stats')) { ?>
			<li><a rel="external" data-ajax="false" href="/webcashier/stats/">Sales stats</a></li>
			<?php } 
			?>
		</ul>
	</div>
</div>