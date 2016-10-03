function Cn_Cmi_IsJsonString(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

function Cn_Cmi_showError(str) {
	if (Cn_Cmi_IsJsonString(str)) {
		var obj = JSON.parse(str);
		if (obj.refunds != undefined) {
			alert('Order updated successfully \n\n');
			document.forms["refresh_page"].submit();
		} else {
			alert(obj.message);
			document.getElementById("loading-mask").style.display = 'none';
			//document.forms["refresh_page"].submit();
		}
	} else {
		if (str.message) {
			alert(str.message);
		} else {
			alert(str);
		}
		document.getElementById("loading-mask").style.display = 'none';
		//document.forms["refresh_page"].submit();
	}
}

function Cn_Cmi_showSuccess(str) {
	if (Cn_Cmi_IsJsonString(str)) {
		var obj = JSON.parse(str);
		alert(obj.message);
		document.forms["refresh_page"].submit();
	} else {
		document.forms["refresh_page"].submit();
	}
}

function Cn_Cmi_openCronProd(cron) {
	document.getElementById(cron).style.display = 'block';
}

function Cn_Cmi_toggleAuto(auto) {
	var elementsVal = document.getElementsByClassName("cnc_marketplace_select_box");
	if (auto == 1) {
		document.getElementById("order_automatic").style.display = 'block';
		for (var elemIndex = 0; elemIndex < elementsVal.length; ++elemIndex) {
			elementsVal[elemIndex].setAttribute("class", "cnc_marketplace_select_box classValidation");
		}
	} else {
		document.getElementById("order_automatic").style.display = 'none';
		for (var elemIndex = 0; elemIndex < elementsVal.length; ++elemIndex) {
			elementsVal[elemIndex].setAttribute("class", "cnc_marketplace_select_box");
		}
	}
}

function Cn_Cmi_orderActionHandler(event) {
	var oredrAction = event.target.className;
	var id = event.target.parentElement.parentElement.getElementsByClassName("order-id")[0].innerHTML;
	var state = event.target.parentElement.parentElement.getElementsByClassName("order_state")[0].innerHTML;
	document.getElementById("orderId").value = id;
	document.getElementById("activity").value = oredrAction;
	if (oredrAction == 'reject' || oredrAction == 'reject_manual') {
		document.getElementById("reject").value = 1;
		if (state != 'WAITING_ACCEPTANCE') {
			alert('Order Must have status WAITING_ACCEPTANCE to reject.');
			return false;
		}
	}
	if (oredrAction == 'confirm' && state != 'WAITING_ACCEPTANCE') {
		alert('Order Must have status WAITING_ACCEPTANCE to accept.');
		return false;
	}
	if (oredrAction == 'shipped' && state != 'SHIPPING') {
		alert('Order status Must be SHIPPING .');
		return false;
	}

	Cn_Cmi_callAPI("orderForm", null);

}

function Cn_Cmi_refundActionHandler(oredrAction, state, id) {
	if (oredrAction == 'refund' && (state == 'SHIPPING' || state == 'SHIPPED' || state == 'RECEIVED')) {
		document.getElementById("refund_order_id").value = id;
		var elem = document.getElementById("refund_dialog");
		if (elem)
			elem.style.display = 'block';
		document.getElementById("orderId_lines").value = id;
		var xmlhttp;
		var url = document.getElementById('orderLinesGet').getAttribute("action");
		document.getElementById("loading-mask").style.display = 'block';
		if (window.XMLHttpRequest) {
			xmlhttp = new XMLHttpRequest();
		} else {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("loading-mask").style.display = 'none';
				var order = xmlhttp.responseText;
				order = JSON.parse(order);
				var order_lines = order.order_lines;
				var html = '';
				for (var o = 0; o < order_lines.length; o++) {
					if (order_lines[o].can_refund) {
						html += '<div class = "leaf-container">';
						html += '<div class = "form-group border grey floater">';
						html += '<span class = "parent-heading">' + order_lines[o].product_title + '</span> <br />';
						html += '<span>Prod sku: ' + order_lines[o].product_sku + ' | Offer sku: ' + order_lines[o].offer_sku + '</span>';
						html += '</div>';
						html += '<div class = "form-group border grey floater">';
						html += '<div class = "radio_holder">';
						var onclick = 'Cn_Cmi_refundFieldsLoad(this,"' + order_lines[o].order_line_id + '")';
						html += "<input class = 'cnc_hide cnc_checkbox refund_" + id + "' id = 'ref_chk_" + order_lines[o].order_line_id + "' type = 'checkbox' name = 'order_line_refunds[]' onclick = '" + onclick + "' value = '" + order_lines[o].order_line_id + "' />";
						html += '<label class="cnc_marketplace_sprite_icons control-label" for="ref_chk_' + order_lines[o].order_line_id + '"></label>';
						html += '</div>';
						html += '</div>';
						html += '<div class = "leaf_refund_ajax" id = "leaf_refund_' + order_lines[o].order_line_id + '">';
						html += '</div>';
						html += '</div>';
					} else {
						html += '<div class = "leaf-container">';
						html += '<div class = "form-group border grey floater">';
						html += '<span class = "parent-heading">' + order_lines[o].product_title + '</span> <br />';
						html += '<span>Prod sku: ' + order_lines[o].product_sku + ' | Offer sku: ' + order_lines[o].offer_sku + '</span>';
						html += '<div>Product has already been refunded or refused</div>';
						html += '</div>';
						html += '<div class = "leaf_refund_ajax" id = "leaf_refund_' + order_lines[o].order_line_id + '">';
						html += '</div>';
						html += '</div>';
					}
				}
				document.getElementById("refund_orderline_ajax").innerHTML = html;
			}
		}
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(Form.serialize('orderLinesGet'));
	} else {
		alert('Order Must have status SHIPPING,SHIPPED or RECEIVED.');
		return false;
	}
}

