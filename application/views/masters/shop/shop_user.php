<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
  <div class="col-lg-2 col-lg-offset-4 col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-3 col-xs-6 padding-5">
    <input type="text" class="form-control input-sm text-center" id="user-box" placeholder="ค้นหาชื่อผู้ใช้งาน" value="" />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="add_user()"><i class="fa fa-plus"></i> เพิ่มผู้ใช้งาน</button>
  </div>
  <div class="divider"></div>
  <div class="col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-200">User Name</th>
          <th class="fix-width-250">Display Name</th>
          <th class="fix-width-150">วันที่เพิ่ม</th>
          <th class="min-width-100"></th>
        </tr>
      </thead>
      <tbody id="user-table">
        <?php if(!empty($users)) : ?>
          <?php $no = 1; ?>
          <?php foreach($users as $user) : ?>
            <tr id="row-<?php echo $user->id; ?>">
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle"><?php echo $user->uname; ?></td>
              <td class="middle"><?php echo $user->name; ?></td>
              <td class="middle"><?php echo thai_date($user->date_add, FALSE, '/'); ?></td>
              <td class="middle">
                <button type="button" class="btn btn-minier btn-danger" onclick="removeUser(<?php echo $user->id; ?>, '<?php echo $user->uname; ?>')">
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

  <script id="user-template" type="text/x-handlebarsTemplate">
  	<tr id="row-{{id}}">
  		<td class="middle text-center no"></td>
  		<td class="middle">{{uname}}</td>
  		<td class="middle">{{name}}</td>
  		<td class="middle">{{date_add}}</td>
  		<td class="middle text-center">
  			<button type="button" class="btn btn-minier btn-danger" onclick="removeUser({{id}}, '{{uname}}')">
  				<i class="fa fa-trash"></i>
  			</button>
  		</td>
  	</tr>
  </script>
