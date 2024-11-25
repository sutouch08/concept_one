<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-5">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <select class="form-control input-sm" id="payments-list">
      <option value="">เลื่อกการชำระเงิน</option>
      <?php echo select_pos_payment_method(); ?>
    </select>
  </div>
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 padding-5">
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addPayment()"><i class="fa fa-plus"></i> เพิ่มการชำระเงิน</button>
  </div>
  <div class="divider"></div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="width-5 text-center">#</th>
          <th class="width-20">Code</th>
          <th class="width-30">Payments</th>
          <th class="width-15">Role</th>
          <th class="width-15">วันที่เพิ่ม</th>
          <th class="text-center"></th>
        </tr>
      </thead>
      <tbody id="payment-table">
        <?php if(!empty($payments)) : ?>
          <?php $no = 1; ?>
          <?php foreach($payments as $pm) : ?>
            <tr id="payment-row-<?php echo $pm->id; ?>">
              <td class="middle text-center p-no"><?php echo $no; ?></td>
              <td class="middle"><?php echo $pm->payment_code; ?></td>
              <td class="middle"><?php echo $pm->payment_name; ?></td>
              <td class="middle text-center"><?php echo $pm->role_name; ?></td>
              <td class="middle"><?php echo thai_date($pm->date_add, FALSE, '/'); ?></td>
              <td class="middle text-center">
                <button type="button" class="btn btn-minier btn-danger" onclick="removePayment(<?php echo $pm->id; ?>, '<?php echo $pm->payment_name; ?>')">
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


  <script id="payment-template" type="text/x-handlebarsTemplate">
  	<tr id="payment-row-{{id}}">
  		<td class="middle text-center p-no"></td>
  		<td class="middle">{{code}}</td>
  		<td class="middle">{{name}}</td>
      <td class="middle">{{role_name}}</td>
  		<td class="middle">{{date_add}}</td>
  		<td class="middle text-center">
  			<button type="button" class="btn btn-minier btn-danger" onclick="removePayment({{id}}, '{{payment_name}}')">
  				<i class="fa fa-trash"></i>
  			</button>
  		</td>
  	</tr>
  </script>
