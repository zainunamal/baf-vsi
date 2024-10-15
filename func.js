function cekDeposit(setoran) {
	var deposit = document.getElementById("deposit");
	if (deposit != null) {
		deposit = deposit.value;
		if (deposit >= 0 && deposit <= setoran) {
			document.getElementById('formDeposit').submit();
		} else {
			alert("Deposit value invalid");
		}
	}
}

function redirect(action, formId) {
	var form = document.getElementById(formId);
	if (form != null) {
		form.action = action;
		form.submit();
	}
}

var winDetail = null;

function showDetailPpid(area, module, id) {
	var url = Base64.encode("a=" + area + "&m=" + module + "&i=" + id);
	if (!winDetail) {
		winDetail = window.open(
			"view/showDetailPpid.php?param=" + url,
			"Detail PPID", 
			"toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1, resizable=1, width=300, height=500");
	} else if (winDetail.closed) {
		winDetail = window.open(
			"view/showDetailPpid.php?param=" + url,
			"Detail PPID", 
			"toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1, resizable=1, width=300, height=500");
	} else {
		winDetail.focus();
	}
}

function cekConfirmPassword(id1, id2) {
	var pwd = document.getElementById(id1).value;
	var pwd2 = document.getElementById(id2).value;
	if (pwd == pwd2) {
		return true;
	} else {
		alert('Wrong confirmed password, please re-entry.');
		return false;
	}
}

var nRole = 0;

function removeDiv() {
	var divId = 'div' + this.id.substring(3);
	var div = document.getElementById(divId);
	
	var tableRole = document.getElementById('divRole');
	tableRole.removeChild(div);
}

function addNewRole(selectRole) {
	var div = document.createElement('div');
	div.setAttribute('id', 'div' + nRole);
	div.innerHTML = 'Role : &nbsp;&nbsp; ' + selectRole + ' &nbsp;&nbsp;&nbsp;&nbsp; ';
	
	// button
	var btnRemove = document.createElement('input');
	btnRemove.setAttribute('id', 'btn' + nRole);
	btnRemove.setAttribute('type', 'button');
	btnRemove.setAttribute('value', '-');
	addEvent(btnRemove, 'click', removeDiv);
	
	div.appendChild(btnRemove);
	
	var tableRole = document.getElementById('divRole');
	tableRole.appendChild(div);
	
	nRole++;
}
