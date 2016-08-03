<? if($userlevel == 0){?>
	</div>	
		<div data-role="content" data-theme="a">
<? } ?>
		<? $j=0;
		for ($i=0; $i<count($messages); $i++){
			if(($type == MSG_DELETED && $messages[$i][TF_PM_AUTHOR] == $username)||$type != MSG_DELETED){
				$j++;
			}
		}?>
			<?php if($j>0):?>
				<table data-role="table" id="table-custom-2" data-insert="true" data-mode="reflow" class="ui-body-d ui-shadow table-stripe ui-responsive" data-column-popup-theme="a" <? if($userlevel >= 1){?>data-filter="true" data-filter-placeholder="Filter message"<?}?>>
					<thead>
						<tr>
						<th><?php if($type != MSG_SENT) echo 'From'; else echo 'Recipients'; ?></th>
						<th>Subject</th>
						<th>Date</th>
						<? if($userlevel >= 1){?>
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
