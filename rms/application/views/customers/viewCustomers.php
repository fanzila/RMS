<link rel="stylesheet" href="/public/viewCustomers.css">
</div>
<div data-role="content" data-theme="a">
  <div class="customer-list" data-role="collapsible">
    <h3>Customers List</h3>
    <?if (isset($customers) && !empty($customers)) : ?>
    <table cellpadding="5" width="70%" class="customer-list">
      <tr>
        <th>email</th>
        <th>Client IP</th>
        <th>Platform</th>
        <th>Browser/Version</th>
        <th>Client Mac</th>
        <th>Opt Out</th>
        <th>Date</th>
      </tr>
      <?foreach ($customers as $customer) : ?>
        <tr>
          <td><?=$customer['email']?></td>
          <td><?=$customer['clientIP']?></td>
          <td><?=$customer['clientUserAgent']['platform']?></td>
          <td><?=$customer['clientUserAgent']['browser'] . '/' . $customer['clientUserAgent']['version']?></td>
          <td><?=$customer['clientMac']?></td>
          <td><?=$customer['optout']?></td>
          <td><?=$customer['date']?></td>
        </tr>
      <?endforeach;?>
    </table>
    <? else : ?>
    <span> No customers in Database </span>
    <? endif; ?>
  </div>
  <div data-role="collapsible">
    <h3>Stats</h3>
  </div>
</div>
</div> <!-- page -->