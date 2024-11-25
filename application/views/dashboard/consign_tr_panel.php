<div class="row">
  <div class="col-sm-12">
    <table class="table border-1" style="height:500px;">
      <thead>
        <tr>
          <th class="middle" colspan="4">
            ฝากขาย(Shop) รออนุมัติ
            <button type="button" class="btn btn-link pull-right" onclick="get_wt()"><i class="fa fa-refresh green"></i></button>
          </th>
        </tr>
        <tr>
          <th class="width-15 text-center">วันที่</th>
          <th class="width-20">เลขที่</th>
          <th class="width-50">ลูกค้า</th>
          <th class="width-10"></th>
        </tr>
      </thead>
      <tbody id="consign_tr_panel">
        <tr>
          <td colspan="4" class="text-center">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw"></i>
            <span class="sr-only">Loading...</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script id="wt-template" type="text/x-handlebarsTemplate">
  {{#if this.data}}
    {{#each this.data}}
      <tr id="{{code}}">
        <td class="middle text-center">{{date_add}}</td>
        <td class="middle">{{code}}</td>
        <td class="middle hide-text">
          <input type="text" class="print-row" value="{{customer}}" style="" />
        </td>
        <td class="middle text-right">
          <button class="btn btn-minier btn-info" onclick="viewConsignTRDetail('{{code}}')">
          <i class="fa fa-eye"></i> View
          </button>
        </td>
      </tr>
    {{/each}}
    <tr>
    <td colspan="2" class="middle blue">Showing {{result_rows}} of {{rows}} rows</td>
    <td colspan="2" class="middle blue"><a href="#" onclick="viewAllConsignTR()">View all</a></td>
    </tr>
    {{else}}
    <tr>
      <td colspan="4" class="middle text-center">No data</td>
    </tr>
    {{/if}}

</script>

<script>

$(document).ready(function(){
  setTimeout(function(){
    get_wt();
  }, 1000);
});

var wt = setInterval(function(){
  get_wt();
}, <?php echo $refresh_rate; ?>);

function get_wt(){
  let html = '<tr><td colspan="4" class="text-center"><i class="fa fa-refresh fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span></td></tr>';
  $('#consign_tr_panel').html(html);
  setTimeout(function(){
    $.ajax({
      url:BASE_URL + 'orders/consign_tr/get_un_approve_list',
      type:'GET',
      cache:false,
      data:{
        'limit' : <?php echo $limit_rows; ?>
      },
      success:function(rs){
        if(isJson(rs)){
          let source = $('#wt-template').html();
          let data = $.parseJSON(rs);
          let output = $('#consign_tr_panel');
          render(source, data, output);
        }
      }
    })
  }, 1000)
}


function viewConsignTRDetail(code){
  //--- properties for print
  var center    = ($(document).width() - 900)/2;
  var prop 			= "width=900, height=900. left="+center+", scrollbars=yes";
	var target = BASE_URL + 'orders/consign_tr/edit_order/' + code + '/approve?nomenu';
	window.open(target, "_blank", prop);
}


function viewAllConsignTR(){
  window.open(BASE_URL + 'orders/consign_tr', "_blank");
}

</script>
