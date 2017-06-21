String.prototype.capitalize = function() {
    return this.replace(/(?:^|\s)\S/g, function(a) { return a.toUpperCase(); });
};

var alerts = {

	alertsCount: 0,
	alertsContainer: "errors",

	createAlert: function (type, message, TTL)
	{
		HTML = `<div onClick="alerts.dismiss(this)" id="alert${this.alertsCount}" class="${type.toLowerCase()}"><strong>${type.capitalize()}: </strong>${message}<i class="material-icons">close</i></div>`;
		document.getElementById(this.alertsContainer).innerHTML += HTML;
		
		this.removeAlert(this.alertsCount, TTL);
		this.alertsCount += 1;
	},

	removeAlert: function(alertID, TTL)
	{
		var id = "alert" + alertID;
		setTimeout(function(alert){
			var alertElm = document.getElementById(alert);
			if (alertElm != null)
				alertElm.parentNode.removeChild(alertElm);
		}, TTL, id);
	},

	dismiss: function(alertElm)
	{
		if (alertElm != null)
			alertElm.parentNode.removeChild(alertElm);
	}
};

var shorten = {

	URLRegex: /([--:\w?@%&+~#=]*\.[a-z]{2,4}\/{0,2})((?:[?&](?:\w+)=(?:\w+))+|[--:\w?@%&+~#=]+)?/,
	autocheckTimeout: null,
	gracetime: 3,

	// Check if is a valid URL
	// If yes then returns true
	check: function(address)
	{
		return (this.URLRegex.test(address)) ? true: false;
	},

	inputUpdate: function(element)
	{
		// Check if element exists
		if (element == null)
			return;

		// If a timeout exists, remove it and set a new one
		// to display an error after the fracetime
		if ( this.autocheckTimeout != null )
		{
			clearTimeout(this.autocheckTimeout);
		};
		this.autocheckTimeout = setTimeout(function(x){
			if ( !x.check(element.value) )
			{
				alerts.createAlert("error", "The URL entered is not valid!", 7 * 1000);
			};
		}, this.gracetime * 1000, this);
	},

	shorten: function(url)
	{
		// Duplicate endpoint
		api.checkIfAlreadyExists(url, function(data){
			
			// Parse JSON
			var json = JSON.parse(data);

			// If the URL does not exist shorten it
			if (!json.Success)
				api.createNewLink(json.URL, function(data){
					// Parse JSON
					var json = JSON.parse(data);
					console.error(json);
					if( json.Success )
						alert("Successfull, Your short URL is: " + json.ShortLink);
					else
					{
						var c = confirm("That url has already been shortend, would you like to use the previously shortned link?");
						if (c)
							alert(json.ShortLink);
						else
							alert("Clear INput etc");
					};
				});
		
		});
	},

	keyHandler: function(element, e)
	{
		// If Enter check and submit
		if (e.keyCode == 13)
		{
			if( this.check(element.value) )
				this.shorten(element.value);
			else
				alerts.createAlert('error','That is an invalid URL!', 7 * 1000);
		};

		this.inputUpdate(this);
	}
};

var api = {

	baseURL: "api.php",
	createEndpoint: "?action=create&url=",
	duplicateEndpoint: "?action=duplicate&url=",

	checkIfAlreadyExists: function(url, anonFunc)
	{
		var finalURL = this.baseURL + this.duplicateEndpoint + url;
		AJAX.G(finalURL, anonFunc);
	},

	createNewLink: function(url, anonFunc)
	{
		var finalURL = this.baseURL + this.createEndpoint + url;
		AJAX.G(finalURL, anonFunc);
	}

};

var AJAX = {

	G: function(URL, anonFunc)
	{
		var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200)
            	anonFunc(this.responseText);
        };
        xmlhttp.open("GET", URL, true);
        xmlhttp.send();
	}

};