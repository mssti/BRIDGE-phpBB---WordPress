/**************************************************************************************************
Live time Script- 
**************************************************************************************************/
function the_clock()
{
	var the_clock_datenow	= new Date()
	var the_clock_hours		= the_clock_datenow.getHours()
	var the_clock_minutes	= the_clock_datenow.getMinutes()
	var the_clock_seconds	= the_clock_datenow.getSeconds()
	var the_clock_ampm		= "am"

	if (the_clock_hours		>=	12){ the_clock_ampm = "pm" }
	if (the_clock_hours		>	12){ the_clock_hours = the_clock_hours - 12 }
	if (the_clock_hours		==	 0){ the_clock_hours = 12 }
	if (the_clock_minutes	<=	 9){ the_clock_minutes = "0" + the_clock_minutes }
	if (the_clock_seconds	<=	 9){ the_clock_seconds = "0" + the_clock_seconds }

	var time = " " + the_clock_hours + ":" + the_clock_minutes + ":" + the_clock_seconds + " " + the_clock_ampm;

	try {
		document.getElementById("the_clock").innerHTML = time;
	} catch(e) {}
}

function get_the_clock()
{
	if (document.getElementById) { setInterval("the_clock()", 1000) };
}

var $jQ_WPphpBB=jQuery;

/**
* A simple iFrame modal window, leveraging the jQuery framework
*	Code from : http://deseloper.org/read/2008/04/a-simple-modal/
**/
var modalWindow = {  
	parent:"body",
	windowId:"modal-window",
	content:null,
	width:null,
	height:null,
	close:function()
	{
		setTimeout(function() { $jQ_WPphpBB(".modal-window").remove(); }, 0);
		setTimeout(function() { $jQ_WPphpBB(".modal-overlay").remove(); location.href = location.href}, 0);
	},
	open:function(mode)
	{
		mode = (!mode) ? "login" : mode;
		var modal = "";
		modal += "<div class=\"modal-overlay\"></div>";
		modal += "<div id=\"" + this.windowId + "\" class=\"modal-window\" style=\"width:" + this.width + "px; height:" + this.height + "px; margin-top:-" + (this.height / 2) + "px; margin-left:-" + (this.width / 2) + "px;\">";
		modal += this.content;
		modal += "</div>";
		
		$jQ_WPphpBB(this.parent).append(modal);
		$jQ_WPphpBB(".modal-overlay").css({opacity: 0}).animate({opacity: 0.75});
		$jQ_WPphpBB(".modal-window").hide().fadeIn();
		$jQ_WPphpBB(".modal-window").append("<div id=\"close-window\"><a class=\"close-window\">X</a></div>");
		$jQ_WPphpBB(".modal-window").append("<span class=\"copyright\">Bridge by <a href=\"http://www.mssti.com/phpbb3\" title=\"Micro Software &amp; Servicio Técnico Informático\" onclick=\"window.open(this.href);return false;\">.:: MSSTI ::.</a></span>");
		$jQ_WPphpBB(".close-window").click(function(){modalWindow.close();});
		$jQ_WPphpBB(".modal-overlay").click(function(){modalWindow.close();});

		if (mode == "login" || mode == "logout" || mode == "register")
		{
			$jQ_WPphpBB("#modal-iframe").load(function(){
				$jQ_WPphpBB("#modal-iframe").contents().find("head").append('<link rel="stylesheet" type="text/css" media="all" href="' + base_url + '/css/login.css?ver=0.0.8" />');
				$jQ_WPphpBB("#modal-iframe").contents().find("body#error-page").css("width", "80%");

				$jQ_WPphpBB(".modal-window .modal-loading").hide();
			});
		}
	}
};
var stylesheet_directory = '';
var openMyModal = function(mode, source, popup_width, popup_height, popup_name)
{
	mode = (!mode) ? "login" : mode;
	if (!popup_width)
	{
		popup_width = 450;
	}
	if (!popup_height)
	{
		popup_height = 520;
	}
	if (!popup_name)
	{
		popup_name = "wp_login";
	}

	modalWindow.windowId = popup_name;
	modalWindow.width = popup_width + 20;
	modalWindow.height = popup_height + 20;
	modalWindow.content = "<img class=\"modal-loading\" src=\"" + base_url + "/images/loading.gif\" alt=\"\" /> <iframe id=\"modal-iframe\" name=\"modal-iframe\" class=\"modal-iframe\"width=\"100%\" height=\"99.9%\" frameborder=\"0\" scrolling=\"no\" allowtransparency=\"true\" src=\"" + source + "\"></iframe>";
	modalWindow.open(mode);
};
