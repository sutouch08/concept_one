<li class="light-purple" id="wq-result">
  <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
    WQ
    <span class="badge badge-primary">0</span>
  </a>
</li>



<script id="wq-template" type="text/x-handlebarsTemplate">
<a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
  WQ
  <span class="badge badge-primary">{{rows}}</span>
</a>

<ul class="dropdown-menu-left dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
  <li class="dropdown-header">
    <center>รออนุมัติ  {{rows}}  รายการ</center>
  </li>

  <li class="dropdown-content ace-scroll" style="position: relative;">
    <div class="scroll-track" style="display: none;">
    <div class="scroll-bar"></div>
    </div>
    <div class="scroll-content" style="">
    <ul class="dropdown-menu dropdown-navbar">
    {{#if this.data}}
      {{#each this.data}}
        <li>
          <a href="javascript:void(0)" onclick="viewTransformDetail('{{code}}')">
            <div class="clearfix">
            <b class="blue">{{code}}</b> &nbsp; {{customer}}
            </div>
          </a>
        </li>
      {{/each}}
    {{else}}
      <li>
        <a href="javascript:void(0)" class="clearfix">
          <center><b class="blue">No Data</b></center>
        </a>
      </li>
    {{/if}}
      </ul>
    </div>
  </li>

  <li class="dropdown-footer">
    <a href="javascript:void(0)" onclick="viewAllTransform()">
      ดูรายการทั้งหมด
      <i class="ace-icon fa fa-arrow-right"></i>
    </a>
  </li>
</ul>
</script>

<script>

$(document).ready(function(){
  get_wq();
});

var ws = setInterval(function(){
  get_wq();
}, refresh_rate);

function get_wq(){
  $.ajax({
    url:BASE_URL + 'orders/orders/get_un_approve_list',
    type:'GET',
    cache:false,
    data:{
      'limit' : limit_rows,
      'role' : 'T'
    },
    success:function(rs){
      if(isJson(rs)){
        let source = $('#wq-template').html();
        let data = $.parseJSON(rs);
        let output = $('#wq-result');
        render(source, data, output);
      }
    }
  })
}



function viewTransformDetail(code){
  //--- properties for print
  var center    = ($(document).width() - 1000)/2;
  var prop 			= "width=1000, height=900. left="+center+", scrollbars=yes";
	var target = BASE_URL + 'inventory/transform/edit_order/' + code + '/approve?nomenu';
	window.open(target, "_blank", prop);
}

function viewAllTransform(){
  $('#wq-form').submit();
}
</script>
