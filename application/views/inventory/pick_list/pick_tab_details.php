<div id="details-tab" class="tab-pane fade" style="height:400px; overflow:auto;">
  <table class="table table-striped tableFixHead" style="min-width:970px;">
    <thead>
      <tr class="font-size-11">
        <th class="fix-width-40 text-center fix-header">#</th>
        <th class="fix-width-100 fix-header">เลขที่</th>
        <th class="fix-width-150 fix-header">MKP No.</th>
        <th class="fix-width-150 fix-header">ช่องทางขาย</th>
        <th class="fix-width-200 fix-header">รหัส</th>
        <th class="min-width-200 fix-header">สินค้า</th>
        <th class="fix-width-80 fix-header">จำนวน</th>
      </tr>
    </thead>
    <tbody id="details-table">
  <?php if( ! empty($details)) : ?>
    <?php $no = 1; ?>
    <?php $sumDetails = 0; ?>
    <?php foreach($details as $rs) : ?>
      <tr class="font-size-11">
        <td class="text-center"><?php echo $no; ?></td>
        <td><?php echo $rs->order_code; ?></td>
        <td><?php echo $rs->reference; ?></td>
        <td><?php echo $rs->channels_name; ?></td>
        <td><?php echo $rs->product_code; ?></td>
        <td class="hide-text"><?php echo $rs->product_name; ?></td>
        <td class="text-center pick-detail"><?php echo number($rs->qty); ?></td>
      </tr>
      <?php $no++; ?>
      <?php $sumDetails += $rs->qty; ?>
    <?php endforeach; ?>
    <tr>
      <td colspan="6" class="text-right">Total</td>
      <td class="text-center"><?php echo number($sumDetails); ?></td>
    </tr>
  <?php endif; ?>
    </tbody>
  </table>
</div>

<script id="details-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    {{#if @last}}
      <tr>
        <td colspan="6" class="text-right">Total</td>
        <td class="text-center">{{totalQty}}</td>
      </tr>
    {{else}}
      <tr class="font-size-11">
        <td class="text-center d-no">{{no}}</td>
        <td>{{order_code}}</td>
        <td>{{reference}}</td>
        <td>{{channels}}</td>
        <td>{{product_code}}</td>
        <td class="hide-text">{{product_name}}</td>
        <td class="text-center pick-detail">{{qty}}</td>
      </tr>
    {{/if}}
  {{/each}}
</script>