function Cn_Cmi_refundFieldsLoad(check, order_line_id) {
	var html_fields = document.getElementById("refund_fields").innerHTML;

	if (check.checked == true) {
		document.getElementById("leaf_refund_" + order_line_id).innerHTML = html_fields;
	} else {
		document.getElementById("leaf_refund_" + order_line_id).innerHTML = '';
	}
}

function Cn_Cmi_trackActionHandler(event) {
	var oredrAction = event.target.className;
	var state = event.target.parentElement.parentElement.getElementsByClassName("order_state")[0].innerHTML;
	if (oredrAction == 'tracking' && (state == 'SHIPPING' || state == 'SHIPPED')) {
		var id = event.target.parentElement.parentElement.getElementsByClassName("order-id")[0].innerHTML;
		document.getElementById("orderid").value = id;
		var elem = document.getElementById("tracking");
		elem.style.display = 'block';
	} else {
		alert('Order Must have status SHIPPING or SHIPPED.');
		return false;
	}
}

function Cn_Cmi_orderDetailsActionHandler(order_id) {

	document.getElementById("order_idd_down").value = order_id;
	document.getElementById("orderId_lines").value = order_id;
	var parent = document.getElementById("order_det");
	var elem = document.getElementById("order_details");
	elem.style.display = 'block';
	elem.style.overflow = 'scroll';
	var xmlhttp;
	var url = document.getElementById('orderLinesGet').getAttribute("action");
	document.getElementById("loading-mask").style.display = 'block';
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("loading-mask").style.display = 'none';
			document.getElementById("order_det").innerHTML = '';
			Cn_Cmi_parse(JSON.parse(xmlhttp.responseText), parent);
			xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(Form.serialize('orderLinesGet'));

}

function Cn_Cmi_checkboxActionHandler(value) {
	var allInputs = document.getElementsByTagName("input");
	for (var inputIndex = 0, max = allInputs.length; inputIndex < max; inputIndex++) {
		if (allInputs[inputIndex].type === 'checkbox' && allInputs[inputIndex].className == value) {
			if (allInputs[inputIndex].checked == true)
				allInputs[inputIndex].checked = false;
			else
				allInputs[inputIndex].checked = true;
		}
	}
}

function Cn_Cmi_refreshSchedule(prod_offer) {
	var xmlhttp;
	var url = document.getElementById('schduleRefresh').getAttribute("action");
	if (prod_offer == 'products')
		document.getElementById('ajax_schedule_type').value = 'products';
	else
		document.getElementById('ajax_schedule_type').value = 'offers';
	document.getElementById("loading-mask").style.display = 'block';
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("loading-mask").style.display = 'none';
			var schedules = JSON.parse(xmlhttp.responseText);
			var html = '';
			for (i = 0; i < schedules.length; i++) {
				if (schedules[i].messages == null)
					schedules[i].messages = '';
				if (schedules[i].executed_at == null)
					schedules[i].executed_at = '';
				html += '<tr class="odd">';
				html += '<td class="center">' + schedules[i].schedule_id + '</td>';
				html += '<td class="center">' + schedules[i].status + '</td>';
				html += '<td class="center">' + schedules[i].scheduled_at + '</td>';
				html += '<td class="center">' + schedules[i].messages + '</td>';
				html += '<td class="center">' + schedules[i].executed_at + '</td>';
				html += '</tr>';
			}
			if (prod_offer == 'products')
				document.getElementById("prod_ajax_update").innerHTML = html;
			else
				document.getElementById("offer_ajax_update").innerHTML = html;
		}
	}
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(Form.serialize('schduleRefresh'));
}

function Cn_Cmi_parse(node, parent) {
	for (var key in node) {
		if (typeof node[key] == 'object' && node[key] != null && node[key] != '') {
			if (typeof node[key] != 'function') {
				Cn_Cmi_parse(node[key], Cn_Cmi_creatParent(key, parent));
			}
		} else {
			if (typeof node[key] != 'function')
				Cn_Cmi_creatleaf(key, node[key], parent)
		}
	}
	return node;
};

function Cn_Cmi_creatParent(key, parent) {
	var container = window.document.createElement('div');
	container.setAttribute('class', 'parsed parent-container');
	var heading = window.document.createElement('span');
	heading.setAttribute('class', 'parent-heading');

	if (key >= 0 && key <= 100) {
		key = parseInt(key) + 1;
		key = "Item " + key;
	} else {
		key = key.split('_').join(' ');
	}

	heading.innerHTML = key;
	container.appendChild(heading);
	parent.appendChild(container);

	return container;
};

function Cn_Cmi_creatleaf(key, value, parent) {
	var element;
	var container = window.document.createElement('div');
	container.setAttribute('class', 'parsed leaf-container');
	var heading = window.document.createElement('span');
	heading.setAttribute('class', 'parsed child-heading');
	var span = window.document.createElement('span');
	key = key.split('_').join(' ');

	heading.innerHTML = key + ': ';
	span.innerHTML = value;
	container.appendChild(heading);
	container.appendChild(span);
	if (!parent.getElementsByClassName('main-container').length) {
		var mainContainer = window.document.createElement('div');
		mainContainer.setAttribute('class', 'main-container');
		parent.appendChild(mainContainer);
	}
	parent.getElementsByClassName('main-container')[0].appendChild(container);
};

function Cn_Cmi_closePopup(event) {
	var element = event.target.parentElement;
	element.style.display = 'none';
}

function Cn_Cmi_deleteOffer() {
	var check = confirm('Are you sure you want to delete this offer?');
	if (check)
		Cn_Cmi_showWaitNoReason();
	else
		return false;
}

