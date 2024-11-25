<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
</div>
<hr>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-bordered border-1">
      <tbody>
        <?php if(!empty($doc)) : ?>
          <tr><td class="fix-width-100">เลขที่</td><td class="min-width-200"><?php echo $doc->U_ECOMNO; ?></td></tr>

          <tr><td class="">วันที่</td><td><?php echo thai_date($doc->DocDate, FALSE); ?></td></tr>
          <tr><td class="">ลูกค้า</td><td><?php echo $doc->CardCode. ' : '.$doc->CardName; ?></td></tr>
          <tr><td class="">เงินสด</td><td><?php echo empty($doc->CashAcct) ? "-" : "[{$doc->CashAcct}] = " . number($doc->CashSum, 2) ; ?></td></tr>
          <tr><td class="">เงินโอน</td><td><?php echo empty($doc->TrsfrAcct) ? "-" : "[{$doc->TrsfrAcct}] = " . number($doc->TrsfrSum, 2) ; ?></td></tr>
          <tr><td class="">บัตรเครดิต</td><td><?php echo empty($card) ? "-" : "[{$card->CreditAcct}] = " . number($card->CreditSum, 2) ; ?></td></tr>
          <tr><td class="">เช็ค</td><td><?php echo empty($doc->CheckAcct) ? "-" : "[{$doc->CheckAcct}] = ". number($doc->CheckSum, 2) ; ?></td></tr>
          <tr><td class="">JrnlMemo</td><td><?php echo $doc->JrnlMemo; ?></td></tr>
          <tr><td class="">Temp Date</td><td><?php echo thai_date($doc->F_E_CommerceDate, TRUE); ?></td></tr>
          <tr><td class="">SAP Date</td><td><?php echo empty($doc->F_SapDate) ? NULL : thai_date($doc->F_SapDate, TRUE); ?></td></tr>
          <tr><td class="">Status</td><td><?php echo ($doc->F_Sap == 'Y' ? 'Success' : ($doc->F_Sap == 'N' ? 'Failed' : 'Pending')); ?></td></tr>
          <tr><td class="">Message</td><td><?php echo $doc->Message; ?></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $this->load->view('include/footer'); ?>
