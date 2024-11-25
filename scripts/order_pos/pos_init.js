window.addEventListener('load', async (e) => {
  setTimeout(async () => {
    let deviceId = await getPosDeviceId();

    if(deviceId) {
      goToPOS(deviceId);
    }
    else {
      $('#init-message').addClass('hide');
      $('#not-register').removeClass('hide');
    }

  }, 1000);
});