function Cn_Cmi_deleteBulkOffers() {
	var offer_list_check = JSON.stringify(Cn_Cmi_getCheckedBoxes('offer_list_check'));
	if (offer_list_check == '{}') {
		alert('Please select any offers to delete');
		return false;
	} else {
		var check = confirm('Are you sure you want to delete these offers?');
		if (check) {
			document.getElementById("offer_ids").value = offer_list_check;
			Cn_Cmi_showWaitNoReason();
		} else {
			return false;
		}
	}
}

function Cn_Cmi_openOfferMsg(offer_id) {
	document.getElementById("msg_offer_dialog").style.display = 'block';
	document.getElementById("offer_id_msg").value = offer_id;
	document.getElementById('offer_sku_msg').value = offer_id;
	document.getElementById("make_zero_" + offer_id).innerHTML = '0';
	var xmlhttp;
	var url = document.getElementById('messageOfferGet').getAttribute("action");
	document.getElementById("loading-mask").style.display = 'block';
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("loading-mask").style.display = 'none';
			var msg = JSON.parse(xmlhttp.responseText);
			var messages = msg.messages;
			var html = '';
			for (var key = 0; key < messages.length; key++) {
				if (messages[key].from_type == 'CUSTOMER' || messages[key].from_type == 'OPERATOR') {
					html += '<div class = "cust_msg clear">';
					html += '<div class= "offer_msg_message">';
					html += '<div class="offer_msg_date">';
					html += messages[key].date_created;
					html += '</div>';
					html += '<b>' + messages[key].subject + '</b> <br />';
					html += messages[key].body;
					html += '<div class="arrow-right"></div>';
					html += '</div>';
					html += '<div class = "by_him">';
					html += messages[key].from_name;
					html += '</div>';
					html += '</div>';
				} else {
					html += '<div class = "your_msg clear">';
					html += '<div class= "offer_msg_answer">';
					html += '<div class="offer_msg_date">';
					html += messages[key].date_created;
					html += '</div>';
					html += '<b>' + messages[key].subject + '</b> <br />';
					html += messages[key].body;
					html += '<div class="arrow-left"></div>';
					html += '</div>';
					html += '<div class = "by_you">';
					html += 'You';
					html += '</div>';
					html += '</div>';
				}
			}
			document.getElementById("offer_msg_fieldset_add").innerHTML = html;
			Cn_Cmi_scroll_holder('scroll_holder');
		}
	}
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(Form.serialize('messageOfferGet'));
}

function Cn_Cmi_answerMessage() {
	if (document.getElementById('message_answer').value.trim() == '') {
		alert('Please enter a reply');
		return false;
	}
	var xmlhttp;
	var url = document.getElementById('messageOfferAnswer').getAttribute("action");
	document.getElementById("loading-mask").style.display = 'block';
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("loading-mask").style.display = 'none';
			var html = document.getElementById("offer_msg_fieldset_add").innerHTML;
			var answer = document.getElementById("message_answer");
			html += '<div class="your_msg clear">';
			html += '<div class="offer_msg_answer">';
			html += '<div class="offer_msg_date">';
			html += '06/07/1987 01:00:89';
			html += '</div>';
			html += answer.value;
			html += '<div class="arrow-left"></div>';
			html += '</div>';
			html += '<div class="by_you">';
			html += 'You';
			html += '</div>';
			html += '</div>';
			document.getElementById("offer_msg_fieldset_add").innerHTML = html;
			Cn_Cmi_scroll_holder('scroll_holder');
			answer.value = '';
		}
	}
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(Form.serialize('messageOfferAnswer'));
}

function Cn_Cmi_selectCarrierCode(sel) {
	var carriers = sel.value.split("-_-");
	document.getElementById("courier").value = carriers[0];
	document.getElementById("carriercode").value = carriers[1];
}

function Cn_Cmi_bulkExport() {
	items = JSON.stringify(Cn_Cmi_getCheckedBoxes("check_name"));
	if (items == '{}') {
		alert('Please select any orders from the list to export');
		return false;
	} else {
		document.getElementById("order_idd_down_bulk").value = items;
	}
}

function Cn_Cmi_bulkChangeOrderRead() {
	items = JSON.stringify(Cn_Cmi_getCheckedBoxes("check_name"));
	if (items == '{}') {
		alert('Please select any orders from the list to mark as read');
		return false;
	} else {
		document.getElementById("order_unread_bulk").value = items;
	}
}

function Cn_Cmi_showWaitNoReason() {
	document.getElementById("loading-mask").style.display = 'block';
}

function Cn_Cmi_validateAttributes() {
	check_items = JSON.stringify(Cn_Cmi_getCheckedBoxes("field_key_values[]"));
	if (check_items == '{}') {
		alert('Please select atleast one attribute from the list');
		return false;
	} else {
		Cn_Cmi_showWaitNoReason();
	}

}

function Cn_Cmi_Obj_to_Array(obj) {
	return Object.keys(obj).map(function (key) {return obj[key]})
}

function Cn_Cmi_getImportList_Products(onload) {
	var checked_boxes = Cn_Cmi_getCheckedBoxes("product_import");
	check_items = JSON.stringify(checked_boxes);
	if (!onload && check_items == '{}') {
		alert('Please select any imports from the list');
	} else {
		document.getElementById("prod_imports_id").value = check_items;
		var xmlhttp;
		var url = document.getElementById('importList').getAttribute("action");
		document.getElementById("loading-mask").style.display = 'block';
		if (window.XMLHttpRequest) {
			xmlhttp = new XMLHttpRequest();
		} else {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("loading-mask").style.display = 'none';
				var template = document.createElement('template');
				template.innerHTML = xmlhttp.responseText;
				var tbody = template.content.childNodes[0];
				product_table = document.getElementsByName("product_imports_table")[0];
				for (var i = 0; i < product_table.childNodes.length; i++) {
					if (product_table.childNodes[i].nodeName == 'TBODY') {
						product_table.removeChild(product_table.childNodes[i]);
					}
				}
				product_table.appendChild(tbody);
				if(!onload) {
					var ids = Cn_Cmi_Obj_to_Array(checked_boxes);
					var message = "Successfully refreshed status for Import ID ";
					if(ids.length > 1) {
						message = message.replace("status", "statuses");
						message = message.replace("ID", "ID's");
					}
					Cn_Cmi_showSuccessMessage("product_imports_messages", message + Cn_Cmi_Obj_to_Array(checked_boxes).join(", "));
				}
			}
		}
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(Form.serialize('importList'));
	}
}

