// *** Text bei der Eingabeaufforderung ***********
linkname = "Geben Sie bitte den Linknamen an (optional).";
linkadresse = "Geben Sie bitte die vollst\u00E4ndige Adresse des Links an.";
emailname = "Geben Sie bitte die E-Mail-Adresse ein.";
listentyp = "F\u00FCr eine numerierte Liste geben Sie eine '1' an.\r\nF\u00FCr eine alphabetische ein 'a'.\r\nF\u00FCr eine einfache Punktliste dr\u00FCcken Sie OK.";
listenwerte = "Geben Sie bitte die Listenpunkte ein und dr\u00FCcken Sie anschliessend OK.\r\nAlternativ k\u00F6nnen Sie 'Abbrechen' w\u00E4hlen, um die Liste direkt fertigzustellen.";

//**** Pop-Fenster **********
function helpwindow(w,h,e) {
 window.open("include.php?path=popup&mode=help&explain="+e,"helpwindow","toolbar=no,scrollbars=yes,resizable=yes,status=no,width="+w+",height="+h);
 }
function smiliewindow(w,h) {
 window.open("include.php?path=popup&mode=smilies&window_w_size="+w+"&window_h_size="+h,"smilies","toolbar=no,scrollbars=yes,resizable=yes,status=no,width="+w+",height="+h);
 }
function finduserID(w,h) {
 window.open("include.php?path=popup&mode=finduser&window_w_size="+w+"&window_h_size="+h,"finduser","toolbar=no,scrollbars=yes,resizable=yes,status=no,width="+w+",height="+h);
 }
function checkall(status,theelement) {
 for (i=0;i<document.myform.length;i++) {if(document.myform.elements[i].name=="" + theelement + "[]") document.myform.elements[i].checked=status;}
 }

/*bbcode*/
var pkBBArea=null;
var pkBBSelected='';

function pkBBFocus() 
	{
	pkBBArea.focus();
	}

/*textselection*/
function pkBBSelection(obj) 
	{
	pkBBArea=obj;
	
	if(window.getSelection)
		pkBBSelected=pkBBArea.value.substring(pkBBArea.selectionStart,pkBBArea.selectionEnd);
	else if(document.getSelection)
		pkBBSelected=pkBBArea.value.substring(pkBBArea.selectionStart,pkBBArea.selectionEnd);
	else if(document.selection)
		pkBBSelected=document.selection.createRange().text;

	if(pkBBArea.createTextRange)
		pkBBArea.caretPos=document.selection.createRange().duplicate();
	
	return true;
	}
	
function pkBBSingle(text)
	{
	text=' '+text+' ';
	pkBBCodeAdd(text);
	}

function pkBBCodeAdd(text)
	{
	if(pkBBArea==null)
		{
		pkBBArea=document.getElementById('pkBBArea');
		pkBBFocus();
		pkBBSelection(pkBBArea);
		}

	if(window.getSelection)
		{
		pos=pkBBArea.selectionStart + text.length;
		scrollPos = pkBBArea.scrollTop;
		pkBBArea.value=pkBBArea.value.substr(0,pkBBArea.selectionStart) + text + pkBBArea.value.substr(pkBBArea.selectionEnd);
		pkBBArea.selectionStart=pos;
		pkBBArea.selectionEnd=pos;		
		}	
	else if(pkBBArea.createTextRange && pkBBArea.caretPos)
		{
		var caretPos = pkBBArea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1)==' ' ? text + ' ' : text;
		}
	else
		pkBBArea.value+=text
	
	pkBBFocus();
	pkBBArea.scrollTop = scrollPos;
	}

function pkBBCode(bbcode)
	{
	text=(pkBBSelected) ? pkBBSelected : '';
	text="["+bbcode+"]"+text+"[/"+bbcode+"]";
	
	pkBBCodeAdd(text);	
	pkBBFocus();
	}

/*link and email*/
function pkBBLink(bbcode)
	{
	text=(pkBBSelected) ? pkBBSelected : '';
	
	linktext = prompt(linkname,text);
	
	var fensterausgabe;
	if (bbcode == "URL") {
		ausgabe = linkadresse;
		ausgabeinhalt = "http://";
		}
	else {
		ausgabe = emailname;
		ausgabeinhalt = "";
		}
	
	linkurl = prompt(ausgabe,ausgabeinhalt);
	if ((linkurl != null) && (linkurl != "")) {
		if ((linktext != null) && (linktext != "")) {
			auswahltext = "["+bbcode+"="+linkurl+"]"+linktext+"[/"+bbcode+"] ";
			pkBBCodeAdd(auswahltext);
			
			}
		else{
			auswahltext = "["+bbcode+"]"+linkurl+"[/"+bbcode+"] ";
			pkBBCodeAdd(auswahltext);
			}
		}
	}

/* list */
function pkBBList()
	{
	listtype=prompt(listentyp,'');
	
	if((listtype == "a") || (listtype == "1"))
		{
		mylist = "[list="+listtype+"]\n";
		listend = "[/list="+listtype+"] ";
		}
	else
		{
		mylist = "[list]\n";
		listend = "[/list] ";
		}
	
	listentry="initial";
	while((listentry!="") && (listentry != null))
		{
		listentry=prompt(listenwerte,'');
		
		if((listentry!='') && (listentry != null))
			mylist = mylist+"[li]"+listentry+"[/li]\n";
		}
	
	pkBBCodeAdd(mylist+listend);
	}