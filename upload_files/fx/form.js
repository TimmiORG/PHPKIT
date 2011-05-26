function pkSetOption(elementid)
	{
	document.getElementById(elementid).checked=true;
	}
	
function pkCaptchaReload(elementid,src)
	{
	now = new Date();	
	o=document.getElementById(elementid);
	o.src=src + '&' + now.getTime();;
	}