function Cn_Cmi_getImportList_Offers(onload) {
	var checked_boxes = Cn_Cmi_getCheckedBoxes("offer_import");
	check_items = JSON.stringify(checked_boxes);
	if (!onload && check_items == '{}') {
		alert('Please select any imports from the list');
	} else {
		document.getElementById("offer_imports_id").value = check_items;
		var xmlhttp;
		var url = document.getElementById('importList').getAttribute("action");
		document.getElementById("loading-mask").style.display = 'block';
		if (window.XMLHttpRequest) {
			xmlhttp = new XMLHttpRequest();
		} else {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("loading-mask").style.display = 'none';
				var template = document.createElement('template');
				template.innerHTML = xmlhttp.responseText;
				var tbody = template.content.childNodes[0];
				offer_table = document.getElementsByName("offer_imports_table")[0];
				for (var i = 0; i < offer_table.childNodes.length; i++) {
					if (offer_table.childNodes[i].nodeName == 'TBODY') {
						offer_table.removeChild(offer_table.childNodes[i]);
					}
				}
				offer_table.appendChild(tbody);
				if(!onload) {
					var ids = Cn_Cmi_Obj_to_Array(checked_boxes);
					var message = "Successfully refreshed status for Import ID ";
					if(ids.length > 1) {
						message = message.replace("status", "statuses");
						message = message.replace("ID", "ID's");
					}
					Cn_Cmi_showSuccessMessage("offer_imports_messages", message + Cn_Cmi_Obj_to_Array(checked_boxes).join(", "));
				}
			}
		}
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(Form.serialize('importList'));
	}
}

function Cn_Cmi_showSuccessMessage(message_blok_id, message){

	var oldMessages = document.getElementById("messages")
	if (typeof(oldMessages) != 'undefined' && oldMessages != null)
	{
		oldMessages.remove();
	}
	var messageHtml = "<ul class=\"messages\"><li class=\"success-msg\"><ul><li><span>$MESSAGE<\/span><\/li><\/ul><\/li><\/ul>";
	messageHtml = messageHtml.replace("$MESSAGE", message);
	document.getElementById(message_blok_id).innerHTML = messageHtml;
}


function Cn_Cmi_open_incident(order_id) {
	document.getElementById("incident_popup_" + order_id).style.display = 'block';
	Cn_Cmi_changeUnreadIncident(order_id);
}

function Cn_Cmi_OpenOrderMessage(order_id, number_msg) {
	document.getElementById("orderId").value = order_id;
	document.getElementById("order_msg_order_id").value = order_id;
	document.getElementById("make_zero_" + order_id).innerHTML = '0';
	var xmlhttp;
	var url = document.getElementById("url_ajax_msgForm").value;
	document.getElementById("loading-mask").style.display = 'block';
	var ordermsg_number = document.getElementById("ordermsg_number");
	if (ordermsg_number)
		ordermsg_number.innerHTML = parseInt(ordermsg_number.innerHTML) - parseInt(number_msg);
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("loading-mask").style.display = 'none';
			if (xmlhttp.responseText) {
				var msg = JSON.parse(xmlhttp.responseText);
				var messages = msg.messages;
				var html = '';
				var last_subject = '';
				for (var key = 0; key < messages.length; key++) {
					if (messages[key].from_type == 'CUSTOMER' || messages[key].from_type == 'OPERATOR') {
						html += '<div class = "cust_msg clear">';
						html += '<div class= "offer_msg_message">';
						html += '<div class="order_msg_date">';
						html += messages[key].date_created;
						html += '</div>';
						html += '<b>' + messages[key].subject + '</b> <br />';
						html += messages[key].body;
						html += '<div class="arrow-right"></div>';
						html += '</div>';
						html += '<div class = "by_him">';
						html += messages[key].from_name;
						html += '</div>';
						html += '</div>';
					} else {
						html += '<div class = "your_msg clear">';
						html += '<div class= "offer_msg_answer">';
						html += '<div class="order_msg_date">';
						html += messages[key].date_created;
						html += '</div>';
						html += '<b>' + messages[key].subject + '</b> <br />';
						html += messages[key].body;
						html += '<div class="arrow-left"></div>';
						html += '</div>';
						html += '<div class = "by_you">';
						html += 'You';
						html += '</div>';
						html += '</div>';
					}
					last_subject = messages[key].subject;
				}
				document.getElementById("ajax_message_insert").innerHTML = html;
				document.getElementById("message_subject").value = 'Re: ' + last_subject;
				Cn_Cmi_scroll_holder('scroll_holder');
			}
		}
	}
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(Form.serialize('orderForm'));

	document.getElementById("order_message_dialog").style.display = 'block';
}

function Cn_Cmi_scroll_holder(id) {
	var element = document.getElementById(id);
	element.scrollTop = element.scrollHeight;
}

