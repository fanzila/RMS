<? if($userlevel == 0){?>
	</div>	
		<div data-role="content" data-theme="a">
<? } ?>
		<?//Check if there is something to display. Otherwise, the titlebar would be alone.
		$j=0;
		for ($i=0; $i<count($messages); $i++){
			if(($type == MSG_DELETED && $messages[$i][TF_PM_AUTHOR] == $username)||$type != MSG_DELETED){
				$j++;
			}
		}?>
			<?php if($j>0):?>
				<table data-role="table" id="table-custom-2" data-insert="true" data-mode="reflow" class="ui-body-d ui-shadow table-stripe ui-responsive" data-column-popup-theme="a" <? if($this->ion_auth_acl->has_permission('additional_menu_pm')){?>data-filter="true" data-filter-placeholder="Filter message"<?}?>>
					<thead>
						<tr>
						<th>From</th>
						<th>To</th>
						<th>Subject</th>
						<th>Date</th>
						<? if($this->ion_auth_acl->has_permission('additional_menu_pm')){?>
							<? if($type == MSG_SENT){?>
								<th><? echo 'Archive';?></th>
							<? }else if($type == MSG_DELETED){?>
								<th><? echo 'Restore';?></th>
							<?}?>
							</tr>
						<?}?>
					</thead>
					<tbody>
						<?php for ($i=0; $i<count($messages); $i++): ?>
							<? if(($type == MSG_DELETED && $messages[$i][TF_PM_AUTHOR] == $username)||$type != MSG_DELETED){?>
								<tr>
									<th>
										<?php
										echo $messages[$i][TF_PM_AUTHOR];
										?>
									</th>
									<th>
										<?php
										  	$recipients = $messages[$i][PM_RECIPIENTS];
											foreach ($recipients as $recipient)
												echo (next($recipients)) ? $recipient.', ' : $recipient;
										?>
									</th>
									<th><a data-ajax="false" href='<?php echo site_url().'/pm/message/'.$messages[$i][TF_PM_ID]; ?>'><?php echo $messages[$i][TF_PM_SUBJECT] ?></a></th>
									<th><?php echo $messages[$i][TF_PM_DATE]; ?></th>
									<? if($this->ion_auth_acl->has_permission('additional_menu_pm')){?>
										<? if($type == MSG_SENT && $messages[$i][TF_PM_AUTHOR]==$username){?>
											<th><? echo '<a data-ajax="false" href="'.site_url().'/pm/delete/'.$messages[$i][TF_PM_ID].'/'.$type.'"> x </a>';?></th>
										<? }else if($type == MSG_DELETED){?>
											<th><? echo '<a data-ajax="false" href="'.site_url().'/pm/restore/'.$messages[$i][TF_PM_ID].'"> o </a>'; ?></th>
										<?}?>
									<?}?>
								</tr>
							<?}?>
						<?php endfor;?>
					</tbody>
				</table>
			<?php else:?>
				<h3>No messages found.</h3>
			<?php endif;?>
		</div>
	</div>
