function containerEntity(el) {
	if (el.parentNode.classList.contains("container")) return el;
    return containerEntity(el.parentNode);
}

function prepareCall(statement) {
	return eval(statement);
}

function listenCheckables(el) {
	el.querySelectorAll(".checkable").forEach(function(el) {
		el.addEventListener(el.getAttribute("data-event"), checkAndUpdate);
	});
}

function insertTemplate(el) {
	let node = document.getElementById(el.getAttribute("data-template")).content.firstElementChild.cloneNode(true);
	el.appendChild(node);
	
	return node;
}

function checkAndInsertTemplate(el, auto) {
	if (el.childElementCount) {
		let last = el.lastElementChild;
		let check = prepareCall.call(last, last.getAttribute("data-check"));
		if (check && !prepareCall.call(check, check.getAttribute("data-check"))) return;
	} else {
		if (auto && !document.getElementById(el.getAttribute("data-template")).content.firstElementChild.getAttribute("data-check")) return;
	}
	
	if (el.childElementCount < parseInt(el.getAttribute("data-max"))) {
		let added = insertTemplate(el);
		prepareContainers(added.querySelectorAll(".container"));
		return added;
	}
}

function checkAndUpdate(event) {
	let container = containerEntity(event.target).parentNode;
	
	if (!prepareCall.call(event.target, event.target.getAttribute("data-check"))) {
		prepareCall.call(containerEntity(event.target), containerEntity(event.target).getAttribute("data-remove"));
		containerEntity(event.target).remove();
	}
	
	let added = checkAndInsertTemplate(container, true);
	
	if (added) {
		prepareCall.call(added, added.getAttribute("data-add"));
		listenCheckables(added);
	}
}

function prepareContainers(els) {
	els.forEach(function(el) {
		let added = checkAndInsertTemplate(el, true);
		
		if (added) {
			listenCheckables(added);
		}
	});
}

function prepareContainersLoad() {
	prepareContainers(document.querySelectorAll(".container"));
}

window.addEventListener("load", prepareContainersLoad, {once: true});