function Cn_Cmi_CreateNewMsg() {
	if (document.getElementById('message_subject').value.trim() == '') {
		alert('Please enter a subject');
		document.getElementById('message_subject').focus();
		return false;
	} else if (document.getElementById('message_answer').value.trim() == '') {
		alert('Please enter a reply');
		document.getElementById('message_answer').focus();
		return false;
	}
	var xmlhttp;
	var url = document.getElementById('message_order_answer').getAttribute("action");
	document.getElementById("loading-mask").style.display = 'block';
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("loading-mask").style.display = 'none';
			var message_subject = document.getElementById("message_subject").value;
			var message_answer = document.getElementById("message_answer").value;
			html = document.getElementById("ajax_message_insert").innerHTML;
			html += '<div class = "your_msg clear">';
			html += '<div class= "offer_msg_answer">';
			html += '<div class="order_msg_date">';
			html += 'Now';
			html += '</div>';
			html += '<b>' + message_subject + '</b> <br />';
			html += message_answer;
			html += '<div class="arrow-left"></div>';
			html += '</div>';
			html += '<div class = "by_you">';
			html += 'You';
			html += '</div>';
			html += '</div>';
			document.getElementById("ajax_message_insert").innerHTML = html;
			document.getElementById("message_answer").value = '';
			Cn_Cmi_scroll_holder('scroll_holder');
		}
	}
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(Form.serialize('message_order_answer'));
}

function Cn_Cmi_refundFormValidation(checkboxes_checked) {
	var order_id = document.getElementById('refund_order_id').value;
	var checks = document.getElementsByClassName("cnc_hide cnc_checkbox refund_" + order_id);
	var checks_ar = [];
	var return_val = '';
	for (var c = 0; c < checks.length; c++) {
		checks_ar.push(checks[c].checked);
	}
	var val = checks_ar.indexOf(true);
	if (val == '-1') {
		alert('Please Select atleast one product from the list for refund');
		return_val = 'products';
	}
	var checkboxes_checked_ar = Object.keys(checkboxes_checked).map(function(key) {
		return checkboxes_checked[key]
	});

	checkboxes_checked_ar.forEach(function(checked) {
		var particular_div = document.getElementById('leaf_refund_' + checked);
		var fields = particular_div.getElementsByClassName("refund_orderinputs");
		for (var j = 0; j < fields.length; j++) {
			if (fields[j].value == '') {
				return_val = 'mandate_fields';
				break;
			}
		}
	});
	if (!return_val)
		return 'yes';
	else {
		if (return_val == 'mandate_fields')
			alert('Please fill all mandatory fields');
		return return_val;
	}
}

function Cn_Cmi_callAPI(id, data) {
	var xmlhttp;
	var checkboxes_checked = [];
	checkboxes_checked = Cn_Cmi_getCheckedBoxes('order_line_refunds[]');
	if (id == 'refundForm') {
		var val = Cn_Cmi_refundFormValidation(checkboxes_checked);
		if (val == 'products' || val == 'mandate_fields')
			return false;
	}
	var url = document.getElementById(id).getAttribute("action");
	document.getElementById("loading-mask").style.display = 'block';
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			if (xmlhttp.responseText == "") {
				Cn_Cmi_showSuccess('status updated successfully');
			} else {
				Cn_Cmi_showError(xmlhttp.responseText);
			}
		}
	}
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	//alert(Form.serialize(id));
	if (data)
		xmlhttp.send(Form.serialize(id), data);
	else
		xmlhttp.send(Form.serialize(id));
}

function Cn_Cmi_schedulerStock(ev) {
	document.getElementById('stock-form').style.display = 'block';
	document.getElementById('product-form').style.display = 'none';
}

function Cn_Cmi_schedulerProduct(ev) {
	document.getElementById('product-form').style.display = 'block';
	document.getElementById('stock-form').style.display = 'none';
}

function Cn_Cmi_getCheckedBoxes(chkboxName) {
	var checkboxes = document.getElementsByName(chkboxName);
	var checkboxesChecked = {};
	// loop over them all
	for (var checkboxesIndex = 0; checkboxesIndex < checkboxes.length; checkboxesIndex++) {
		// And stick the checked ones onto an array...
		if (checkboxes[checkboxesIndex].checked) {
			checkboxesChecked[checkboxesIndex] = checkboxes[checkboxesIndex].value;
		}
	}
	// Return the array if it is non-empty, or null
	// alert(checkboxesChecked);
	return checkboxesChecked;
}

function Cn_Cmi_getCheckedBoxesStatus(chkboxName, action) {
	var checkboxes = document.getElementsByName(chkboxName);
	var checkboxesChecked = {};
	// loop over them all
	for (var checkboxesIndex = 0; checkboxesIndex < checkboxes.length; checkboxesIndex++) {
		// And stick the checked ones onto an array...
		if (checkboxes[checkboxesIndex].checked) {
			checkboxesChecked[checkboxesIndex] = checkboxes[checkboxesIndex].getAttribute("data-action");
			if (action == "accept" && checkboxesChecked[checkboxesIndex] != "WAITING_ACCEPTANCE") {
				alert('Order Must have status WAITING_ACCEPTANCE to accept.');
				return 'incorrect';
			}
			if (action == "shipped" && checkboxesChecked[checkboxesIndex] != "SHIPPING") {
				alert('Order Must be SHIPPING.');
				return 'incorrect';
			}
		}
	}
	return true;
}

function Cn_Cmi_statusChange(event) {
	var items = [];
	var error = 0;
	var action = event.target.getAttribute("data-action");

	var id = event.target.parentElement.parentElement.getElementsByClassName("order-id")[0].innerHTML;
	items = JSON.stringify(Cn_Cmi_getCheckedBoxes("check_name"));

	status = Cn_Cmi_getCheckedBoxesStatus("check_name", action);
	if (status != 'incorrect') {
		document.getElementById("orderId").value = items;
		document.getElementById("activity").value = action;
		switch (action) {
			case "accept":
				Cn_Cmi_callAPI('orderForm');
				break;
			case "shipped":
				Cn_Cmi_callAPI('orderForm');
				break;
		}
	}
}

