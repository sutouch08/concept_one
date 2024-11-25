<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    <p class="pull-right top-p">
<?php if( $this->pm->can_add ) : ?>
      <button type="button" class="btn btn-xs btn-success top-btn" onclick="goAdd()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
<?php endif; ?>
    </p>
  </div>
</div>
<hr class="padding-5"/>

<form id="searchForm" method="post" >
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่/ชื่อเงื่อนไข</label>
    <input type="text" class="form-control input-sm text-center search-box" name="rule_code" id="rule_code" value="<?php echo $code; ?>" autofocus />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>รหัส/ชื่อ นโยบาย</label>
    <input type="text" class="form-control input-sm text-center search-box" name="policy" id="policy" value="<?php echo $policy; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>ส่วนลด</label>
    <input type="number" class="form-control input-sm text-center search-box" pattern="[0-9]" inputmode="numeric" name="rule_disc" id="rule_disc" value="<?php echo $discount; ?>">
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>สถานะเงื่อนไข</label>
    <select class="form-control input-sm" name="rule_status" onchange="getSearch()">
      <option value="all" <?php echo is_selected('all', $rule_status); ?>>ทั้งหมด</option>
      <option value="1" <?php echo is_selected('1', $rule_status); ?>>Active</option>
      <option value="0" <?php echo is_selected('0', $rule_status); ?>>Inactive</option>
    </select>
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>สถานะนโยบาย</label>
    <select class="form-control input-sm" name="policy_status" onchange="getSearch()">
      <option value="all" <?php echo is_selected('all', $policy_status); ?>>ทั้งหมด</option>
      <option value="1" <?php echo is_selected('1', $policy_status); ?>>Active</option>
      <option value="0" <?php echo is_selected('0', $policy_status); ?>>Inactive</option>
    </select>
  </div>


  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
    <label class="display-block not-show">search</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">ค้นหา</button>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
    <label class="display-block not-show">reset</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>
</div>
</form>

<hr class="padding-5"/>
<?php echo $this->pagination->create_links(); ?>

 <div class="row">
   <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
     <table class="table table-striped border-1" style="min-width:960px;">
       <thead>
         <tr>
           <th class="fix-width-40 text-center">ลำดับ</th>
           <th class="fix-width-120 text-center">เลขที่เงื่อนไข</th>
           <th class="min-width-250">เงื่อนไข</th>
           <th class="fix-width-120 text-center">เลขที่นโยบาย</th>
           <th class="fix-width-150 text-center">ส่วนลด</th>
           <th class="fix-width-80 text-center">สถานะ เงื่อนไข</th>
           <th class="fix-width-80 text-center">สถานะ นโยบาย</th>
           <th class="fix-width-120"></th>
         </tr>
       </thead>
       <tbody>
<?php if(!empty($rules)) : ?>
  <?php $no = $this->uri->segment($this->segment) + 1; ?>
  <?php foreach($rules as $rs) : ?>
    <?php
        $disc = array(
          'price' => $rs->item_price,
          'disc_1' => $rs->item_disc,
          'unit_1' => $rs->item_disc_unit,
          'disc_2' => $rs->item_disc_2,
          'unit_2' => $rs->item_disc_2_unit,
          'disc_3' => $rs->item_disc_3,
          'unit_3' => $rs->item_disc_3_unit
        );

      ?>
        <tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
          <td class="middle text-center no"><?php echo number($no); ?></td>
          <td class="middle text-center"><?php echo $rs->code; ?></td>
          <td class="middle"><?php echo $rs->name; ?></td>
          <td class="middle text-center"><?php echo $rs->policy_code; ?></td>
          <td class="middle text-center"><?php echo parse_discount_to_label($disc); ?></td>
          <td class="middle text-center"><?php echo is_active($rs->active); ?></td>
          <td class="middle text-center"><?php echo is_active($rs->policy_status); ?></td>
          <td class="middle text-right">
            <button type="button" class="btn btn-xs btn-info" onclick="viewDetail('<?php echo $rs->id; ?>')"><i class="fa fa-eye"></i></button>
      <?php if($this->pm->can_edit) : ?>
            <button type="button" class="btn btn-xs btn-warning" onclick="goEdit('<?php echo $rs->id; ?>')"><i class="fa fa-pencil"></i></button>
      <?php endif; ?>
      <?php if($this->pm->can_delete) : ?>
            <button type="button" class="btn btn-xs btn-danger" onclick="getDelete('<?php echo $rs->id; ?>', '<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
      <?php endif; ?>

          </td>
        </tr>
    <?php $no++; ?>
  <?php endforeach; ?>

<?php else : ?>
        <tr>
          <td colspan="8" class="text-center">
            <h4>ไม่พบรายการ</h4>
          </td>
        </tr>
<?php endif; ?>
       </tbody>
     </table>
   </div>
 </div>

<script src="<?php echo base_url(); ?>scripts/discount/rule/rule.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/rule_list.js"></script>
<?php $this->load->view('include/footer'); ?>
