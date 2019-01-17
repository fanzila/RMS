<?php

$supplier_id = property_exists($supplier, 'id') ? $supplier->id : 'create';

?>

<form class="supplier-update" id="supplier-<?= $supplier_id ?>" name="supplier-<?= $supplier_id ?>" method="post" action="/supplier/save">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="box" style="background-color: #fbf19e;">
        <h3 style="padding: 0.5em;">Main informations</h3>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <div class="box">
        <label for="name-<?= $supplier_id ?>">Name:</label>
        <input id="name-<?= $supplier_id ?>" type="text" name="name" value="<?= stripslashes($supplier->name) ?>" data-clear-btn="true" />
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <div class="box">
        <label for="main-product-<?= $supplier_id ?>">Main product:</label>
        <input id="main-product-<?= $supplier_id ?>" type="text" name="main_product" value="<?= stripslashes($supplier->main_product) ?>" />
      </div>
    </div>
  </div>

  <div class="row middle-xs middle-sm middle-md middle-lg">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
      <div class="box">
        <label for="category-<?= $supplier_id ?>">Category:</label>
        <select id="category-<?= $supplier_id ?>" name="id_category">
          <?php foreach ($categories as $category) { ?>
            <option value="<?= $category->id ?>"
              <?php if (!$category->active) echo 'disabled'; ?>
              <?php if ($category->name === $supplier->category_name) echo 'selected'; ?>
              ><?= $category->name ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
      <div class="box">
        <label for="location-<?= $supplier_id ?>">Location:</label>
        <input id="location-<?= $supplier_id ?>" type="text" name="location" value="<?= stripslashes($supplier->location) ?>" data-clear-btn="true" />
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
      <div class="box">
        <label for="delivery-days-<?= $supplier_id ?>">Delivery days:</label>
        <input id="delivery-days-<?= $supplier_id ?>" type="text" name="delivery_days" value="<?= stripslashes($supplier->delivery_days) ?>" data-clear-btn="true" />
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
      <div class="box">
        <label for="order-mode-<?= $supplier_id ?>">Order method:</label>
        <select id="order-mode-<?= $supplier_id ?>" name="order_method">
          <?php foreach ($order_methods as $order_method) { ?>
            <option value="<?= $order_method ?>"
              <?php if ($order_method === $supplier->order_method) echo 'selected'; ?>
              ><?= $order_method ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <div class="box">
        <label for="website-<?= $supplier_id ?>">Website:</label>
        <input id="website-<?= $supplier_id ?>" type="text" name="website" value="<?= $supplier->website ?>" data-clear-btn="true" />
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
      <div class="box">
        <label for="simple-order-form-<?= $supplier_id ?>">Simple order form:</label>
        <select id="simple-order-form-<?= $supplier_id ?>" name="simple_order_form">
          <option value="1" <? if($supplier->simple_order_form == 1) echo "selected"; ?>>Yes</option>
          <option value="0" <? if($supplier->simple_order_form == 0) echo "selected"; ?>>No</option>
        </select>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
      <div class="box">
        <label for="no-chased-email-<?= $supplier_id ?>">No chased email:</label>
        <select id="no-chased-email-<?= $supplier_id ?>" name="no_chased_email">
          <option value="1" <? if($supplier->no_chased_email == 1) echo "selected"; ?>>Yes</option>
          <option value="0" <? if($supplier->no_chased_email == 0) echo "selected"; ?>>No</option>
        </select>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
      <div class="box">
        <label for="active-<?= $supplier_id ?>">Active: (on or off)</label>
        <select id="active-<?= $supplier_id ?>" name="active">
          <option value="1" <? if($supplier->active == 1) echo "selected"; ?>>Yes</option>
          <option value="0" <? if($supplier->active == 0) echo "selected"; ?>>No</option>
        </select>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="box" style="background-color: #fbf19e;">
        <h3 style="padding: 0.5em;">Payment</h3>
      </div>
    </div>
  </div>

  <div class="row middle-xs middle-sm middle-md middle-lg">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
      <div class="box">
        <label for="carriage-paid-<?= $supplier_id ?>">Carriage paid:</label>
        <input id="carriage-paid-<?= $supplier_id ?>" type="text" name="carriage_paid" value="<?= $supplier->carriage_paid ?>" data-clear-btn="true" />
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
      <div class="box">
        <label for="payment-type-<?= $supplier_id ?>">Payment type:</label>
        <select id="payment-type-<?= $supplier_id ?>" name="payment_type">
          <?php foreach ($payment_types as $payment_type) { ?>
            <option value="<?= $payment_type ?>"
              <?php if ($payment_type === $supplier->payment_type) echo 'selected'; ?>
              ><?= $payment_type ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
      <div class="box">
        <label for="payment-delay-<?= $supplier_id ?>">Payment delay:</label>
        <input id="payment-delay-<?= $supplier_id ?>" type="text" name="payment_delay" value="<?= stripslashes($supplier->payment_delay) ?>" data-clear-btn="true" />
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="box" style="background-color: #fbf19e;">
        <h3 style="padding: 0.5em;">Contact</h3>
      </div>
    </div>
  </div>

  <div class="row middle-xs middle-sm middle-md middle-lg">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
      <div class="box">
        <label for="contact-sale-name-<?= $supplier_id ?>">Contact sale name:</label>
        <input id="contact-sale-name-<?= $supplier_id ?>" type="text" name="contact_sale_name" value="<?= $supplier->contact_sale_name ?>" data-clear-btn="true" />
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
      <div class="box">
        <label for="contact-sale-tel-<?= $supplier_id ?>">Contact sale tel:</label>
        <input id="contact-sale-tel-<?= $supplier_id ?>" type="text" name="contact_sale_tel" value="<?= $supplier->contact_sale_tel ?>" data-clear-btn="true" />
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
      <div class="box">
        <label for="contact-sale-email-<?= $supplier_id ?>">Contact sale email:</label>
        <input id="contact-sale-email-<?= $supplier_id ?>" type="text" name="contact_sale_email" value="<?= $supplier->contact_sale_email ?>" data-clear-btn="true" />
      </div>
    </div>
  </div>

  <div class="row middle-xs middle-sm middle-md middle-lg">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
      <div class="box">
        <label for="contact-order-name-<?= $supplier_id ?>">Contact order name:</label>
        <input id="contact-order-name-<?= $supplier_id ?>" type="text" name="contact_order_name" value="<?= $supplier->contact_order_name ?>" data-clear-btn="true" />
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
      <div class="box">
        <label for="contact-order-tel-<?= $supplier_id ?>">Contact order tel:</label>
        <input id="contact-order-tel-<?= $supplier_id ?>" type="text" name="contact_order_tel" value="<?= $supplier->contact_order_tel ?>" data-clear-btn="true" />
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
      <div class="box">
        <label for="contact-order-email-<?= $supplier_id ?>">Contact order email:</label>
        <input id="contact-order-email-<?= $supplier_id ?>" type="text" name="contact_order_email" value="<?= $supplier->contact_order_email ?>" data-clear-btn="true" />
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="box" style="background-color: #fbf19e;">
        <h3 style="padding: 0.5em;">Comments</h3>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
      <div class="box">
        <label for="comment-internal-<?= $supplier_id ?>">Comment internal:</label>
        <textarea id="comment-internal-<?= $supplier_id ?>" rows="5" name="comment_internal"><?= stripslashes($supplier->comment_internal) ?></textarea>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
      <div class="box">
        <label for="comment-order-<?= $supplier_id ?>">Comment order:</label>
        <textarea id="comment-order-<?= $supplier_id ?>" rows="5" name="comment_order"><?= stripslashes($supplier->comment_order) ?></textarea>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
      <div class="box">
        <label for="comment-delivery-<?= $supplier_id ?>">Comment delivery:</label>
        <textarea id="comment-delivery-<?= $supplier_id ?>" rows="5" name="comment_delivery"><?= stripslashes($supplier->comment_delivery) ?></textarea>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
      <div class="box">
        <label for="comment-delivery-info-<?= $supplier_id ?>">Comment delivery info:</label>
        <textarea id="comment-delivery-info-<?= $supplier_id ?>" rows="5" name="comment_delivery_info"><?= stripslashes($supplier->comment_delivery_info) ?></textarea>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="box">
        <input type="submit" value="Save">
      </div>
    </div>
  </div>

  <?php
    if (property_exists($supplier, 'id') && !empty($supplier->id))
      echo '<input type="hidden" name="id" value="' . $supplier_id . '">';
  ?>
</form>