/*Order Page changes*/
function Cn_Cmi_toggleContent() {
	var contentId = document.getElementById("filter-container");
	contentId.style.display == "block" ? contentId.style.display = "none" : contentId.style.display = "block";
	var background_color = document.getElementById("filter-opener");
	background_color.style.backgroundColor == "" ? background_color.style.backgroundColor = '#efeeed' : background_color.style.backgroundColor = "";
	var arrowToggle = document.getElementById("filter-dropdown") || document.getElementById("filter-uparrow");
	arrowToggle.id == "filter-dropdown" ? arrowToggle.id = "filter-uparrow" : arrowToggle.id = "filter-dropdown";

}

function Cn_Cmi_schedulerDisplay() {
	var schedulerRecursive = document.getElementById("timeOption");
	var selectedOption = schedulerRecursive.options[schedulerRecursive.selectedIndex].value;
	if (selectedOption == "recursive") {
		var scheduleDisplay = document.getElementById("schedulerec");
		scheduleDisplay.style.display = "block";
		var scheduleOnce = document.getElementById("schedulerOnce");
		scheduleOnce.style.display = "none";
	} else {
		var scheduleDisplay = document.getElementById("schedulerec");
		scheduleDisplay.style.display = "none";
		var scheduleOnce = document.getElementById("schedulerOnce");
		scheduleOnce.style.display = "block";
	}
}

function Cn_Cmi_schedulerDisplayOffer() {
	var selectedOption = document.getElementById("timeOption_offer").value;

	if (selectedOption == "recursive") {
		document.getElementById("schedulerec_offer").style.display = "block";;
		document.getElementById("schedulerOnce_offer").style.display = "none";
	} else {
		document.getElementById("schedulerec_offer").style.display = "none";
		document.getElementById("schedulerOnce_offer").style.display = "block";

	}
}



function Cn_Cmi_showrec() {
	var sel = document.getElementById("recurrence").value;
	var element = document.querySelectorAll("[id^='showcustom-']");
	for (elemIndex = 0; elemIndex < element.length; elemIndex++) {
		element[elemIndex].style.display = "none";
	}
	//hide_required
	document.getElementById('showcustom-' + sel).style.display = 'block';
}

function Cn_Cmi_openOfferEdit(offerSku) {
	var xmlhttp;
	var url = document.getElementById('offerSingle').getAttribute("action");
	document.getElementById("offer_dialog").style.display = 'block';
	document.getElementById("loading-mask").style.display = 'block';
	document.getElementById("offer_id_ajax").value = offerSku;
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("loading-mask").style.display = 'none';
			var offer_details = JSON.parse(xmlhttp.responseText);
			var all_texts = document.getElementById('offer_dialog').getElementsByClassName('cnc_marketplace_text_box');
			document.getElementById('quantity').value = offer_details.quantity ? offer_details.quantity : '';
			document.getElementById('price_offer_cmi').value = offer_details.price ? offer_details.price : '';
			document.getElementById('min_quantity_alert').value = offer_details.min_quantity_alert ? offer_details.min_quantity_alert : '';
			document.getElementById('available_started').value = offer_details.available_start_date ? offer_details.available_start_date : '';
			document.getElementById('available_ended').value = offer_details.available_end_date ? offer_details.available_end_date : '';
			document.getElementById('description').value = offer_details.description ? offer_details.description : '';
			document.getElementById('start_date').value = offer_details.discount ? offer_details.discount.start_date : '';
			document.getElementById('end_date').value = offer_details.discount ? offer_details.discount.end_date : '';
			document.getElementById('origin_price').value = offer_details.discount ? offer_details.discount.origin_price : '';
		}
	}
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(Form.serialize('offerSingle'));

}

//Login Form Validation

function Cn_Cmi_FormManValidation(classN) {
	if (document.getElementsByClassName('validation-advice').length > 0) {
		var msgs = document.getElementsByClassName('validation-advice');

		for (var c = 0; c < msgs.length; c++) {
			msgs[c].setAttribute("class", "cnc_absolute validation-advice cnc_hide");
		}
	}
	if (document.getElementsByClassName('success-msg').length > 1)
		document.getElementsByClassName('success-msg')[1].style.display = 'none';
	if (document.getElementsByClassName('error-msg').length > 1)
		document.getElementsByClassName('error-msg')[1].parentElement.style.display = 'none';
	var names = document.getElementsByClassName(classN);
	var stop = [];
	for (var elementIndex = 0; elementIndex < names.length; elementIndex++) {
		var htmlObj = names[elementIndex];
		var dataAttr = htmlObj.getAttribute('data-attribute');
		if (dataAttr == "select" || dataAttr == "input" || dataAttr == "url") {
			if (dataAttr == "url") {
				var urlregex = new RegExp("^(http|https):\/\/(www.)?");
				var urlValue = htmlObj.value;
				if (!urlregex.test(urlValue) || urlValue == "") {
					document.getElementsByClassName('error_msg_none')[0].style.display = 'block';
					if (urlValue == "")
						document.getElementsByClassName('error_msg_block')[0].innerHTML = "Please fill all the mandatory fields.";
					else
						document.getElementsByClassName('error_msg_block')[0].innerHTML = "Please enter a Valid URL.";
					htmlObj.focus();
					stop.push(0);
				}
			}
			var inputvalue = htmlObj.value;
			if (inputvalue == "") {
				document.getElementsByClassName('error_msg_none')[0].style.display = 'block';
				document.getElementsByClassName('error_msg_block')[0].innerHTML = "Please fill all the mandatory fields.";
				htmlObj.focus();
				var errorMessageArrow = document.createElement("div");
				errorMessageArrow.innerHTML = 'This is a required field.';
				if (htmlObj.tagName === 'SELECT')
					errorMessageArrow.className = 'cnc_absolute validation-advice';
				else {
					htmlObj.parentNode.style.position = 'relative';
					errorMessageArrow.className = 'cnc_absolute cnc_input_error_top validation-advice';
				}
				var remove_two = htmlObj.parentNode.getElementsByClassName('validation-advice')[0];
				if (remove_two != undefined)
					remove_two.parentNode.removeChild(remove_two);
				htmlObj.parentNode.appendChild(errorMessageArrow);
				stop.push(0);
			}
		} else {
			var inputvalue = htmlObj.value;
			if (inputvalue == "") {
				document.getElementsByClassName('error_msg_none')[0].style.display = 'block';
				document.getElementsByClassName('error_msg_block')[0].innerHTML = "Please fill all the mandatory fields.";
				htmlObj.focus();
				stop.push(0);
			}
		}
	}
	if (stop.indexOf(0) == '-1') {
		Cn_Cmi_showWaitNoReason();
	} else {
		return false;
	}
}

