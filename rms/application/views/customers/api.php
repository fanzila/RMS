</div>
<div data-role="content" data-theme="a">
  <div>
    <? if (isset($keys)) : ?>
      <table>
        <tr>
          <th>ID</th>
          <th>Application Name</th>
          <th>Key</th>
        </tr>
      <? foreach ($keys as $key) : ?>
        <tr>
          <td><?=$key['id']?></td>
          <td><?=$key['app_name']?></td>
          <td><?=$key['key']?></td>
        </tr>
      <? endforeach; ?>
      </table>
    <? else : ?>
      <span>No Api Keys Recorded</span>
    <? endif; ?>
  </div>
  <div data-role="collapsible">
    <h3> Generate a key</h3>
  <form id="apiKey" name="apiKey" method="post" data-ajax="false" action="/customers/createApikey">
      <label for="app_name">Application Name (Max 64 chararcters, alphanumeric only)</label>
      <input type="text" name="app_name" id="app_name" data-clear-btn="true" data-mini="true" required pattern="[a-zA-Z0-9]+"/>
      <input type="submit" name="submit" value="GENERATE" />
    </form>
  </div>
  <div data-role="collapsible">
    <h3>CRUD KEYS</h3>
    <iframe src="/crud/customers_api_keys" width="98%" height="600" style="border-radius: 3px; border-style: solid;"></iframe>
  </div>
</div>
</div> <!-- page -->