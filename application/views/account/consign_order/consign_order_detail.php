<?php if($doc->status == 2) : ?>
<?php   $this->load->view('cancle_watermark') ?>
<?php endif; ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1000px;">
      <thead>
        <tr class="font-size-12">
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-150">รหัส</th>
          <th class="min-width-150">สินค้า</th>
          <th class="fix-width-120">บิลขาย(POS)</th>
          <th class="fix-width-120">ใบสั่งขาย</th>
          <th class="fix-width-100 text-right">ราคา</th>
          <th class="fix-width-100 text-right">ส่วนลด</th>
          <th class="fix-width-100 text-right">จำนวน</th>
          <th class="fix-width-100 text-right">มูลค่า</th>
        <?php if($doc->ref_type != 4) : ?>
          <th class="fix-width-40"></th>
        <?php endif; ?>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php  $no = 1; ?>
<?php  $totalQty = 0; ?>
<?php  $totalAmount = 0; ?>

<?php if(! empty($details)) : ?>
<?php  foreach($details as $rs) : ?>
        <tr class="font-size-12 rox" id="row-<?php echo $rs->id; ?>">
          <td class="middle text-center no">
            <?php echo $no; ?>
          </td>
          <td class="middle text-center">
            <?php echo $rs->product_code; ?>
          </td>
          <td class="middle">
            <?php echo $rs->product_name; ?>
          </td>
          <td class="middle">
            <?php echo $rs->ref_code; ?>
          </td>
          <td class="middle"><?php echo $rs->so_code; ?></td>
          <td class="middle text-right">
            <span class="price" id="price-<?php echo $rs->id; ?>"><?php echo number($rs->price,2); ?></span>
            <input type="number" class="form-control input-xs text-center hide input-price" id="input-price-<?php echo $rs->id; ?>" value="<?php echo round($rs->price,2); ?>" />
          </td>
          <td class="middle text-right">
            <span class="disc" id="disc-<?php echo $rs->id; ?>"><?php echo $rs->discount; ?></span>
            <input type="text" class="form-control input-xs text-center hide input-disc" id="input-disc-<?php echo $rs->id; ?>" value="<?php echo $rs->discount; ?>" />
          </td>
          <td class="middle text-right qty" id="qty-<?php echo $rs->id; ?>">
            <?php echo number($rs->qty); ?>
          </td>
          <td class="middle text-right amount" id="amount-<?php echo $rs->id; ?>">
            <?php echo number($rs->amount, 2); ?>
          </td>
        <?php if($doc->ref_type != 4) : ?>
          <td class="middle text-center">
          <?php if($rs->status == 0 && ($this->pm->can_edit OR $this->pm->can_delete)) : ?>
            <button type="button" class="btn btn-minier btn-danger" onclick="deleteRow('<?php echo $rs->id; ?>', '<?php echo $rs->product_code; ?>')">
              <i class="fa fa-trash"></i>
            </button>
          <?php endif; ?>
          </td>
        <?php endif; ?>
        </tr>

<?php  $no++; ?>
<?php  $totalQty += $rs->qty; ?>
<?php  $totalAmount += $rs->amount; ?>
<?php endforeach; ?>
<?php endif; ?>
        <tr id="total-row">
          <td colspan="7" class="middle text-right"><strong>รวม</strong></td>
          <td id="total-qty" class="middle text-right"><?php echo number($totalQty); ?></td>
          <td id="total-amount" class="middle text-right"><?php echo number($totalAmount,2); ?></td>
          <?php if($doc->ref_type != 4) : ?>
            <td></td>
          <?php endif; ?>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script id="new-row-template" type="text/x-handlebarsTemplate">
<tr class="font-size-12 rox" id="row-{{id}}">
  <td class="middle text-center no"></td>
  <td class="middle text-center">{{barcode}}</td>
  <td class="middle">{{product}}</td>
  <td class="middle">{{ref_code}}</td>
  <td class="middle text-right">
    <span class="price" id="price-{{id}}">{{price}}</span>
    <input type="number" class="form-control input-xs text-center hide input-price" id="input-price-{{id}}" value="{{price}}" />
  </td>
  <td class="middle text-right">
    <span class="disc" id="disc-{{id}}">{{discount}}</span>
    <input type="text" class="form-control input-xs text-center hide input-disc" id="input-disc-{{id}}" value="{{discount}}" />
  </td>
  <td class="middle text-right qty" id="qty-{{id}}">{{qty}}</td>
  <td class="middle text-right amount" id="amount-{{id}}">{{amount}}</td>
  <td class="middle text-center">
    <button type="button" class="btn btn-xs btn-danger" onclick="deleteRow('{{id}}', '{{product}}')">
      <i class="fa fa-trash"></i>
    </button>
  </td>
</tr>
</script>


<script id="row-template" type="text/x-handlebarsTemplate">
  <td class="middle text-center no"></td>
  <td class="middle text-center">{{barcode}}</td>
  <td class="middle hide-text">{{product}}</td>
  <td class="middle hide-text">{{ref_code}}</td>
  <td class="middle text-right price" id="price-{{id}}">{{price}}</td>
  <td class="middle text-right disc" id="disc-{{id}}">{{discount}}</td>
  <td class="middle text-right qty" id="qty-{{id}}">{{qty}}</td>
  <td class="middle text-right amount" id="amount-{{id}}">{{amount}}</td>
  <td class="middle text-center">
    <button type="button" class="btn btn-xs btn-danger" onclick="deleteRow('{{id}}', '{{product}}')">
      <i class="fa fa-trash"></i>
    </button>
  </td>
</script>

<script id="detail-template" type="text/x-handlebarsTemplate">
{{#each this}}
  {{#if @last}}
  <tr id="total-row">
    <td colspan="6" class="middle text-right"><strong>รวม</strong></td>
    <td id="total-qty" class="middle text-center">{{ total_qty }}</td>
    <td id="total-amount" colspan="2" class="middle text-center">{{ total_amount }}</td>
  </tr>
  {{else}}
  <tr class="font-size-12 rox" id="row-{{id}}">
    <td class="middle text-center no"></td>
    <td class="middle text-center">{{barcode}}</td>
    <td class="middle">{{product}}</td>
    <td class="middle">{{ref_code}}</td>
    <td class="middle text-center">
      <input type="number" class="form-control input-xs text-center padding-5 price" min="0" id="price-{{id}}" value="{{price}}" onKeyup="reCal('{{id}}')" onChange="reCal('{{id}}')" />
    </td>
    <td class="middle text-center">
      <input type="text" class="form-control input-xs text-center disc" id="disc-{{id}}" value="{{discount}}" onKeyup="recal('{{id}}')" onChange="recal('{{id}}')" />
    </td>
    <td class="middle text-center">
      <input type="number" class="form-control input-xs text-center qty" min="0" id="qty-{{id}}" value="{{qty}}" onKeyup="reCal('{{id}}')" onChange="reCal('{{id}}')" />
    </td>
    <td class="middle text-right amount" id="amount-{{id}}">{{ amount }}</td>
    <td class="middle text-center">
      <button type="button" class="btn btn-xs btn-danger" onclick="deleteRow('{{id}}', '{{product}}')"><i class="fa fa-trash"></i></button>
    </td>
  </tr>
  {{/if}}

{{/each}}
</script>
