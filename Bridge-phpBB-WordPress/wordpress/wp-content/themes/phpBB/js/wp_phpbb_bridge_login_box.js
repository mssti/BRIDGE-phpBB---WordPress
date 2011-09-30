/**
 *
 * @package: phpBB 3.0.9 :: BRIDGE phpBB & WordPress -> WordPress root/wp-content/themes/phpBB/js
 * @version: $Id: wp_phpbb_bridge_login_box.js, v0.0.8 2011/08/25 11:08:25 leviatan21 Exp $
 * @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
 * @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
 * 
 */

var WPphpBBlogin = {
	init:function()
	{
		// Hide the loading image in the .modal-window at the main domument
		parent.$jQ_WPphpBB(".modal-window .modal-loading").hide();

		// Makes sure we always have a place to put messages
		if ($jQ_WPphpBB(".message").length == 0)
		{
			$jQ_WPphpBB("<p class=\"message\"></p>").insertAfter('#login h1');
		}

		// Makes sure we always close the modal windows
		$jQ_WPphpBB(".alignright input").click(function(){ WPphpBBlogin.close(home_url); });
		$jQ_WPphpBB("#backtoblog a").click(function(){ WPphpBBlogin.close(home_url); });

		// Check user login and password before submit
		$jQ_WPphpBB("#wp-submit").click(function(){ return WPphpBBlogin.formcheck(); });
	},
	formcheck:function()
	{
		$jQ_WPphpBB("#ajax-loading").fadeToggle("slow", "linear");

		var el_user_login = document.getElementById("user_login");
		if (el_user_login.value == '' || el_user_login.value == undefined || el_user_login.value == null)
		{
			$jQ_WPphpBB("#user_login").addClass("input-error");
			$jQ_WPphpBB("#user_login").css("position", "relative");
			var positions = new Array(15, 30, 15, 0, -15, -30, -15, 0);
			positions = positions.concat(positions.concat(positions));
			WPphpBBlogin.shakeit("#user_login", positions, 20);
			$jQ_WPphpBB("#ajax-loading").fadeToggle("slow", "linear");
			return false;
		}

		var el_user_pass = document.getElementById("user_pass");
		if (el_user_pass.value == '' || el_user_pass.value == undefined || el_user_pass.value == null)
		{
			$jQ_WPphpBB("#user_pass").addClass("input-error");
			$jQ_WPphpBB("#user_pass").css("position", "relative");
			var positions = new Array(15, 30, 15, 0, -15, -30, -15, 0);
			positions = positions.concat(positions.concat(positions));
			WPphpBBlogin.shakeit("#user_pass", positions, 20);
			$jQ_WPphpBB("#ajax-loading").fadeToggle("slow", "linear");
			return false;
		}
	},
	shakeit:function(element_id, positions, duration)
	{
		var c = positions.shift();

		$jQ_WPphpBB(element_id).css("left", c + "px");

		if (positions.length > 0)
		{
			window.setTimeout( function() { WPphpBBlogin.shakeit(element_id, positions, duration); }, duration);
		}
		else
		{
			try
			{
				$jQ_WPphpBB(element_id).css("position", "static");
				$jQ_WPphpBB(element_id).removeClass("input-error");
				$jQ_WPphpBB(element_id).focus();
			}catch(e){}
		}
	},
	// Send function
	add:function(user_data, ajax_url, refresh_url)
	{
		// AJAX function
		$jQ_WPphpBB.ajax({
			timeout:10000,
			dataType:'text',
			beforeSend:function()
			{
			},
			success:function(msg)
			{
				// successful request; do something with the data
				$jQ_WPphpBB(".message").html(msg);
			},
			complete:function()
			{
			},
			error:function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert("Error=(" + XMLHttpRequest.status + ") textStatus=(" + textStatus + ") errorThrown=(" + errorThrown + ")");
			/**
				if (XMLHttpRequest.status==400)
				{
					// Flood alert
					alert("error : 400");
				}
				else if(XMLHttpRequest.status==403)
				{
					// No access alert
					alert("error : 403");
				}
				else if(XMLHttpRequest.status==501)
				{
					// No message alert
					alert("error : 501");
				}
				else
				{
					// No message alert
					alert("Error" + XMLHttpRequest.status);
				}
			**/
			}
		});
	},
	close:function(refresh_url)
	{
		if (refresh_url)
		{
			parent.location.href = refresh_url.replace(/&amp;/g, '&');
		}
		parent.modalWindow.close();
	},
	error:function(msg)
	{
		$jQ_WPphpBB(".message").html(msg);
	}
};

$jQ_WPphpBB(document).ready(function() { WPphpBBlogin.init(); });
