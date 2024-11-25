$(document).ready(function() {
	//---	reload ทุก 5 นาที
	setTimeout(function(){ goBack(); }, 300000);
});


window.addEventListener('load', () => {
	resizeDisplay();
})

window.addEventListener('resize', () => {
	resizeDisplay();
});

function resizeDisplay() {
	let height = $(window).height();
	let navHeight = 45;
	let searchHeight = $('#search-row').height() + navHeight + 25;
	let pagination = $('#pagination').height();
	let pageContentHeight = height - pagination - 75;
	let billTableHeight = pageContentHeight - (searchHeight + 65 + pagination);
	billTableHeight = billTableHeight < 500 ? 500 : billTableHeight;

	$('.page-content').css('height', pageContentHeight + 'px');
	$('#bill-div').css('height', billTableHeight + 'px');
}
