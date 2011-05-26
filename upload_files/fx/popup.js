function pkWinResize(widthsize,heightsize)
	{
	if(widthsize==0 || heightsize==0)
		return;
		
	widthsize2=screen.width;
	heightsize2=screen.height;
	
	if(widthsize>=widthsize2) 
		{widthsize=widthsize;}
	if(heightsize>=heightsize2)
		{heightsize=((heightsize2/100)*85);}
	
	window.resizeTo(widthsize,heightsize);
	}

function pkWinOnTop()
	{
	self.focus();
	}
	
function submitText()
	{
	document.preview.submit();
	}

function getText()
	{
	document.preview.previewtext.value = opener.document.myform.content.value;
	}

function selectUser()
	{
	if(document.finduser.User.options[document.finduser.User.selectedIndex].value != -1)
		{
		opener.document.myform.im_receiver.value = document.finduser.User.options[document.finduser.User.selectedIndex].value;
		opener.document.myform.im_receiver.focus();
		}
	}

function selectBuddy()
	{
	if(document.finduser.Buddy.options[document.finduser.Buddy.selectedIndex].value != -1)
		{
		opener.document.myform.im_receiver.value = document.finduser.Buddy.options[document.finduser.Buddy.selectedIndex].value;
		opener.document.myform.im_receiver.focus();
		}
	}

function singler(text)
	{
	var txtarea = opener.document.myform.content;
	var caretPos = txtarea.caretPos;
	
	text=' '+text+' ';
	
	if(txtarea.createTextRange && txtarea.caretPos)
		{
		var caretPos=txtarea.caretPos;
		caretPos.text=caretPos.text.charAt(caretPos.text.length - 1)==' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();
		}
	else
		{
		txtarea.value += text;
		txtarea.focus();
		}
	}
	
function returnLink(dat,size)
	{
	var txtarea = opener.document.myform.cont_altdat;
	var caretPos = txtarea.caretPos;
	
	text=(txtarea.value!='' ? '\n' : '');
	text=text+dat;
	
	if(txtarea.createTextRange && txtarea.caretPos)
		{
		var caretPos = txtarea.caretPos;

		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1)==' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();
		}
	else
		{
		txtarea.value += text;
		txtarea.focus();
		}	
	
	
	opener.document.myform.cont_filesize.value = size;	
	}