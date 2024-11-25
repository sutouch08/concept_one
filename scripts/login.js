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
				window.location.href = BASE_URL;
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
  doLogin();
});


$('#uname').change(function() {
  $('#pwd').focus();
})
