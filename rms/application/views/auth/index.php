			<?php if($this->ion_auth_acl->has_permission('create_user')) {?>
				<a href="/auth/create_user/" class="ui-btn ui-btn-right" rel="external" data-ajax="false" data-icon="plus"><i class="zmdi zmdi-plus zmd-2x"></i></a><? } ?>
		</div>
		<div data-role="content">

<h1>Staff</h1>

<style>

.row {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  width: 100%;
  margin: 2px;
  padding : 2px;
}

.column {
  display: flex;
  flex-direction: column;
  flex-basis: 100%;
  flex: 1;
  margin: 2px;
  padding : 2px;
}
</style>

<?php
if($message) { ?>
	<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
		<li style="background-color: #e8ffb9;"><?=$message?></li>
	</ul>
<? } ?>
				
<?php foreach ($users as $user):?>
	<? $bgcolor = ""; if($user->active == 0) $bgcolor = "#aaaaaa"; ?>
	<div class="row" style="background-color: <?=$bgcolor?>; border: 1px solid silver;">

	<div class='column'>
		<?php if($this->ion_auth_acl->has_permission('edit_user')) { ?>
			<a data-ajax="false" href="auth/edit_user/<?=$user->id?>">
		<? } ?>
		
		<?php echo htmlspecialchars($user->username,ENT_QUOTES,'UTF-8');?>
		
		<?php if($this->ion_auth_acl->has_permission('edit_user')) { ?>
			</a>
		<? } ?>
		
	</div>
		
	<div class='column'>
		<a href="tel:<?php echo htmlspecialchars($user->phone,ENT_QUOTES,'UTF-8');?>"><?php echo htmlspecialchars($user->phone,ENT_QUOTES,'UTF-8');?></a>
	</div>
				
	<div class='column'><font size="2">
		<a href="mailto:<?php echo htmlspecialchars($user->email,ENT_QUOTES,'UTF-8');?>"><?php echo htmlspecialchars($user->email,ENT_QUOTES,'UTF-8');?></a>
	</font></div>
	
	<? if($this->ion_auth_acl->has_permission('view_groups_user')) { ?>
	<div class='column'><font size="2">
			<?$test_real = $this->ion_auth->is_real_admin($current_user->id);?>
			<?$test_fake = $this->ion_auth->is_admin($current_user->id);?>
			<?php foreach ($user->groups as $group):?>
				<?=htmlspecialchars($group->name,ENT_QUOTES,'UTF-8')?>
            <?php endforeach?>
	</font></div>
	<? } ?>
	
	<? if ($this->ion_auth_acl->has_permission('view_bus_user')) { ?>
	<div class='column'><font size="2">
			<?php foreach ($user->bus as $bu):?>
				<?=htmlspecialchars($bu->name)?><br />
            <?php endforeach?>
	</font></div>
	<? } ?>
	
	<? if($this->ion_auth_acl->has_permission('view_status_user')) { ?>
	<div class='column'><font size="2">
			<?php $attributes = array('rel' => 'external', 'data-ajax' => 'false');
			if($this->ion_auth_acl->has_permission('deactivate_user')){
				echo ($user->active) ? anchor("auth/deactivate/".$user->id, 'Inactive', $attributes) : anchor("auth/activate/". $user->id, 'Active', $attributes);?> <? if($this->ion_auth_acl->has_permission('delete_user')) { ?> | <? echo anchor("auth/delete/".$user->id, 'Delete', $attributes);  ?><? } } ?></font>
	</div>
	<? } ?>
</div>
<?php endforeach;?>


	</div><!-- /content -->
	<br /><br />
	<div id="view"></div>
</div><!-- /page -->
