<form id="systemForm">
  <?php
  $open     = $CLOSE_SYSTEM == 0 ? 'btn-success' : '';
  $close    = $CLOSE_SYSTEM == 1 ? 'btn-danger' : '';
  $freze    = $CLOSE_SYSTEM == 2 ? 'btn-warning' : '';
  $manual_code_yes = $MANUAL_DOC_CODE == 1 ? 'btn-success' : '';
  $manual_code_no = $MANUAL_DOC_CODE == 0 ? 'btn-danger' : '';
  $strongOn = $USE_STRONG_PWD == 1 ? 'btn-primary' : '';
  $strongOff = $USE_STRONG_PWD == 0 ? 'btn-primary' : '';

  ?>
  <div class="row">
    <?php if( $cando === TRUE ): //---- ถ้ามีสิทธิ์ปิดระบบ ---//	?>
      <div class="col-sm-3"><span class="form-control left-label">ปิดระบบ</span></div>
      <div class="col-sm-9">
        <div class="btn-group input-xlarge">
          <button type="button" class="btn btn-sm <?php echo $open; ?>" style="width:33%;" id="btn-open" onClick="openSystem()">เปิด</button>
          <button type="button" class="btn btn-sm <?php echo $close; ?>" style="width:33%;" id="btn-close" onClick="closeSystem()">ปิด</button>
          <button type="button" class="btn btn-sm <?php echo $freze; ?>" style="width:34%" id="btn-freze" onclick="frezeSystem()">ดูอย่างเดียว</button>
        </div>
        <span class="help-block">กรณีปิดระบบจะไม่สามารถเข้าใช้งานระบบได้ในทุกส่วน โปรดใช้ความระมัดระวังในการกำหนดค่านี้</span>
        <input type="hidden" name="CLOSE_SYSTEM" id="closed" value="<?php echo $CLOSE_SYSTEM; ?>" />
      </div>
      <div class="divider-hidden"></div>

    <?php endif; ?>

    <div class="col-sm-3"><span class="form-control left-label">ป้อนเลขที่เอกสารเอง</span></div>
    <div class="col-sm-9">
      <div class="btn-group input-medium">
        <button type="button" class="btn btn-sm <?php echo $manual_code_yes; ?>" style="width:50%;" id="btn-manual-yes" onClick="toggleManualCode(1)">เปิด</button>
        <button type="button" class="btn btn-sm <?php echo $manual_code_no; ?>" style="width:50%;" id="btn-manual-no" onClick="toggleManualCode(0)">ปิด</button>
      </div>
      <span class="help-block">เปิดการป้อนเลขที่เอกสารด้วยมือ หากปิดระบบจะรับเลขที่เอกสารให้อัตโนมัติ</span>
      <input type="hidden" name="MANUAL_DOC_CODE" id="manual-doc-code" value="<?php echo $MANUAL_DOC_CODE; ?>" />
    </div>
    <div class="divider-hidden"></div>


    <div class="col-sm-3"><span class="form-control left-label">วันที่ในการบันทึกขายตัดสต็อก</span></div>
    <div class="col-sm-9">
      <div class="btn-group input-medium">
        <select class="form-control input-sm input-medium" name="ORDER_SOLD_DATE">
          <option value="B" <?php echo is_selected("B", $ORDER_SOLD_DATE); ?>>วันที่เปิดบิล</option>
          <option value="D" <?php echo is_selected("D", $ORDER_SOLD_DATE); ?>>วันที่เอกสาร</option>
        </select>
      </div>
      <span class="help-block">กำหนดประเภทวันที่ที่ใช้ในการบันทึกขายและตัดสต็อกในระบบ SAP</span>
    </div>
    <div class="divider-hidden"></div>

    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">Strong Password</span></div>
    <div class="col-lg-5 col-md-9 col-sm-9 col-xs-12">
      <div class="btn-group input-medium">
        <button type="button" class="btn btn-sm <?php echo $strongOn; ?>" style="width:50%;" id="btn-strong-on" onClick="toggleStrongPWD(1)">เปิด</button>
        <button type="button" class="btn btn-sm <?php echo $strongOff; ?>" style="width:50%;" id="btn-strong-off" onClick="toggleStrongPWD(0)">ปิด</button>
      </div>
      <span class="help-block">เมื่อเปิดใช้งาน การกำหนดรหัสผ่านจะต้องประกอบด้วย ตัวอักษรภาษาอังกฤษ พิมพ์ใหญ่ พิมพ์เล็ก ตัวเลข และสัญลักษณ์พิเศษ อย่างน้อย อย่างล่ะ 1 ตัว และต้องมีความยาว 8 ตัวอักษรขึ้นไป</span>
      <input type="hidden" name="USE_STRONG_PWD" id="use-strong-pwd" value="<?php echo $USE_STRONG_PWD; ?>" />
    </div>
    <div class="divider-hidden"></div>


    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">Password Age</span></div>
    <div class="col-lg-7 col-md-9 col-sm-9 col-xs-12">
      <input type="number" class="form-control input-sm input-mini text-center" name="USER_PASSWORD_AGE" id="pwd-age" value="<?php echo $USER_PASSWORD_AGE; ?>" />
      <span class="help-block">กำหนดอายุของรหัสผ่าน(วัน) User จำเป็นต้องเปลี่ยนรหัสผ่านหากรหัสผ่านหมดอายุ</span>
    </div>
    <div class="divider-hidden"></div>

    <div class="col-sm-9 col-sm-offset-3">
      <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
        <button type="button" class="btn btn-sm btn-success" onClick="updateConfig('systemForm')"><i class="fa fa-save"></i> บันทึก</button>
      <?php endif; ?>
    </div>
    <div class="divider-hidden"></div>

  </div><!--/row-->
</form>
