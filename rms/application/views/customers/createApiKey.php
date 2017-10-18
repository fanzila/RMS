</div>
<div data-role="content" data-theme="a">
  <?if (isset($error)) : ?>
    <span> <?=$error?> </span>
  <? elseif (isset($apiKey)) : ?>
    <span> You API key for the BU: <?=$buName?> is: <?=$apiKey?> </span>
    <h5>DO NOT LOSE IT ! You'll have to create another one if that happens.</h5>
  <? else : ?>
    <span> There was an error while generating your API key. Please retry. </span>
  <? endif; ?>
</div>
</div> <!-- page -->