//Show filters in orders page
function Cn_Cmi_removeFromFilter(value) {
	var checkboxes = document.getElementsByName('filter_item[]');
	// loop over them all
	for (var checkboxesIndex = 0; checkboxesIndex < checkboxes.length; checkboxesIndex++) {
		if (checkboxes[checkboxesIndex].checked) {

			if (value == checkboxes[checkboxesIndex].value) {
				var elements = checkboxes[checkboxesIndex];
				var id = elements.getAttribute('id');
				document.getElementById(id).checked = false;
				break;
			}
		}
	}
	document.forms["filter_form"].submit();
	Cn_Cmi_showWaitNoReason();
}

function Cn_Cmi_scrollFilter(dir, px) {
	var scroller = document.getElementById('scroller');
	if (dir == 'l') {
		scroller.scrollLeft -= px;
	} else if (dir == 'r') {
		scroller.scrollLeft += px;
	}
}


function Cn_Cmi_ListNewOrders() {
	document.forms["newOrdersForm"].submit();
}

function Cn_Cmi_ListUnreadMsg(type) {
	if (type == 'order')
		document.forms["unread_msg_order"].submit();
	else
		document.forms["unread_msg_offer"].submit();
}

function Cn_Cmi_orderLinePopup(orderId, state) {
	if (state != 'WAITING_ACCEPTANCE') {
		alert('Order Must have status WAITING_ACCEPTANCE to accept or reject');
		return false;
	}
	document.getElementById("order_line_accept").style.display = 'block';
	document.getElementById("orderId_lines").value = orderId;
	document.getElementById("order_id_lines").value = orderId;
	var xmlhttp;
	var url = document.getElementById('orderLinesGet').getAttribute("action");
	document.getElementById("loading-mask").style.display = 'block';
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("loading-mask").style.display = 'none';
			var order = xmlhttp.responseText;
			order = JSON.parse(order);
			var order_lines = order.order_lines;
			var html = '';
			var accept = {};
			var reject = {};
			for (var o = 0; o < order_lines.length; o++) {
				html += '<div class = "leaf-container">';
				html += '<div class = "form-group border grey floater">';
				html += '<span class = "parent-heading">' + order_lines[o].product_title + '</span> <br />';
				html += '<span>Prod sku: ' + order_lines[o].product_sku + ' | Offer sku: ' + order_lines[o].offer_sku + '</span>';
				html += '</div>';
				html += '<div class = "form-group border grey floater">';
				html += '<div class = "radio_holder">';
				accept = {
					'accept': order_lines[o].order_line_id
				};
				html += "<input class = 'accept_" + orderId + "' type = 'radio' name = 'order_line[" + o + "]' value = '" + JSON.stringify(accept) + "' />Accept";
				html += '</div>';
				html += '<div class = "radio_holder">';
				reject = {
					'reject': order_lines[o].order_line_id
				};
				html += "<input class = 'reject_" + orderId + "' type = 'radio' name = 'order_line[" + o + "]' value = '" + JSON.stringify(reject) + "' />Reject";
				html += '</div>';
				html += '</div>';
				html += '</div>';
			}
			document.getElementById("order_line_ajax").innerHTML = html;
		}
	}
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(Form.serialize('orderLinesGet'));

}

function Cn_Cmi_ConfirmValidation() {
	var order_id = document.getElementById("orderId_lines").value;
	var radios = document.getElementsByClassName("accept_" + order_id);
	var radios_rej = document.getElementsByClassName("reject_" + order_id);
	for (var r = 0; r < radios.length; r++) {
		if (!radios[r].checked && !radios_rej[r].checked) {
			alert('Please select accept or reject for each order line before submission');
			return false;
		}
	}
	Cn_Cmi_showWaitNoReason();
}

function Cn_Cmi_ListUnreadOrders(val) {
	document.getElementById("unread_orders").value = val + '_read';
	document.forms["unreadOrdersForm"].submit();
}

function Cn_Cmi_changeUnreadIncident(order_id) {
	//alert(order_id);
	document.getElementById("incident_orderId").value = order_id;
	var xmlhttp;
	var url = document.getElementById('incidentReadForm').getAttribute("action");
	var incident_number = document.getElementById("incident_number");
	if (incident_number)
		incident_number.innerHTML = parseInt(incident_number.innerHTML) - 1;

	document.getElementById("loading-mask").style.display = 'block';
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

			document.getElementById("loading-mask").style.display = 'none';
		}
	}
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(Form.serialize('incidentReadForm'));
}

function getStyle(id, name) {
	var element = document.getElementById(id);
	return element.currentStyle ? element.currentStyle[name] : window.getComputedStyle ? window.getComputedStyle(element, null).getPropertyValue(name) : null;
}

