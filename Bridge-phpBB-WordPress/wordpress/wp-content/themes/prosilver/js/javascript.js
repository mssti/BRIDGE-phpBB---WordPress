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
