		</div>
		<div data-role="content">

<h1><?php echo lang('edit_user_heading');?></h1>
<p><?php echo lang('edit_user_subheading');?></p>

<div id="infoMessage"><?php echo $message;?></div>

<?php
$attributes = array('rel' => 'external', 'data-ajax' => 'false');
echo form_open(uri_string(), $attributes);
?>
<div class="row">

  <div class="col-xs-12 col-sm-6 col-md-5">
    <div class="box">
	  <p>
      		<?php echo 'Username';?> <br />
      		<?php echo form_input($username);?>
	  </p>
      
	  <p>
            <?php echo lang('edit_user_fname_label', 'first_name');?>
            <?php echo form_input($first_name);?>
      </p>

      <p>
            <?php echo lang('edit_user_lname_label', 'last_name');?>
            <?php echo form_input($last_name);?>
      </p>

      <p>
            <?php echo lang('edit_user_phone_label', 'phone');?>
            <?php echo form_input($phone);?>
      </p>

      <p>
            Comment: <br />
            <?php echo form_input($comment);?>
      </p>

      <p>
            <?php echo lang('edit_user_email_label', 'email');?>
            <?php echo form_input($email);?>
      </p>

      <p>
            <?php echo lang('edit_user_password_label', 'password');?>
            <?php echo form_input($password);?>
      </p>

      <p>
            <?php echo lang('edit_user_password_confirm_label', 'password_confirm');?>
            <?php echo form_input($password_confirm);?>
      </p>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6 col-md-7">
    <div class="row">
    <div class="col-xs-6 col-sm-6 col-md-6">
              <div class="box">
        <?php if ($this->ion_auth->is_admin()): ?>

            <h3><?php echo lang('edit_user_groups_heading');?></h3>
            <?php foreach ($groups as $group):?>
                <label class="checkbox">
                <?php
                    $gID=$group['id'];
                    $checked = null;
                    $item = null;
                    foreach($currentGroups as $grp) {
                        if ($gID == $grp->id) {
                            $checked= ' checked="checked"';
                        break;
                        }
                    }
				if($group['id'] == 1 AND $current_user_groups[0]->level < 3) { 
					echo ""; 
				} else {
                ?>
                <input type="checkbox" name="groups[]" value="<?php echo $group['id'];?>"<?php echo $checked;?>>
                <?php echo htmlspecialchars($group['name'],ENT_QUOTES,'UTF-8');?>
				<? } ?>
                </label>
            <?php endforeach?>
              </div></div>
<div class="col-xs-6 col-sm-6 col-md-6"><div class="box">
          <h3><?php echo lang('edit_user_bus_heading');?></h3>
          <?php foreach ($bus as $bu):?>
              <label class="checkbox">
              <?php
                  $bID=$bu['id'];
                  $checked = null;
                  $item = null;
                  foreach($currentBus as $up) {
                      if ($bID == $up->id) {
                          $checked= ' checked="checked"';
                      break;
                      }
                  }
              ?>
              <input type="checkbox" name="bus[]" value="<?php echo $bu['id'];?>"<?php echo $checked;?>>
              <?php echo htmlspecialchars($bu['name'],ENT_QUOTES,'UTF-8');?>
              </label>
          <?php endforeach?>

      <?php endif ?>

      <?php echo form_hidden('id', $user->id);?>
      <?php echo form_hidden($csrf); ?>
</div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12">
  <p><?php echo form_submit('submit', lang('edit_user_submit_btn'));?></p>
</div>

  </div>
</div>
<?php echo form_close();?>
	</div><!-- /content -->
	<br /><br />
	<div id="view"></div>
</div><!-- /page -->