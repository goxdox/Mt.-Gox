//this function includes all necessary js files for the application
function mtgox_include_js(file)
{

  var script  = document.createElement('script');
  script.src  = file;
  script.defer = true;

  document.getElementsByTagName('head').item(0).appendChild(script);
}

function mtgox_include_css(file)
{

  var script  = document.createElement('link');
  script.href  = file;
  script.rel = "stylesheet";
  script.type="text/css";

  document.getElementsByTagName('head').item(0).appendChild(script);
}


var mtgox_dialog_ele;
 function mtgox_pay(ele)
 {
	 mtgox_dialog_ele=ele;
	 
	 mtgox_include_js('http://mtgox.com/js/jquery.js');
	 mtgox_include_js('http://mtgox.com/js/jquery.ui.js');
	 mtgox_include_css('http://mtgox.com/css/jquery.ui.css');

	 //alert("ok");
	 mtgox_post();
 }
 
 
 function mtgox_post()
 {	 
	if(typeof jQuery == 'undefined') setTimeout(mtgox_post, 500);
	else
	{		
//		var html='<form id="mtgox_form" action="http://mtgox.com/code/gateway/startTxn.php" method="get" target="mtgox_frame">';
	//		html +='<input type="hidden" name="amount" value="'+mtgoxOptions.amount+'" />';
		//	html +='<input type="hidden" name="custom" value="'+mtgoxOptions.custom+'" />';
			//html +='<input type="hidden" name="merchID" value="'+mtgoxOptions.merchID+'" />';
			//html +='</form>';
		var src="http://mtgox.com/code/gateway/startTxn.php?amount="+mtgoxOptions.amount+"&custom="+mtgoxOptions.custom+"&merchID="+mtgoxOptions.merchID;
		var html = '<iframe name="mtgox_frame" src="'+src+'" width="100%" height="100%" style="border:0px;" />';
		var goxFrame=$("<div></div>").appendTo(mtgox_dialog_ele).html(html);
		
		
		goxFrame.dialog({
			height: 330,
			width: 400,
			title: 'Pay Using Bitcoins!'
		});
		
		//$('#mtgox_form').submit();
	}
 }
