function pkVersioncheck() 
	{
	if(top == self)
		return;
		
	if(latestversion!= null && latestversion!='undefined' && version != latestversion)
		{
		var thisversion = version.substring(0,1) + version.substring(2,3) + version.substring(4,5);
		var newversion = latestversion.substring(0,1) + latestversion.substring(2,3) + latestversion.substring(4,5);		
	
		if(newversion>=thisversion)
			{
			window.top.frames[0].location.href=hiddenurl+'&versionchecked='+latestversion;
			return;
			}
		}
	}