<div id="orders-tab" class="tab-pane fade active in" >
  <div class="row" style="margin:0px; padding-top:15px;">

    <?php if($doc->status == 'P') : ?>
      <div class="col-lg-2 col-md-2-harf col-sm-2 hidden-xs">&nbsp;</div>
      <div class="col-lg-2-harf col-md-3-harf col-sm-4 col-xs-6 padding-5">
        <input type="text" class="form-control input-sm text-center" id="order-ref" value="" placeholder="Scan Order Number" autofocus />
      </div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
        <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addOrderByRef()">Add</button>
      </div>
      <div class="col-lg-5 col-md-2-harf col-sm-2-harf hidden-xs">&nbsp;</div>
      <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-3 padding-5">
        <button type="button" class="btn btn-xs btn-danger btn-100 pull-right" onclick="deleteOrders()">ลบออเดอร์</button>
      </div>
      <div class="divider"></div>
    <?php endif; ?>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0" style="height:400px; overflow:auto;">
      <table class="table table-striped tableFixHead">
        <thead>
          <tr>
            <?php if($doc->status == 'P') : ?>
              <th class="fix-width-50 text-center fix-header">
                <label>
                  <input type="checkbox" class="ace chk-od-all" onchange="chkOrderTabAll($(this))" />
                  <span class="lbl"></span>
                </label>
              </th>
            <?php endif; ?>
            <th class="fix-width-40 text-center fix-header">#</th>
            <th class="fix-width-100 fix-header">เลขที่</th>
            <th class="fix-width-150 fix-header">MKP No.</th>
            <th class="min-width-150 fix-header">ช่องทางขาย</th>
          </tr>
        </thead>
        <tbody id="order-tab-table">
          <?php if( ! empty($orders)) : ?>
            <?php $no = 1; ?>
            <?php foreach($orders as $rs) : ?>
              <?php $ruid = genUid(); ?>
              <tr class="font-size-11" id="row-<?php echo $ruid; ?>">
                <?php if($doc->status == 'P') : ?>
                  <td class="text-center">
                    <label>
                      <input type="checkbox" class="ace chk-od" data-row="<?php echo $ruid; ?>" value="<?php echo $rs->order_code; ?>"/>
                      <span class="lbl"></span>
                    </label>
                  </td>
                <?php endif; ?>
                <td class="text-center o-no"><?php echo $no; ?></td>
                <td class=""><?php echo $rs->order_code; ?></td>
                <td class=""><?php echo $rs->reference; ?></td>
                <td class=""><?php echo $rs->channels_name; ?></td>
              </tr>
              <?php $no++; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script id="order-tab-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr class="font-size-11" id="row-{{ruid}}">
      <td class="text-center">
        <label>
          <input type="checkbox" class="ace chk-od" data-row="{{ruid}}" value="{{order_code}}"/>
          <span class="lbl"></span>
        </label>
      </td>
      <td class="text-center o-no"></td>
      <td class="">{{order_code}}</td>
      <td class="">{{reference}}</td>
      <td class="">{{channels}}</td>
    </tr>
  {{/each}}
</script>
