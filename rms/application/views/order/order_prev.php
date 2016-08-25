		</div>
		<div data-role="content" data-theme="a">
			
			<?$attributes = array('id' => "logOrder", 'name' => "logOrder");
			echo form_open("order/getprevorder", $attributes);?>
				<table width="100%" style="background-color: #ffffff; border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
					<tr>
						<td colspan="4" style="background-color: #fbf19e;">Search by <font size="2">(all optionnal but fill at least one)</font> :
						</td>
					</tr>
					<tr>
						<td width="10%"><label for="supplier" id="label">Supplier:</label></td>
						<td width="40%"><input type="text" id="supplier" name="supplier" value="" data-clear-btn="true" /></td>
						<td width="10%"><label for="user" id="label">Username:</label></td>
						<td width="40%"><input type="text" id="user" name="user" value="" data-clear-btn="true" /></td>
					</tr>
					<tr>
						<td><label for="idorder" id="label">ID:</label></td>
						<td><input type="text" id="idorder" name="idorder" value="" data-clear-btn="true" /></td>
						<td><label for="status" id="label">Status:</label></td>
						<td><input type="text" id="status" name="status" value="" data-clear-btn="true" /></td>
					</tr>
					<tr>
						<td><label for="sdate" id="label">From the : (YYYY-MM-DD)</label></td>
						<td><input type="text" id="sdate" name="sdate" value="" data-clear-btn="true" /></td>
						<td><label for="edate" id="label">To the : (YYYY-MM-DD)</label></td>
						<td><input type="text" id="edate" name="edate" value="" data-clear-btn="true" /></td>
					</tr>
					<tr>
						<td colspan="4">
							<?$attributes = array('id' => "sub", 'name' => "submit");
							echo form_submit($attributes, 'Search');?>
						</td>
					</tr>
				</table>
			</form>
			<ul data-role="listview" data-inset="true"  data-filter="false">

				<? if(!empty($results)) { ?>
					<? foreach ($results as $rec): ?>
						<li data-role="list-divider"><?=$rec['date']?> | <?=$rec['supplier_name']?> |  <?=$rec['first_name']?> <?=$rec['last_name']?> | ID: <?=$rec['idorder']?> | Status commande: <?=$rec['status']?> <?if($rec['status'] == 'sent') { ?> | Status confirmation : <?=$rec['confirm']?> <? } ?>

						<?if($rec['status'] != 'draft') { ?>
							<li data-inset="true" data-split-theme="a"> <a rel="external" data-ajax="false" href="/order/downloadOrder/<?=$rec['idorder']?>_<?=$rec['supplier_name']?>">View BDC</a></li>
							<?}?>
						<li data-inset="true" data-split-theme="a"> <a rel="external" data-ajax="false" href="/order/viewProducts/0/<?=$rec['idorder']?>">Open order</a></li>
					<?php endforeach; ?>
				<?}?>
			</ul>
			<p><?php echo $links; ?></p>
			</div>
		</div>