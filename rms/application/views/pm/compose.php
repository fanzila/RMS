	</div>
	<div data-role="content" data-theme="a">
		<?php
			/*
			 * Note:
			 * 'name' is the "name" property of the input field / list
			 * 'id' is the "id" property of the input field / list AND the "for" property of the label field
			 * 'name' and 'id' dont have to be the same - but it is most logical
			 */
			$this->load->library('session');
			$MAX_INPUT_LENGTHS = $this->config->item('$MAX_INPUT_LENGTHS', 'pm');
			$recipients = array(
				'name'	=> PM_RECIPIENTS,
				'id'	=> PM_RECIPIENTS,
				'value' => set_value(PM_RECIPIENTS, $message[PM_RECIPIENTS]),
				'maxlength'	=> $MAX_INPUT_LENGTHS[PM_RECIPIENTS], 
				'data-clear-btn' => "true",
				'size'	=> 40,
			);
			$subject = array(
				'name'	=> TF_PM_SUBJECT,
				'id'	=> TF_PM_SUBJECT,
				'value' => set_value(TF_PM_SUBJECT, $message[TF_PM_SUBJECT]),
				'maxlength'	=> $MAX_INPUT_LENGTHS[TF_PM_SUBJECT], 
				'data-clear-btn' => "true",
				'size'	=> 40
			);
			$body = array(
				'name'	=> TF_PM_BODY,
				'id'	=> TF_PM_BODY,
				'value' => set_value(TF_PM_BODY, $message[TF_PM_BODY])
			);
		?>
								
			<form action="/index.php/pm/send" data-ajax="false"  method="post" accept-charset="utf-8">
				
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="5%"><?php echo form_label('To', $recipients['id']); ?></td>
						<td width="66%">
							<select style="background-color:#a1ff7c" name="recipients" id="recipients" data-inline="true" data-theme="a" required>
								<option value="">Select a Recipient</option>
								<? if($userlevel >= 2){ ?>
									<option value="<?=$managers?>">All manager</option>
								<?}?>
								<?foreach ($users as $user) {
									if($user->username != $this->session->all_userdata()['identity']){?>
										<option id="<?=$user->id?>" value="<?=$user->username?>" <? if(isset($form['user']) AND $form['user']==$user->id) { ?> selected <? } ?>><?=$user->first_name?> <?=$user->last_name?>
										</option>
									<?}
								}?>
							</select>
						</td>
						<td><?php echo form_error($recipients['name']); ?></td>
					</tr>	
					<tr>
						<td><?php echo form_label('Type d\'entretien', $subject['id']); ?></td>
						<td width="66%">
							<?$i=0;foreach ($sujets as $sujet) {
								$result[$i]['name']=$sujet->name;
								$result[$i]['text']=$sujet->text;
								$i += 1;
							}?>
						<select style="background-color:#a1ff7c" name="privmsg_subject" id="privmsg_subject" data-inline="true" data-theme="a" onchange="getContent(this)">
							<option value="">Select a Subject</option>
							<?foreach ($sujets as $sujet) {?>
							<option value="<?=$sujet->name?>"<? if(isset($form['sujet']) AND $form['sujet']==$sujet->id) { ?> selected <?}?>>
								<?=$sujet->name?>
							</option>
							<?}?>
						</select>
							<?foreach ($sujets as $sujet) {?>
							<div align="<?=$sujet->text?>" id="<?=$sujet->name?>"></div>
							<?}?>
						</td>
						<td><?php echo form_error($subject['name']); ?></td>	
					</tr>	
					<tr>
						<td><?php echo form_label('Compte Rendu', $body['id']); ?></td>
						<td><input type="text" name="privmsg_body" id="privmsg_body" value="" data-clear-btn="true" /></td>
						<td><?php echo form_error($body['name']); ?></td>	
					</tr>
					<tr>
					<td colspan=2 align="center" valign="top"><br/>
						<label>
							<!-- DO NOT CHANGE BUTTON NAME, NEEDED FOR CONTROLLER "send" -->
							<input data-ajax="false" type="submit" name="btnSend" id="btnSend" value="Send" />
						</label>
					</td>
					<td></td>
					</tr>	
					<tr>
					<td align="left" valign="top" style="font-weight:bold; background:#F2F2F2; padding:4px;">
					</td>
					<td align="left" valign="top" style="font-weight:bold; background:#F2F2F2; padding:4px;">
					<?php
					if(isset($status)) echo $status.' ';
					if($this->session->flashdata('status')) echo $this->session->flashdata('status').' ';
					if(!$found_recipients)
					{
						foreach($suggestions as $original => $suggestion) 
						{
							echo 'Did you mean <font color="#00CC00">'.$suggestion.'</font> for <font color="#CC0000">'.$original.'</font> ?'; 
							echo '<br />';
						}
					} ?>
					</td>
					<td></td>
					</tr>
				</table>
			<?php echo form_close(); ?>

			
			<script type="text/javascript">
				function getContent(select) {
					var test = select.value;
					var text = document.getElementById(select.value).align;
					tinymce.get('privmsg_body').setContent(text);
				}
			</script>
		</div>
	</div>