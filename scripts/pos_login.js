var deviceId = null;

window.addEventListener('load', async function() {
  let deviceId = await getPosDeviceId();
  
  if(deviceId != null) {
    $('#deviceId').val(deviceId);

    await getPosData(deviceId);
  }
});

async function getPosDeviceId() {
  let data = localStorage.getItem('IXPOSDATA');
	if(data !== null && data !== undefined) {
    pos = JSON.parse(data);
		return pos.deviceId;
  }
	else {
		swal({
			title:'Error!',
			text:'ไม่พบข้อมูลที่ Register ไว้',
			type:'error'
		});

		return false;
	}
}


async function getPosData(deviceId) {
  $.ajax({
    url:BASE_URL + 'pos/get_pos_data',
    type:'GET',
    cahe:false,
    data:{
      'deviceId' : deviceId
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          if(ds.data.users) {
            let source = $('#user-drop-down').html()
            let output = $('#uname')

            render(source, ds.data.users, output)
          }
          else {
            let data = {'nodata':'nodata'}
            let source = $('#no-user-drop-down').html()
            let output = $('#uname')

            render(source, data, output);
          }

          if(ds.data.pos) {
            let shopName = '#'+ds.data.pos.shop_name;
            $('#shop-name').text(shopName);
          }
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        });
      }
    }
  })
}


function showPwd() {
  var x = document.getElementById("pwd");
  var y = document.getElementById("pwd-btn");

  if(x.type === "password") {
    x.type = "text";
    y.classList.remove('fa-eye');
    y.classList.add('fa-eye-slash');
  }
  else {
    x.type = "password";
    y.classList.remove('fa-eye-slash');
    y.classList.add('fa-eye');
  }
}


function doLogin() {
	const uname = $('#uname').val();
	const pwd = $('#pwd').val();
	const ipwd = $('#ipwd').text();
	const remember = $('#remember').is(':checked') ? 1 : 0;

	if(uname.length == 0) {
		$('#uname').focus();
		return false;
	}

	if(pwd.length == 0) {
		$('#pwd').focus();
		return false;
	}

	if(pwd != ipwd) {
		return false;
	}

	$.ajax({
		url:BASE_URL + 'users/authentication/validate_credentials',
		type:'POST',
		cache:false,
		data:{
			'uname' : uname,
			'pwd' : ipwd,
			'remember' : remember
		},
		success:function(rs) {
			rs = $.trim(rs);

			if(rs === 'success') {
				window.location.href = BASE_URL + 'orders/order_pos/';
			}
			else {
				$('#login-error').text(rs);
			}
		}
	});
}



$('#pwd').keyup(function(e) {
	if(e.keyCode === 13) {
		doLogin();
	}
	else {
		$('#ipwd').text($(this).val());
	}
});

$('#btn-login').click(function() {
  $('#login-error').html('&nbsp;');

  let uname = $('#uname').val();
  let pwd = $('#pwd').val();

  if(uname.length == 0) {
    $('#login-error').text('กรุณาเลือกผู้ใช้งาน');
    return false;
  }

  if(pwd.length == 0) {
    $('#login-error').text('กรุณาใส่รหัสผ่าน');
    return false;
  }

  doLogin();
});


$('#uname').change(function() {
  $('#pwd').focus();
})
