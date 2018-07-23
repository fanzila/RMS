</div>
	<div data-role="content" data-theme="a">
	<?php $this->load->helper('url'); ?>
<table width="100%" border="1" style="border-collapse:collapse; border-color:#CCCCCC;" cellpadding="4" cellspacing="4">
	<tr>
    <td style="padding:10px;">
      <a data-ajax="false" href="<?php echo site_url()."/pm"?>"><i class="zmdi zmdi-email-open zmd-fw "></i>Inbox</a> &nbsp;&nbsp;&nbsp;
      <? if($this->ion_auth_acl->has_permission('additional_menu_pm')){?>
        <!--<a data-ajax="false" href="<php echo site_url()."/pm/messages/".MSG_UNREAD?>">Unread</a> &nbsp;&nbsp;&nbsp; -->
        <a data-ajax="false" href="<?php echo site_url()."/pm/messages/".MSG_SENT?>"><i class="zmdi zmdi-mail-send zmd-fw"></i>Sent</a> &nbsp;&nbsp;&nbsp;
        <a data-ajax="false" href="<?php echo site_url()."/pm/messages/".MSG_DELETED?>"><i class="zmdi zmdi-archive zmd-fw"></i>Archived</a> &nbsp;&nbsp;&nbsp;
        <? if($this->ion_auth_acl->has_permission('send_message')){?>
        <a data-ajax="false" href="<?php echo site_url()."/pm/send"?>"><i class="zmdi zmdi-plus-circle-o zmd-fw"></i>New Report</a>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <? } ?>
      <? } ?>
    </td>
	</tr>
	<tr>
    <td style="padding:10px;">
      <?php
        $action = '/pm/messages';

        if (isset($type) && !empty($type))
          $action .= '/' . $type;
      ?>
      <form method="POST" action="<?= $action ?>">
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <label for="reports-from-search">From:</label>
            <select name="from" id="reports-from-search">
              <option value=""></option>
              <?php foreach ($users as $id => $username) { ?>
                <option value="<?= $id ?>" <?php if ($from == $id) echo 'selected'; ?>><?= $username ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <label for="reports-to-search">To:</label>
            <select name="to" id="reports-to-search">
              <option value=""></option>
              <?php foreach ($users as $id => $username) { ?>
                <option value="<?= $id ?>" <?php if ($to == $id) echo 'selected'; ?>><?= $username ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4">
            <label for="reports-subject-search">Search on subject:</label>
            <input type="search" name="search" id="reports-subject-search" value="<?= $search ?>" />
          </div>
          <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
            <input type="submit" value="Search" />
          </div>
        </div>
      </form>
    </td>
	</tr>
</table>
<br />
