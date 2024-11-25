function printDownPaymentInvoice(code, option) {
	option = option === undefined ? '' : option;
	let width = 800;
	let height = 800;
	let center = (window.innerWidth - width)/2;
	let prop = "width="+width+", height="+height+", left="+center+", scrollbars=yes";
	let target = BASE_URL + 'orders/down_payment_invoice/print_invoice/'+code+'/'+option;
	window.open(target, '_blank', prop);
}
