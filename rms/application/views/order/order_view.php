		</div>
		<div data-role="content" data-theme="a">
			<?$attributes = array('id' => "logOrder", 'name' => "logOrder", 'data-ajax' => "false", 'method'=> 'get');
			echo form_open("order/viewOrders", $attributes);?>
				<table width="100%" style="background-color: #ffffff; border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
					<tr>
						<td colspan="6" style="background-color: #e9e9e9;">Search by <font size="2">(all optionnal but fill at least one)</font> :
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<select name="supplier" id="supplier" data-mini="true">
								<option value="">Select a supplier</option>
								<? foreach ($suppliers as $sup) { ?>
									<option value="<?=$sup['name']?>" <?if (isset($filters) && isset($filters['supplier']) && $filters['supplier'] == $sup['name']) echo 'selected';?>><?=$sup['name']?></option>
								<? } ?>
							</select>
						</td>
						<td colspan="2">
							<select name="user" id="user" data-mini="true">
								<option value="">Select a user</option>
								<?foreach ($users as $user) {?>
									<option value="<?=$user->username?>" <?if (isset($filters) && isset($filters['user']) && $filters['user'] == $user->username) echo 'selected';?>><?=$user->first_name?> <?=$user->last_name?>
									</option>
								<?}?>
							</select>
						</td>
						<td colspan="2">
								<small>Select a status</small><br/>
								<div class="inline">
									<input id="sentcbk" type="checkbox" name="sent"<?if (!isset($filters) || (isset($filters) && isset($filters['sent']))) echo 'checked';?>>
									<label style="background-color: white;" for="sentcbk">Sent</label>
								</div><br/>
								<div class="inline">
										<? if(!$keylogin) { ?><input id="draftcbk" type="checkbox" name="draft" <?if (isset($filters) && isset($filters['draft'])) echo 'checked';?>><? } ?>
										<label style="background-color: white;" for="draftcbk">Draft</label>
								</div><br/>
								<div class="inline">
									<input id="receivedcbk" type="checkbox" name="received" <?if (!isset($filters) || (isset($filters) && isset($filters['received']))) echo 'checked';?>>
									<label style="background-color: white;" for="receivedcbk">Received</label>
								</div>
						</td>
					</tr>
					<tr>
						<td><label for="idorder" id="label">ID :</label></td>
						<td><input type="text" id="idorder" name="idorder" value="<?if (isset($filters) && isset($filters['idorder'])) echo $filters['idorder'];?>" data-clear-btn="true" /></td>
						<td><label for="sdate" id="label">Order date from the :</label></td>
						<td><input type="text" data-role="date" id="sdate" name="sdate" value="<?if (isset($filters) && isset($filters['sdate'])) echo $filters['sdate'];?>" data-clear-btn="true" /></td>
						<td><label for="edate" id="label">To the :</label></td>
						<td><input type="text" data-role="date" id="edate" name="edate" value="<?if (isset($filters) && isset($filters['edate'])) echo $filters['edate'];?>" data-clear-btn="true" /></td>
					</tr>
					<tr>
						<td colspan="6">
							<input type="hidden" id="filters" name="keep_filters" value="true">
							<?$attributes = array('id' => "sub", 'name' => "submit");
							echo form_submit($attributes, 'Search');?>
						</td>
					</tr>
				</table>
				<input type="hidden" name="search" value="1">
			</form>
			<ul data-role="listview" data-inset="true"  data-filter="false">

				<? if(!empty($results)) { ?>
					<? foreach ($results as $rec): ?>
						<li data-role="list-divider"><?=$rec['idorder']?> | <?=$rec['date']?> | <?=$rec['supplier_name']?> |  <?=$rec['username']?> | <?=$rec['totalht']?> <?if (isset($rec['totalht']) && is_numeric($rec['totalht'])): ?>€ H.T.<?endif;?> | Status commande: <?=$rec['status']?> <?if($rec['status'] == 'sent') { ?> | Confirmation : <?=$rec['confirm']?> <? } ?>
						<? if($rec['status'] == 'received') { ?>
						<br />Received on <?=$rec['date_reception']?> by <?=$rec['username_reception']?> | Status reception: 
						<? $status_reception = ($rec['status_reception']) ? 'OK' : 'NOK'; ?><?=$status_reception?>
						<? } ?> 
						
						<? if(!$keylogin) { ?>
						<li data-inset="true" data-split-theme="a"> <a rel="external" data-ajax="false" href="/order/viewProducts/<?=$rec['idorder']?>/<?=$rec['supplier_id']?>/order">Order from this</a></li>						
						<? } ?>
						<?if($rec['status'] != 'draft') { ?>
							<?if($rec['status'] != 'received') { ?>
								<li data-inset="true" data-split-theme="a"> <a rel="external" data-ajax="false" href="/order/viewProducts/<?=$rec['idorder']?>/<?=$rec['supplier_id']?>/reception">Reception</a></li>
							<? } ?>
							<?if($rec['status'] == 'received') { ?>
								<li data-inset="true" data-split-theme="a"> <a rel="external" data-ajax="false" href="/order/viewProducts/<?=$rec['idorder']?>/<?=$rec['supplier_id']?>/viewreception">View/Edit reception</a></li>
							<? } ?>
						<li data-inset="true" data-split-theme="a"> <a rel="external" data-ajax="false" href="/order/downloadOrder/<?=$rec['idorder']?>_<?=$rec['supplier_name']?>">View BDC</a></li>
						<?}?>
					<?php endforeach; ?>
				<?}?>
			</ul>
			<p><? if(!$search) { echo $links; } ?></p>
			</div>
		</div>

<script>
	$(document).ready(function() {
	$("#edate").datepicker({ dateFormat: 'yy-mm-dd' });
	});
</script>
<script>
	$(document).ready(function() {
	$("#sdate").datepicker({ dateFormat: 'yy-mm-dd' });
	});
</script>