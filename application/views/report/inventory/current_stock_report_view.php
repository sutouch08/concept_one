<?php $this->load->view('include/header'); ?>
<style>
.icon-box {
	display: inline-block;
	width:30%;
	height: 60px;
	font-size: 22px;
	padding-top:15px;
	padding-bottom: 15px;
	text-align: center;
	margin: 0px;
	color:#fff;
}

.sub-icon {
	display: inline-block;
	width:30%;
	height: 50px;
	font-size: 18px;
	padding-top:15px;
	padding-bottom: 15px;
	text-align: center;
	margin: 0px;
	color:#fff;
}

.info-box {
	display: inline-block;
	width:70%;
	height: 60px;
	font-size:22px;
	padding:15px;
	margin-left:-4px;
	text-align: right;
	color:#FFF;
}

.sub-info {
	display: inline-block;
	width:70%;
	height: 50px;
	font-size:18px;
	padding:15px;
	margin-left:-4px;
	text-align: right;
	color:#FFF;
}

.i-blue {  background-color: #4A89DC; }
.c-blue {  background-color: #5D9CEC; }
.i-green {  background-color:  #8CC152;}
.c-green {  background-color:  #A0D468;}
.i-yellow { background-color: #F6BB42;}
.c-yellow { background-color: #FFCE54;}
.i-orange { background-color: #E9573F;}
.c-orange { background-color: #FC6E51;}
.i-red { background-color: #DA4453;}
.c-red { background-color: #ED5565;}
</style>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-sm-6">
			<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
			</p>
		</div>
</div><!-- End Row -->
<hr>
<div class="row">
	<div class="col-sm-12" id="result">
		<div class="row">
			<div class="col-sm-3 padding-5">
				<div class="icon-box i-blue">Qty</div>
				<div class="info-box c-blue" id="all-qty"><?php echo number($allQty); ?></div>
			</div>
			<div class="col-sm-4 padding-5">
				<div class="icon-box i-green">Amount</div>
				<div class="info-box c-green" id="all-amount"><?php echo number($allAmount, 2); ?></div>
			</div>
			<div class="col-sm-2 col-2-harf padding-5">
				<div class="icon-box i-orange">SKU</div>
				<div class="info-box c-orange" id="all-sku"><?php echo number($allSku); ?></div>
			</div>
			<div class="col-sm-2 col-2-harf padding-5">
				<div class="icon-box i-red width-40">Model</div>
				<div class="info-box c-red width-60" id="all-model"><?php echo number($allModel); ?></div>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-sm-12 padding-5">
				<blockquote>
					<p class="lead" style="color:#CCC;">
						รายงานนี้แสดงสินค้าคงเหลือ ณ เวลาที่เรียกรายงาน โดยรายงานแสดงผลลัพธ์ตามนี้ <br/>
						- แสดงเฉพาะสินค้าที่มีสต็อกคงเหลือเท่านั้น<br/>
						- แสดงเฉพาะสต็อกที่มีในคลังของบริษัทเท่านั้น (ไม่รวมสินค้าฝากขายที่มีการเปิดใบกำกับภาษีไปแล้ว)<br/>
						- แสดงรายการสินค้า ตามกลุ่มสินค้า ที่กำหนดโดยฐานข้อมูลรายการสินค้า<br/>
					</p>
				</blockquote>
			</div>

		</div>
	</div>
</div>




<div class="modal fade" id="stockGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
			 </div>
			 <div class="modal-body" id="modalBody"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
			 </div>
		</div>
	</div>
</div>

<script>

function getReport()
{
	load_in();
	$.ajax({
		url:BASE_URL + 'report/inventory/current_stock/get_report',
		type:'GET',
		cache:false,
		success:function(rs){
			load_out();
			$('#result').html(rs);
		},
		error:function(rs){
			load_out();

			swal({
				title:rs.statusText,
				text: rs.responseText,
				type:'error',
				html:true
			});
		}
	});
}


function getData(code)
{
	load_in();
	$.ajax({
		url:BASE_URL + 'report/inventory/current_stock/get_stock_grid',
		type:'GET',
		cache:false,
		data:{
			'style_code' : code
		},
		success:function(rs){
			load_out();

			if(isJson(rs)) {
				let ds = $.parseJSON(rs);

				$("#modal").css("width", ds.width +"px");
				$("#modalTitle").html(ds.code);

				$("#modalBody").html(ds.table);
				$("#stockGrid").modal('show');
			}
			else {
				swal(rs);
			}		
		}
	});
}

</script>
<?php $this->load->view('include/footer'); ?>
