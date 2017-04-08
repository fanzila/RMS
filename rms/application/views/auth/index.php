			<?php if($users['0']->groups['0']->level >= 2) {?>
				<a href="/auth/create_user/" class="ui-btn ui-btn-right" rel="external" data-ajax="false" data-icon="plus"><i class="zmdi zmdi-plus zmd-2x"></i></a><? } ?>
		</div>
		<div data-role="content">

<h1>Staff</h1>

<?if($message) { ?>
	<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
		<li style="background-color: #e8ffb9;"><?=$message?></li>
	</ul>
<? } ?>


<table data-role="table" id="table-custom-2" data-mode="reflow" class="ui-body-d ui-shadow table-stripe ui-responsive" data-column-popup-theme="a">
	<thead>
		<tr class="ui-bar-d">
			<th>Username</th>
			<th data-priority="1"><?php echo lang('index_fname_th');?></th>
			<th data-priority="3"><?php echo lang('index_lname_th');?></th>
			<th data-priority="4"><?php echo lang('index_email_th');?></th>
			<th data-priority="5">Phone</th>
			<th data-priority="6">Comment</th>
				<? if($users['0']->groups['0']->level >= 2) { ?>
			<th><?php echo lang('index_groups_th');?></th>
			<th>BU</th>
				<? if($users['0']->groups['0']->level >= 3) { ?>
			<th data-priority="6"><?php echo lang('index_status_th');?> <? if($user_groups->level >= 10) { ?> | Delete <? } ?></th>
				<? } ?>
			<th data-priority="5"><?php echo lang('index_action_th');?></th>
				<? } ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($users as $user):?>
		<? $bgcolor = ""; if($user->active == 0) $bgcolor = "#aaaaaa"; ?>
		<tr style="background-color: <?=$bgcolor?>">
			<td><?php echo htmlspecialchars($user->username,ENT_QUOTES,'UTF-8');?></td>
            <td><?php echo htmlspecialchars($user->first_name,ENT_QUOTES,'UTF-8');?></td>
            <td><?php echo htmlspecialchars($user->last_name,ENT_QUOTES,'UTF-8');?></td>
            <td><?php echo htmlspecialchars($user->email,ENT_QUOTES,'UTF-8');?></td>
			<td><?php echo htmlspecialchars($user->phone,ENT_QUOTES,'UTF-8');?></td>
			<td><?php echo htmlspecialchars($user->comment,ENT_QUOTES,'UTF-8');?></td>
			<? if($users['0']->groups['0']->level >= 2) { ?>
			<td>
				<?$test_real = $this->ion_auth->is_real_admin($current_user->id);?>
				<?$test_fake = $this->ion_auth->is_admin($current_user->id);?>
				<?php foreach ($user->groups as $group):?>
					<? if($user_groups->level >= 5) { ?><?php if($test_real || ($test_fake && $group->name != 'admin')) echo anchor("auth/edit_group/".$group->id, htmlspecialchars($group->name,ENT_QUOTES,'UTF-8')) ;?> <? } else { ?> <?=htmlspecialchars($group->name,ENT_QUOTES,'UTF-8')?><? } ?>
					<br />
                <?php endforeach?>
			</td>
			<td>
				<?php foreach ($user->bus as $bu):?>
					<?=htmlspecialchars($bu->name)?><br />
                <?php endforeach?>
			</td>
			<td><?php $attributes = array('rel' => 'external', 'data-ajax' => 'false');
			if($test_real || ($test_fake && $group->name != 'admin')){
				echo ($user->active) ? anchor("auth/deactivate/".$user->id, lang('index_active_link'), $attributes) : anchor("auth/activate/". $user->id, lang('index_inactive_link'), $attributes);?> <? if($user_groups->level >= 5) { ?> | <? echo anchor("auth/delete/".$user->id, 'Delete', $attributes);  ?><? }} ?></td>
			
			<td><?php if($test_real || ($test_fake && $group->name != 'admin')) echo anchor("auth/edit_user/".$user->id, 'Edit', $attributes) ;?></td>
			<? } ?>
		</tr>
	<?php endforeach;?>
	</tbody>
</table>

	</div><!-- /content -->
	<br /><br />
	<div id="view"></div>
</div><!-- /page -->
