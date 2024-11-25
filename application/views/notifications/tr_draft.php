<li class="light-green" id="tr-result">
  <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
    TD
    <span class="badge badge-primary">0</span>
  </a>
</li>
<script id="tr-template" type="text/x-handlebarsTemplate">
<a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
  TD
  <span class="badge badge-primary">{{rows}}</span>
</a>

<ul class="dropdown-menu-left dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
  <li class="dropdown-header">
    <center>รอยืนยัน  {{rows}}  รายการ</center>
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
          <a href="javascript:void(0)" onclick="viewOrderClosedDetail('{{code}}')">
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
    <a href="javascript:void(0)" onclick="viewAllConsignRecieve()">
      ดูรายการทั้งหมด
      <i class="ace-icon fa fa-arrow-right"></i>
    </a>
  </li>
</ul>
</script>

<script>

$(document).ready(function(){
  get_receive_wt();
});

var r_wt = setInterval(function(){
  get_receive_wt();
}, refresh_rate);

function get_receive_wt(){
  $.ajax({
    url:BASE_URL + 'orders/consign_tr/get_un_received_list',
    type:'GET',
    cache:false,
    data:{
      'limit' : limit_rows
    },
    success:function(rs){
      if(isJson(rs)){
        let source = $('#tr-template').html();
        let data = $.parseJSON(rs);
        let output = $('#tr-result');
        render(source, data, output);
      }
    }
  })
}



function viewOrderClosedDetail(code){
  //--- properties for print
  var center    = ($(document).width() - 900)/2;
  var prop 			= "width=900, height=900. left="+center+", scrollbars=yes";
	var target = BASE_URL + 'inventory/invoice/view_detail/' + code + '?nomenu&approve_view';
	window.open(target, "_blank", prop);
}


function viewAllConsignRecieve(){
  $('#receive-form').submit();
}

</script>
