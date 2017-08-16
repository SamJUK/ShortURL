/**
 * Add's a capitalisation method to string
 * Usage: "Hello".capitalize();
 */
String.prototype.capitalize = function() {
    return this.replace(/(?:^|\s)\S/g, function(a) { return a.toUpperCase(); });
};

var alerts = {
	// How many alerts have been created
	alertsCount: 0,

	// DOM Container for alerts messages
	alertsContainer: "errors",

	/**
	 * Method to create alert messages
	 * 
	 * @param String | type    | What type of message it is (error, warning, success, etc)
	 * @param String | message | What alert message to display
	 * @param Int    | TTL     | Time for the alert to live for before dissappearing (MS - E.G 1000 = 1 second)
	 */
	createAlert: function (type, message, TTL)
	{
		HTML = `<div onClick="alerts.dismiss(this)" id="alert${this.alertsCount}" class="${type.toLowerCase()}"><strong>${type.capitalize()}: </strong>${message}<i class="material-icons">close</i></div>`;
		document.getElementById(this.alertsContainer).innerHTML += HTML;
		
		this.removeAlert(this.alertsCount, TTL);
		this.alertsCount += 1;
	},

	/**
	 * Method to remove an alert
	 * 
	 * @param String | alertID | What alertID to delete (Stored as the elementsID - document.getElementID('alert2') etc) 
	 * @param Int    | TTL     | Time for the alert to live for before dissappearing (MS - E.G 1000 = 1 second) 
	 */
	removeAlert: function(alertID, TTL)
	{
		var id = "alert" + alertID;
		setTimeout(function(alert){
			var alertElm = document.getElementById(alert);
			this.dismiss(alertElm);
		}, TTL, id);
	},

	/**
	 * Method to remove the alert
	 * 
	 * @param DOM Element | alertElm | Element to delete (E.G document.getElementByID('alert2') )
	 */
	dismiss: function(alertElm)
	{
		if (alertElm != null)
			alertElm.parentNode.removeChild(alertElm);
	}
};

var shorten = {

	// Regex to check it the URL is valid
	URLRegex: /([--:\w?@%&+~#=]*\.[a-z]{2,4}\/{0,2})((?:[?&](?:\w+)=(?:\w+))+|[--:\w?@%&+~#=]+)?/,
	
	// Variable to store the setTimeout to wait the grace time then check for validity
	autocheckTimeout: null,

	// Time to wait before last input to check if its the input is valid
	gracetime: 3,

	/**
	 * Method to check if the input is a valid address
	 *
	 * @param String - address
	 * @return Boolean 
	 *		true =  valid address
	 * 		false = invalid address
	 */
	check: function(address)
	{
		return this.URLRegex.test(address);
	},

	/**
	 * Method to automatically validate the URL entered
	 *
	 * @param DOM Element
	 */
	inputUpdate: function(element)
	{
		// If element doesn't exists return early
		if (element == null)
			return;

		// If a timeout exists, clear it
		if ( this.autocheckTimeout != null )
		{
			clearTimeout(this.autocheckTimeout);
		};

		// Add another timout to check if the input is invalid and then display an alert after the gracetime
		this.autocheckTimeout = setTimeout(function(x){
			if ( !x.check(element.value) )
			{
				alerts.createAlert("error", "The URL entered is not valid!", 7 * 1000);
			};
		}, this.gracetime * 1000, this);
	},

	/**
	 * Method to perform the actual shortening 
	 * 
	 * @param url - url to shorten
	 */
	shorten: function(url)
	{
		// Check if the url has already been shortend
		api.checkIfAlreadyExists(url, data => {
			
			console.log(data);

			// Parse JSON response
			var json = JSON.parse(data);

			// If the URL does not exist shorten it
			if (!json.Success)
				api.createNewLink(json.URL, function(data){
					// Parse JSON response
					var json = JSON.parse(data);

					// Redirect it to the link display page
					window.location = `link.php?su=${json.ShortLink}`;
				});
		
		});
	},

	/**
	 * Keyhandler method for the URL input field
	 *
	 * @param DOM Element - to verify
	 * @param Object - Event Object
	 */
	keyHandler: function(element, e)
	{
		// Check if the enter key was pressed
		if (e.keyCode == 13)
		{
			// If the URL is valid shorten it. Otherwise display an error
			if( this.check(element.value) )
				this.shorten(element.value);
			else
				alerts.createAlert('error','That is an invalid URL!', 7 * 1000);
		};

		// Validate the URL
		this.inputUpdate(this);
	}
};

var api = {

	// URL to the base API page
	baseURL: "app/api.php",

	// Enpoint for creating a new short URL
	createEndpoint: "?action=create&url=",

	// Endpoint for checking if the full url has been shortend already
	duplicateEndpoint: "?action=duplicate&url=",

	/**
	 * Method to check if the URL has already been shortend
	 *
	 * @param String   - URL to check if it has already been shortend
	 * @param Function - Function to run on response
	 */
	checkIfAlreadyExists: function(url, anonFunc)
	{
		var finalURL = this.baseURL + this.duplicateEndpoint + url;
		AJAX.G(finalURL, anonFunc);
	},

	/**
	 * Method to create a new short URL
	 *
	 * @param String   - URL to shorten
	 * @param Function - Function to run on response
	 */
	createNewLink: function(url, anonFunc)
	{
		var finalURL = this.baseURL + this.createEndpoint + url;
		AJAX.G(finalURL, anonFunc);
	}

};

var AJAX = {

	/**
	 * Perform a AJAX get request
	 * 
	 * @param String   - URL to request
	 * @param Function - Function to run on response
	 */
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