<? $title = "Users"; include('jq_header.php'); ?> 
<body>
	<div data-role="page">
		<div data-role="header">
			<a href="/admin/" data-transition="slide" data-icon="home">Home</a>
			<h1>Users</h1>
		</div>
		<div data-role="content">

<h1>Staff</h1>

<div id="infoMessage"><?php echo $message; ?></div>

<table cellpadding=0 cellspacing=10>
	<tr>
		<th><?php echo 'Username';?></th>
		<th><?php echo lang('index_fname_th');?></th>
		<th><?php echo lang('index_lname_th');?></th>
		<th><?php echo lang('index_email_th');?></th>
		<th>Phone</th>
		<? if($users['0']->groups['0']->level >= 2) { ?>
		<th><?php echo lang('index_groups_th');?></th>
		<th><?php echo lang('index_status_th');?> | Delete</th>
		<th><?php echo lang('index_action_th');?></th>
		<? } ?>
	</tr>
	<?php foreach ($users as $user):?>
		<tr>
			<td><?php echo htmlspecialchars($user->username,ENT_QUOTES,'UTF-8');?></td>
            <td><?php echo htmlspecialchars($user->first_name,ENT_QUOTES,'UTF-8');?></td>
            <td><?php echo htmlspecialchars($user->last_name,ENT_QUOTES,'UTF-8');?></td>
            <td><?php echo htmlspecialchars($user->email,ENT_QUOTES,'UTF-8');?></td>
			<td><?php echo htmlspecialchars($user->phone,ENT_QUOTES,'UTF-8');?></td>
			<? if($users['0']->groups['0']->level >= 2) { ?>
			<td>
				<?php foreach ($user->groups as $group):?>
					<?php echo anchor("auth/edit_group/".$group->id, htmlspecialchars($group->name,ENT_QUOTES,'UTF-8')) ;?><br />
                <?php endforeach?>
			</td>
			<td><?php $attributes = array('rel' => 'external', 'data-ajax' => 'false');
			echo ($user->active) ? anchor("auth/deactivate/".$user->id, lang('index_active_link'), $attributes) : anchor("auth/activate/". $user->id, lang('index_inactive_link'), $attributes);?> | <? echo anchor("auth/delete/".$user->id, 'Delete', $attributes);  ?> </td>
			<td><?php echo anchor("auth/edit_user/".$user->id, 'Edit', $attributes) ;?></td>
			<? } ?>
		</tr>
	<?php endforeach;?>
</table>
	
<p><?php if($users['0']->groups['0']->level >= 2) { echo anchor('auth/create_user', lang('index_create_user_link'), $attributes); } ?></p>

	</div><!-- /content -->
	<br /><br />
	<div id="view"></div>
</div><!-- /page -->
<? include('jq_footer.php'); ?>