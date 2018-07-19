</div>

<div data-role="content">
  <form action="/skills/duplicate/<?= $skill->id ?>" method="POST" target="_parent">
    <?php if ($error) { ?>
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <p><?= $error ?></p>
        </div>
      </div>
    <?php } ?>

    <div class="row">
      <div class="col-xs-8 col-sm-6 col-md-6 col-lg-6">
        <select name="id_bu">
          <?php foreach ($bus as $bu) { ?>
            <option value="<?= $bu->id ?>" <?php if ($bu->id == $skill->id_bu) echo 'disabled'; ?>><?= $bu->name ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="col-xs-4 col-sm-6 col-md-6 col-lg-6">
        <input type="submit" value="Duplicate">
      </div>
    </div>
  </form>
</div>
