/**************************************************************************************************
Live time Script- 
**************************************************************************************************/
function the_clock()
{
	var mydate	= new Date()
	var hours	= mydate.getHours()
	var minutes	= mydate.getMinutes()
	var seconds	= mydate.getSeconds()
	var dn		= "am"

	if (hours	>=	  12){ dn = "pm" }
	if (hours	>	  12){ hours = hours - 12 }
	if (hours	==	   0){ hours = 12 }
	if (minutes	<=	   9){ minutes = "0" + minutes }
	if (seconds	<=	   9){ seconds = "0" + seconds }
	//change font size here
	var time = " » " + hours + ":" + minutes + ":" + seconds + " " + dn;

	try {
		document.getElementById("the_clock").innerHTML = time;
	} catch(e) {}
}

function get_the_clock()
{
	if (document.getElementById) { setInterval("the_clock()",1000) };
}

/**
* Toggle the visibility of the quickreply box - START
**/
function quickreply_toggle()
{
	if (document.getElementById('quickreply'))
	{
		document.getElementById('quickreply').style.display = (document.getElementById('quickreply').style.display == 'none') ? 'block' : 'none';
	}
}
/** Toggle the visibility of the quickreply box - END **/

/**
* Resize the quickreply box - START
**/
function quickreply_resize(pix)
{
	if (document.forms[form_name].elements[text_name])
	{
		var box			= document.forms[form_name].elements[text_name];
		var new_height	= (parseInt(box.style.height) ? parseInt(box.style.height) : 300) + pix;

		if (typeof pix != 'number' || parseInt(pix) == 0)
		{
			new_height = parseInt(pix) + 'px';
		}

		if (parseInt(new_height) > 0)
		{
			box.style.height = parseInt(new_height) + 'px';
		}
	}
}
/** Resize the quickreply box - END **/
