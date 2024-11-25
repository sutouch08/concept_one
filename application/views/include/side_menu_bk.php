<?php
$menu_sub_group_code = isset($this->menu_sub_group_code) ? $this->menu_sub_group_code : NULL;
$menu = $this->menu_code;
$menu_group = $this->menu_group_code;
?>
<!--   Side menu Start --->
<ul class="nav nav-list">
	<li class="<?php echo isActiveOpenMenu($menu_group, 'IC'); ?>">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-home"></i>
			<span class="menu-text">ระบบคลังสินค้า</span>
			<b class="arrow fa fa-angle-down"></b>
		</a>
		<ul class="submenu">
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'RECEIVE'); ?>">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon fa fa-caret-right"></i> รับสินค้า <b class="arrow fa fa-angle-down"></b>
				</a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'ICPURC',  'inventory/receive_po', 'รับสินค้าจากการสั่งซื้อ'); ?>
					<?php echo side_menu($menu, 'ICTRRC',  'inventory/receive_transform', 'รับสินค้าจากการแปรสภาพ'); ?>
				</ul>
			</li>
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'RETURN'); ?>">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon fa fa-caret-right"></i> คืนสินค้า <b class="arrow fa fa-angle-down"></b>
				</a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'ICRTOR',  'inventory/return_order', 'รับคืนสินค้า(ลดหนี้)'); ?>
					<?php echo side_menu($menu, 'ICRTLD',  'inventory/return_lend', 'รับคืนสินค้าจาการยืม'); ?>
				</ul>
			</li>
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'REQUEST'); ?>">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon fa fa-caret-right"></i> เบิก/ยืม สินค้า <b class="arrow fa fa-angle-down"></b>
				</a>
				<ul class="submenu">
				<?php echo side_menu($menu, 'ICTRFM',  'inventory/transform', 'เบิกแปรสภาพ(ขาย)'); ?>
				<?php echo side_menu($menu, 'ICTRFS',  'inventory/transform_stock', 'เบิกแปรสภาพ(สต็อก)'); ?>
				<?php echo side_menu($menu, 'ICSUPP',  'inventory/support', 'เบิกสินค้าอภินันท์'); ?>
				<?php echo side_menu($menu, 'ICLEND',  'inventory/lend', 'เบิกยืมสินค้า'); ?>
				</ul>
			</li>
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'PICKPACK'); ?>">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon fa fa-caret-right"></i> จัด/ตรวจ สินค้า
					<b class="arrow fa fa-angle-down"></b>
				</a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'ICODPR',  'inventory/prepare', 'จัดสินค้า'); ?>
					<?php echo side_menu($menu, 'ICODQC',  'inventory/qc', 'ตรวจสินค้า'); ?>
					<?php echo side_menu($menu, 'ICODDO',  'inventory/delivery_order', 'ออเดอร์รอเปิดบิล'); ?>
					<?php echo side_menu($menu, 'ICODIV',  'inventory/invoice', 'ออเดอร์เปิดบิลแล้ว'); ?>
				</ul>
			</li>

			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'TRANSFER'); ?>">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon fa fa-caret-right"></i> โอน/ย้าย สินค้า
					<b class="arrow fa fa-angle-down"></b>
				</a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'ICTRWH',  'inventory/transfer', 'โอน สินค้า'); ?>
					<?php echo side_menu($menu, 'ICTRZN',  'inventory/move', 'ย้าย สินค้า'); ?>
				</ul>
			</li>

			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'CHECK'); ?>">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon fa fa-caret-right"></i> ตรวจสอบ
					<b class="arrow fa fa-angle-down"></b>
				</a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'ICCKBF',  'inventory/buffer', 'ตรวจสอบ BUFFER'); ?>
					<?php echo side_menu($menu, 'ICCKCN',  'inventory/cancle', 'ตรวจสอบ CANCLE'); ?>
					<?php echo side_menu($menu, 'ICCKMV',  'inventory/movement', 'ตรวจสอบ MOVEMENT'); ?>
				</ul>
			</li>

			<?php echo side_menu($menu, 'ICCSRC',  'inventory/consign_check', 'กระทบยอดสินค้า'); ?>
		</ul>
	</li>


	<li class="<?php echo isActiveOpenMenu($menu_group, 'SO'); ?>">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-shopping-basket"></i>
			<span class="menu-text">ระบบขาย</span>
			<b class="arrow fa fa-angle-down"></b>
		</a>
		<ul class="submenu">
			<?php echo side_menu($menu, 'SOODSO',  'orders/orders', 'ออเดอร์'); ?>
			<?php echo side_menu($menu, 'SOODSP',  'orders/sponsor', 'สปอนเซอร์'); ?>
			<?php echo side_menu($menu, 'SOCCSO',  'orders/consign_so', 'ฝากขาย(ใบกำกับ)'); ?>
			<?php echo side_menu($menu, 'SOCCTR',  'orders/consign_tr', 'ฝากขาย(โอนคลัง)'); ?>
			<?php echo side_menu($menu, 'view_stock',  'view_stock', 'เช็คสต็อก'); ?>
		</ul>
	</li>

	<li class="<?php echo isActiveOpenMenu($menu_group, 'AC'); ?>">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-calculator"></i>
			<span class="menu-text">ระบบบัญชี</span>
			<b class="arrow fa fa-angle-down"></b>
		</a>
		<ul class="submenu">
			<?php echo side_menu($menu, 'ACPMCF',  'orders/order_payment', 'ตรวจสอบยอดชำระเงิน'); ?>
			<?php echo side_menu($menu, 'ACCSOD',  'account/consign_order', 'ตัดยอดฝากขาย(Shop)'); ?>
			<?php echo side_menu($menu, 'ACCMOD',  'account/consignment_order', 'ตัดยอดฝากขาย(ห้าง)'); ?>
		</ul>
	</li>


	<li class="<?php echo isActiveOpenMenu($menu_group, 'SC'); ?>">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-cogs"></i>
			<span class="menu-text">การกำหนดค่า</span>
			<b class="arrow fa fa-angle-down"></b>
		</a>
		<ul class="submenu">
			<?php echo side_menu($menu, 'SCCONF', 'setting/configs', 'การกำหนดค่า');  ?>
			<?php echo side_menu($menu, 'SCUSER', 'users/users', 'เพิ่ม/แก้ไข ชื่อผู้ใช้งาน');  ?>
			<?php echo side_menu($menu, 'SCPORF', 'users/profiles', 'เพิ่ม/แก้ไข โปรไฟล์'); ?>
			<?php echo side_menu($menu, 'SCPERM', 'users/permission', 'กำหนดสิทธิ์'); ?>
			<?php echo side_menu($menu, 'SCPOLI', 'discount/discount_policy', 'นโยบายส่วนลด'); ?>
			<?php echo side_menu($menu, 'SCRULE', 'discount/discount_rule', 'เงื่อนไขส่วนลด'); ?>
			<?php //echo side_menu($menu, 'SCBGSP', 'budget/sponsor_budget', 'งบประมาณสปอนเซอร์'); ?>
			<?php //echo side_menu($menu, 'SCBGSU', 'budget/support_budget', 'งบประมาณอภินันท์'); ?>
		</ul>
	</li>

	<li class="<?php echo isActiveOpenMenu($menu_group, 'DB'); ?>">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-database"></i>
			<span class="menu-text">ระบบฐานข้อมูล</span>
			<b class="arrow fa fa-angle-down"></b>
		</a>
		<ul class="submenu">
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'PRODUCT'); ?>">
				<a href="#" class="dropdown-toggle"><i class="menu-icon fa fa-caret-right"></i> ฐานข้อมูลสินค้า <b class="arrow fa fa-angle-down"></b></a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'DBPROD', 'masters/products','เพิ่ม/แก้ไข สินค้า'); ?>
					<?php echo side_menu($menu, 'DBITEM', 'masters/items','เพิ่ม/แก้ไข รายการสินค้า'); ?>
					<?php //echo side_menu($menu, 'DBPDST', 'masters/product_style','เพิ่ม/แก้ไข รุ่นสินค้า'); ?>
					<?php echo side_menu($menu, 'DBPDGP', 'masters/product_group','เพิ่ม/แก้ไข กลุ่มสินค้า'); ?>
					<?php echo side_menu($menu, 'DBPDSG', 'masters/product_sub_group','เพิ่ม/แก้ไข กลุ่มย่อยสินค้า'); ?>
					<?php echo side_menu($menu, 'DBPDCR', 'masters/product_category','เพิ่ม/แก้ไข หมวดหมู่สินค้า'); ?>
					<?php echo side_menu($menu, 'DBPDKN', 'masters/product_kind','เพิ่ม/แก้ไข ประเภทสินค้า'); ?>
					<?php echo side_menu($menu, 'DBPDTY', 'masters/product_type','เพิ่ม/แก้ไข ชนิดสินค้า'); ?>
					<?php echo side_menu($menu, 'DBPTAB', 'masters/product_tab','เพิ่ม/แก้ไข แถบแสดงสินค้า'); ?>
					<?php echo side_menu($menu, 'DBPDCL', 'masters/product_color','เพิ่ม/แก้ไข สี'); ?>
					<?php echo side_menu($menu, 'DBPDSI', 'masters/product_size','เพิ่ม/แก้ไข ไซส์'); ?>
					<?php echo side_menu($menu, 'DBPDBR', 'masters/product_brand','เพิ่ม/แก้ไข ยี่ห้อสินค้า'); ?>
					<?php echo side_menu($menu, 'PDSYNC', 'sync_items','Sync ข้อมูลสินค้า'); ?>
				</ul>
			</li>
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'CUSTOMER'); ?>">
				<a href="#" class="dropdown-toggle"><i class="menu-icon fa fa-caret-right"></i> ฐานข้อมูลลูกค้า <b class="arrow fa fa-angle-down"></b></a>
				<b class="arrow"></b>
				<ul class="submenu">
					<?php echo side_menu($menu, 'DBCUST', 'masters/customers','เพิ่ม/แก้ไข รายชื่อลูกค้า'); ?>
					<?php echo side_menu($menu, 'DBCARE', 'masters/customer_area','เพิ่ม/แก้ไข เขตการขาย'); ?>
					<?php echo side_menu($menu, 'DBCLAS', 'masters/customer_class','เพิ่ม/แก้ไข เกรดลูกค้า'); ?>
					<?php echo side_menu($menu, 'DBCGRP', 'masters/customer_group','เพิ่ม/แก้ไข กลุ่มลูกค้า'); ?>
					<?php echo side_menu($menu, 'DBCKIN', 'masters/customer_kind','เพิ่ม/แก้ไข ประเภทลูกค้า'); ?>
					<?php echo side_menu($menu, 'DBCTYP', 'masters/customer_type','เพิ่ม/แก้ไข ชนิดลูกค้า'); ?>
				</ul>
			</li>

			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'WAREHOUSE'); ?>">
				<a href="#" class="dropdown-toggle"><i class="menu-icon fa fa-caret-right"></i> ฐานข้อมูลคลังสินค้า <b class="arrow fa fa-angle-down"></b></a>
				<b class="arrow"></b>
				<ul class="submenu">
					<?php echo side_menu($menu, 'DBWRHS', 'masters/warehouse','เพิ่ม/แก้ไข คลังสินค้า'); ?>
					<?php echo side_menu($menu, 'DBZONE', 'masters/zone','เพิ่ม/แก้ไข โซน'); ?>
				</ul>
			</li>

			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'TRANSPORT'); ?>">
				<a href="#" class="dropdown-toggle"><i class="menu-icon fa fa-caret-right"></i> ฐานข้อมูลขนส่ง <b class="arrow fa fa-angle-down"></b></a>
				<b class="arrow"></b>
				<ul class="submenu">
					<?php echo side_menu($menu, 'DBSEND', 'masters/sender','เพิ่ม/แก้ไข ขนส่ง'); ?>
					<?php echo side_menu($menu, 'DBTRSP', 'masters/transport','เชื่อมโยงขนส่ง'); ?>
				</ul>
			</li>

			<?php echo side_menu($menu, 'DBCHAN', 'masters/channels','เพิ่ม/แก้ไข ช่องทางขาย'); ?>
			<?php echo side_menu($menu, 'DBPAYM', 'masters/payment_methods','เพิ่ม/แก้ไข การชำระเงิน'); ?>
			<?php echo side_menu($menu, 'DBSALE', 'masters/saleman','ข้อมูลพนักงานขาย'); ?>
		</ul>
	</li>

	<li class="<?php echo isActiveOpenMenu($menu_group, 'RE'); ?>">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-bar-chart"></i>
			<span class="menu-text">รายงาน</span>
			<b class="arrow fa fa-angle-down"></b>
		</a>
		<ul class="submenu">
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'REINVT'); ?>">
				<a href="#" class="dropdown-toggle"><i class="menu-icon fa fa-caret-right"></i> รายงานระบบคลัง <b class="arrow fa fa-angle-down"></b></a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'REICST', 'report/inventory/stock_balance','รายงานสินค้าคงเหลือ'); ?>
				</ul>
			</li>
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'RESALE'); ?>">
				<a href="#" class="dropdown-toggle"><i class="menu-icon fa fa-caret-right"></i> รายงานระบบขาย <b class="arrow fa fa-angle-down"></b></a>
				<b class="arrow"></b>
				<ul class="submenu">
					<?php echo side_menu($menu, 'RESOCU', 'report/sales/sale_order_by_customer','รายงานยอดขาย แยกตามลูกค้า'); ?>
				</ul>
			</li>
		</ul>
	</li>
</ul><!-- /.nav-list -->
