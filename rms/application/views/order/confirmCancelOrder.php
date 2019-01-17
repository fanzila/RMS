  </div>
  <div data-role="content">
    <h2>Êtes vous sur de vouloir annuler cette commande ?</h2>
    <a data-ajax="false" target="_blank" href="/order/downloadOrder/<?=$order['idorder']?>_<?=strtoupper($supplier['name'])?>"><img src="/order/pdfPreview/<?=$filename?>" style="border: 1px solid #e9e9e9;" width="612" height="792"></a>
    <a data-role="button" href="/order/cancelOrder/<?=$order['idorder']?>/<?=$supplier['id']?>/true" style="background-color: #e33030;" id="cancelConfirm">CANCEL ORDER</a>
  </div>
</div>
<script>
  $('#cancelConfirm').on('click', function(){
    return (confirm('Un email va être envoyé au fournisseur, êtes-vous sur de vouloir annuler cette commande ?'));
  });
</script>