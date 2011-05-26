var pkWindowName = window.name;
var pkWindowOpener = top.opener;
var pkFormElement = null;

function pkWindowOpen(url,wname,params)
	{
	win=params ? window.open(url,wname,params) : window.open(url,wname);
	win.focus();	
	}
	
function pkSelectAll(theform, theselect, docheck) 
	{
	var selectObject=document.forms[theform].elements[theselect];
	var selectCount=selectObject.length;
	for(var i = 0; i < selectCount; i++)
		{
		selectObject.options[i].selected = docheck;
		}
	
	return true;
	}

function pkColorShow(ename)
	{
	var color=document.getElementById(ename).value;

	color = color=='' || color=='undefined' ? 'transparent' : '#'+color;
	document.getElementById('show'+ename).style.backgroundColor = color;
	}

function pkUnselect(form)
	{
	selection = form.selectedIndex;
	
	if(selection != -1)
		{
		form.options[selection] = null;
		pkUnselect(form);
		}
	}
	
function pkSetFormElement(form)
	{
	pkFormElement = form;
	}
	
function pkSelectionCheck(thisForm) {
	if(thisForm.selectedIndex != -1 && thisForm.options[thisForm.selectedIndex].value != -1)
		{
		return true;
		}
	else
		{
		return false;
		}
	}
	
	

function pkSelectionToOpener(thisForm) {
	selection = thisForm.selectedIndex;
	if(selection != -1)
		{	
		pkWindowOpener.pkInsertSelectionFromDependant(thisForm.options[selection].text,thisForm.options[selection].value);
		
		thisForm.options[selection].selected = false;
		pkSelectionToOpener(thisForm);
		
		return true;
		}
	else
		{
		return false;
		}
	}
	
function pkInsertSelectionFromDependant(text,value) {

	for(i=0;i<pkFormElement.length;i++)
		{
		if(pkFormElement.options[i].value == value)
			{
			return;
			}
		}
	
	pkFormElement.options[pkFormElement.length] = new Option(text,value,false,true);
	return;
	}