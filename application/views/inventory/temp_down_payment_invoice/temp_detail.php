<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title"><?php echo $doc->U_ECOMNO; ?></h4>
  </div>
</div>
<hr/>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-bordered border-1">
      <tbody>
        <tr><td colspan="2" class="text-center" style="background-color:#eee;">เอกสาร</td></tr>
        <tr><td class="fix-width-150">เลขที่เอกสาร</td><td class="min-width-100"><?php echo $doc->U_ECOMNO; ?></td></tr>
        <tr><td>วันที่</td><td><?php echo thai_date($doc->DocDate, FALSE); ?></td></tr>
        <tr><td>ลูกค้า</td><td><?php echo $doc->CardCode .' : '. $doc->CardName; ?></td></tr>
        <tr><td>Tax ID</td><td><?php echo $doc->LicTradNum; ?></td></tr>
        <tr><td>ที่อยู่</td><td><?php echo $doc->Address; ?></td></tr>
        <tr><td>อ้างอิง</td><td><?php echo $doc->NumAtCard; ?></td></tr>
        <tr><td>มูลค่าก่อนภาษี</td><td><?php echo number($doc->DocTotal - $doc->VatSum, 2); ?></td></tr>
        <tr><td>ภาษี</td><td><?php echo number($doc->VatSum, 2); ?></td></tr>
        <tr><td>มูลค่า</td><td><?php echo number($doc->DocTotal, 2); ?></td></tr>
        <tr><td>Temp Date</td><td><?php echo thai_date($doc->F_E_CommerceDate, TRUE); ?></td></tr>
        <tr><td>SAP Date</td><td><?php echo empty($doc->F_SapDate) ? "-" : thai_date($doc->F_SapDate, TRUE); ?></td></tr>
        <tr><td>Status</td><td><?php echo $doc->F_Sap == 'N' ? 'Failed' : ($doc->F_Sap == 'Y' ? 'Success' : 'Pending'); ?></td></tr>
        <tr><td>Message</td><td><?php echo $doc->Message; ?></td></tr>
        <tr><td colspan="2" class="text-center" style="background-color:#eee;">รายละเอียด</td></tr>
        <?php if(!empty($details)) : ?>
          <?php foreach($details as $rs) : ?>
            <tr><td><?php echo $rs->ItemCode; ?></td><td><?php echo $rs->Dscription." @".number($rs->PriceAfVAT, 2); ?></td></tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $this->load->view('include/footer'); ?>