function Cn_Cmi_accordianToggle(id_showhide, arrow) {
	var value = getStyle(id_showhide, 'display');
	document.getElementById(arrow).classList.toggle('closed');
	if (value == 'none')
		document.getElementById(id_showhide).style.display = 'block';
	else
		document.getElementById(id_showhide).style.display = 'none';
}

function Cn_Cmi_accordianOpen(id_showhide, arrow) {
	var value = getStyle(id_showhide, 'display');
	document.getElementById(arrow).classList.toggle('closed');
	if (value == 'none')
		Cn_Cmi_accordianExpand(id_showhide);
	else
		Cn_Cmi_accordianCollapse(id_showhide);
}

function Cn_Cmi_accordianCollapse(id_showhide) {
	document.getElementById(id_showhide).style.display = 'none';
}

function Cn_Cmi_accordianExpand(id_showhide) {
	document.getElementById(id_showhide).style.display = 'block';
}


function Cn_Cmi_dateOpen(id)
{
    var calendars = document.getElementsByClassName('calendar');
    setTimeout(function () {
        var calendars = document.getElementsByClassName('calendar');
        for(var i=0;i<calendars.length;i++)
        {
            calendars[i].style.left = parseInt(document.getElementById(id).getBoundingClientRect().left, 10)+'px';
            calendars[i].style.top = parseInt(document.getElementById(id).getBoundingClientRect().top, 10)+30+'px';
        }
    }, 1);
}
document.addEventListener('DOMContentLoaded', function() {
	if (document.body.className.match(/adminhtml-marketplace-offers-index/) || document.body.className.match(/adminhtml-marketplace-scheduler-index/)) {
		var calender_array = document.getElementsByClassName("cnc_date_div");

		for (i = 0; i < calender_array.length; i++) {
			var calender_id = calender_array[i].id;
			var input = calender_array[i].getElementsByTagName("input")[0];
			if (calender_id) {
				Calendar.setup({
					inputField: input,
					ifFormat: '%Y-%m-%eT%H:%M:00Z',
					button: calender_id,
					align: 'Bl',
					showsTime: true,
					singleClick: true
				});
                Cn_Cmi_dateOpen();
			}
		}
	}
	if (document.body.className.match(/marketplace-adminhtml-orders-index/) ||
		document.body.className.match(/marketplace-adminhtml-offers-index/) ||
		document.body.className.match(/marketplace-adminhtml-scheduler-index/)) {
		document.body.onclick = function(event) {
			var target = event.originalTarget;
			var check = Cn_Cmi_hasSomeParentTheClass(target, 'modal');
            var check_calender = Cn_Cmi_hasSomeParentTheClass(target, 'calendar');
			var check_clicker = Cn_Cmi_hasSomeParentTheClass(target, 'modal_open');
			if (check_clicker != true && check != true && check_calender != true) {
				var all_models = document.getElementsByClassName('modal');
				for (var m = 0; m < all_models.length; m++) {
					all_models[m].style.display = 'none';
				}
			}
		}

		function Cn_Cmi_hasSomeParentTheClass(element, classname) {
			if (element.className != undefined && element.className.split(' ').indexOf(classname) >= 0) return true;
			return element.parentNode && Cn_Cmi_hasSomeParentTheClass(element.parentNode, classname);
		}
	}
	if (document.body.className.match(/marketplace-adminhtml-orders-index/)) {
		function Cn_Cmi_getTimeRemaining(endtime) {
			var t = Date.parse(endtime) - Date.parse(new Date());
			var seconds = Math.floor((t / 1000) % 60);
			var minutes = Math.floor((t / 1000 / 60) % 60);
			var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
			var days = Math.floor(t / (1000 * 60 * 60 * 24));
			return {
				'total': t,
				'days': days,
				'hours': hours,
				'minutes': minutes,
				'seconds': seconds
			};
		}

		function Cn_Cmi_initializeClock() {
			id = 'cnc_clockdiv';
			var clock = document.getElementsByClassName(id);
			var stop = [];
			for (var i = 0; i < clock.length; i++) {
				var clock_element = document.getElementById(clock[i].id);
				if (clock_element.querySelector('.clock_stopper').value == 0) {
					var order_status = clock_element.querySelector('.order_status_date').value;
					if (order_status == 'WAITING_ACCEPTANCE' || order_status == 'SHIPPING') {
						var deadline = clock_element.querySelector('.update_timer').value;
						var endtime = deadline.replace(/-/g, '/');
						var tomorrow = new Date(endtime);
						if (order_status == 'WAITING_ACCEPTANCE')
							tomorrow.setDate(tomorrow.getDate() + 1);
						else
							tomorrow.setDate(tomorrow.getDate() + 3);
						if (Date.parse(new Date()) >= Date.parse(tomorrow)) {
							document.getElementById(clock[i].id).querySelector('.cnc_clockdiv_inner').innerHTML = 'SLA Breached';
							document.getElementById(clock[i].id).querySelector('.clock_stopper').value = 1;
							stop.push(1);
						} else {
							endtime = tomorrow;
							var daysSpan = clock_element.querySelector('.days');
							var hoursSpan = clock_element.querySelector('.hours');
							var minutesSpan = clock_element.querySelector('.minutes');
							var secondsSpan = clock_element.querySelector('.seconds');

							function updateClock() {
								var t = Cn_Cmi_getTimeRemaining(endtime);
								daysSpan.innerHTML = t.days;
								hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
								minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
								secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);
							}
							updateClock();
							stop.push(0);
						}
					} else {
						document.getElementById(clock[i].id).querySelector('.cnc_clockdiv_inner').innerHTML = 'NA';
						document.getElementById(clock[i].id).querySelector('.clock_stopper').value = 1;
						stop.push(1);
					}
				}
			}
			if (stop.indexOf(0) == '-1') {
				clearInterval(clocker);
			}
		}
		var clocker = setInterval(Cn_Cmi_initializeClock, 1000);
	}
});