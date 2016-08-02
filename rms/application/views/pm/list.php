<? if($userlevel == 0){?>
	</div>	
		<div data-role="content" data-theme="a">
<? } ?>
			<?php if(count($messages)>0):?>
				<table data-role="table" id="table-custom-2" data-insert="true" data-mode="reflow" class="ui-body-d ui-shadow table-stripe ui-responsive" data-column-popup-theme="a" <? if($userlevel >= 1){?>data-filter="true" data-filter-placeholder="Filter message"<?}?>>
					<thead>
						<tr>
						<th><?php if($type != MSG_SENT) echo 'From'; else echo 'Recipients'; ?></th>
						<th>Subject</th>
						<th>Date</th>
						<? if($userlevel >= 1){?>
							<? if($type != MSG_SENT){ ?>
								<th>Reply</th>
							<? }else{ ?>
								<th></th>
							<? } ?>
							<th><?php if($type != MSG_DELETED) echo 'Delete'; else echo 'Restore'; ?></th>
							</tr>
						<?}?>
					</thead>
					<tbody>
						<?php for ($i=0; $i<count($messages); $i++): ?>
						<tr>
							<th>
								<?php
								if($type != MSG_SENT) echo $messages[$i][TF_PM_AUTHOR];
								else
								{
								  	$recipients = $messages[$i][PM_RECIPIENTS];
									foreach ($recipients as $recipient)
										echo (next($recipients)) ? $recipient.', ' : $recipient;
								}?>
							</th>
							<th><a data-ajax="false" href='<?php echo site_url().'/pm/message/'.$messages[$i][TF_PM_ID]; ?>'><?php echo $messages[$i][TF_PM_SUBJECT] ?></a></th>
							<th><?php echo $messages[$i][TF_PM_DATE]; ?></th>
							<? if($userlevel >= 1){?>
								<? if($type != MSG_SENT){ ?>
									<th><?php echo '<a data-ajax="false" href="'.site_url().'/pm/send/'.$messages[$i][TF_PM_AUTHOR].'/RE&#58;'.$messages[$i][TF_PM_SUBJECT].'"> reply </a>' ?></th>
								<? }else{ ?>
									<th></th>
								<? } ?>
								<th>
									<?php if($type != MSG_DELETED)
										echo '<a data-ajax="false" href="'.site_url().'/pm/delete/'.$messages[$i][TF_PM_ID].'/'.$type.'"> x </a>';
									else
										echo '<a data-ajax="false" href="'.site_url().'/pm/restore/'.$messages[$i][TF_PM_ID].'"> o </a>'; ?>
								</th>
							<?}?>
						</tr>

						<?php endfor;?>
					</tbody>
				</table>
			<?php else:?>
				<h3>No messages found.</h3>
			<?php endif;?>
		</div>
	</div>
