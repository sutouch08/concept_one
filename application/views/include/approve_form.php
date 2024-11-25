<?php if($this->notibars == 1) : ?>
  <?php if($this->WT->can_approve) : ?>
  <form id="receive-form" method="post" target="_blank" action="<?php echo base_url(); ?>inventory/invoice">
    <input type="hidden" name="role" value="N">
    <input type="hidden" name="is_valid" value="0" >
  </form>
  <form id="consign_tr-form" method="post" target="_blank" action="<?php echo base_url(); ?>orders/consign_tr">
    <input type="hidden" name="isApprove" value="0">
  </form>
  <?php endif; ?>

  <?php if($this->WS->can_approve) : ?>
  <form id="sponsor-form" method="post" target="_blank" action="<?php echo base_url(); ?>orders/sponsor">
    <input type="hidden" name="isApprove" value="0">
  </form>
  <?php endif; ?>

  <?php if($this->WU->can_approve) : ?>
  <form id="support-form" method="post" target="_blank" action="<?php echo base_url(); ?>inventory/support">
    <input type="hidden" name="isApprove" value="0">
  </form>
  <?php endif; ?>

  <?php if($this->WQ->can_approve) : ?>
  <form id="wq-form" method="post" target="_blank" action="<?php echo base_url(); ?>inventory/transform">
    <input type="hidden" name="isApprove" value="0">
  </form>
  <?php endif; ?>

  <?php if($this->WV->can_approve) : ?>
  <form id="wv-form" method="post" target="_blank" action="<?php echo base_url(); ?>inventory/transform_stock">
    <input type="hidden" name="isApprove" value="0">
  </form>
  <?php endif; ?>

  <?php if($this->WL->can_approve) : ?>
  <form id="wl-form" method="post" target="_blank" action="<?php echo base_url(); ?>inventory/lend">
    <input type="hidden" name="isApprove" value="0">
  </form>
  <?php endif; ?>

  <?php if($this->RR->can_approve) : ?>
  <form id="rr-form" method="post" target="_blank" action="<?php echo base_url(); ?>inventory/receive_po_request">
    <input type="hidden" name="isApprove" value="0">
  </form>
  <?php endif; ?>
<?php endif; ?>
