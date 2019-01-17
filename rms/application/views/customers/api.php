</div>
<div data-role="content" data-theme="a">
  <div data-role="collapsible">
    <h3> Keys</h3>
    <div>
      <? if (!empty($keys['api_key'])) : ?>
        <table cellpadding="5" width="70%" style="text-align: center;">
          <tr>
            <th>ID BU</th>
            <th>Name</th>
            <th>Key</th>
          </tr>
          <tr>
            <td><?=$keys['id']?></td>
            <td><?=$keys['name']?></td>
            <td><?=$keys['api_key']?></td>
          </tr>
        </table>
      <? else : ?>
        <p><b>No Api Keys Recorded</b></p>
      <? endif; ?>
    </div>
      <? if (empty($keys['api_key'])) { ?>
        <form id="apiKey" name="apiKey" method="post" data-ajax="false" action="/customers/createApikey">
        <input type="submit" name="submit" value="GENERATE" />
      <? } else { ?>
        <p><b>You already generated a key, please delete it using the crud below to be able to generate a new one.</b></p>
      <? } ?>
  </form>
  </div>
  <div data-role="collapsible">
    <h3>CRUD KEYS</h3>
    <iframe src="/crud/customers_api_keys/<?=$id_bu?>" width="98%" height="600" style="border-radius: 3px; border-style: solid;"></iframe>
  </div>
</div>
</div> <!-- page -->