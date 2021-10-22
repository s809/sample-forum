var theme;

function loadTheme() {
	document.cookie.split("; ").forEach(function(cookie) {
		cookie = cookie.split("=");
		if (cookie[0] == "theme") {
			theme = JSON.parse(cookie[1]);
		}
	});
	
	let settings = {
		bg_top: document.getElementById("bg_top"),
		bg_bottom: document.getElementById("bg_bottom")
	};
	
	for (key in settings) {
		if (settings[key]) {
			if (theme && theme[key]) {
				settings[key].value = "#" + theme[key];
			}
		}
	}
	
	applyTheme();
}

function updateTheme(key, value) {
	if (!theme) {
		theme = new Object();
	}
	theme[key] = value.replace("#", "");
	document.cookie = "theme=" + JSON.stringify(theme) + "; expires=Fri, 31 Dec 9999 23:59:59 GMT";
	applyTheme();
}

function applyTheme() {
	let templates = {
		bg: "background-image: linear-gradient(#{0}, #{1});"
	};
	
	for (key in theme) {
		switch (key) {
			case "bg_top":
			case "bg_bottom":
				if (!theme.bg_top) theme.bg_top = "ffffff";
				if (!theme.bg_bottom) theme.bg_bottom = "ffffff";
				document.querySelector("main").style = templates.bg.replace("{0}", theme.bg_top).replace("{1}", theme.bg_bottom);
		}
	}
}

window.addEventListener("load", loadTheme, {once: true});