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
	//change font size here
	var time = " " + the_clock_hours + ":" + the_clock_minutes + ":" + the_clock_seconds + " " + the_clock_ampm;

	try {
		document.getElementById("the_clock").innerHTML = time;
	} catch(e) {}
}

function get_the_clock()
{
	if (document.getElementById) { setInterval("the_clock()", 1000) };
}
