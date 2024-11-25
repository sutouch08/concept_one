<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="min-width:300px; min-height:400px; max-width:95vw; max-height:95vh;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
        <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
			 </div>
			 <div class="modal-body">
         <div class="row">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5"
           id="modalBody"
           style="position:relative; min-width:250px; min-height:400px; max-width:100%; max-height:60vh; overflow:auto;">

           </div>
         </div>
       </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addToOrder()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>
