			<?php if(count($messages)>0):?>
				<table data-role="table" id="table-custom-2" data-insert="true" data-mode="reflow" data-filter="true" class="ui-body-d ui-shadow table-stripe ui-responsive" data-column-popup-theme="a" data-filter-placeholder="Filter message">
					<thead>
						<th><?php if($type != MSG_SENT) echo 'From'; else echo 'Recipients'; ?></th>
						<th>Subject</th>
						<th>Date</th>
						<th>Reply</th>
						<th><?php if($type != MSG_DELETED) echo 'Delete'; else echo 'Restore'; ?></th>
						</tr>
					</thead>
					<tbody>
						<?php for ($i=0; $i<count($messages); $i++): ?>
						<tr>
							<td>
								<?php
								if($type != MSG_SENT) echo $messages[$i][TF_PM_AUTHOR];
								else
								{
								  	$recipients = $messages[$i][PM_RECIPIENTS];
									foreach ($recipients as $recipient)
										echo (next($recipients)) ? $recipient.', ' : $recipient;
								}?>
							</td>
							<td><a data-ajax="false" href='<?php echo site_url().'/pm/message/'.$messages[$i][TF_PM_ID]; ?>'><?php echo $messages[$i][TF_PM_SUBJECT] ?></a></td>
							<td><?php echo $messages[$i][TF_PM_DATE]; ?></td>
							<?php if($type != MSG_SENT): ?>
								<td><?php echo '<a data-ajax="false" href="'.site_url().'/pm/send/'.$messages[$i][TF_PM_AUTHOR].'/RE&#58;'.$messages[$i][TF_PM_SUBJECT].'"> reply </a>' ?></td>
							<?php endif; ?>
							<td>
								<?php if($type != MSG_DELETED)
									echo '<a data-ajax="false" href="'.site_url().'/pm/delete/'.$messages[$i][TF_PM_ID].'/'.$type.'"> x </a>';
								else
									echo '<a data-ajax="false" href="'.site_url().'/pm/restore/'.$messages[$i][TF_PM_ID].'"> o </a>'; ?>
								</td>
						</tr>

						<?php endfor;?>
					</tbody>
				</table>
			<?php else:?>
				<h1>No messages found.</h1>
			<?php endif;?>
		</div>
	</div>
