window.addEventListener('load', () => {
	resizeDisplay();
})

window.addEventListener('resize', () => {
	resizeDisplay();
});

function resizeDisplay() {
	let height = $(window).height();
	let navHeight = 45;
	let searchHeight = $('#search-row').height() + navHeight;
	let pagination = $('#pagination').height();
	let pageContentHeight = height - pagination - 72;
	let billTableHeight = pageContentHeight - (searchHeight + 40 + pagination);
  billTableHeight = billTableHeight < 500 ? 500 : billTableHeight;

	$('.page-content').css('height', pageContentHeight + 'px');
  $('.page-content').css('padding-bottom', '0px');
	$('#bill-div').css('height', billTableHeight + 'px');
}
