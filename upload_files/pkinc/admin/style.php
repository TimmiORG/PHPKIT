<?php
# PHPKIT WCMS | Web Content Management System
#
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTMÄSSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN
#
# This file / the PHPKIT software is no freeware! For further 
# information please visit our website or contact us via email:
#
# Diese Datei / die PHPKIT Software ist keine Freeware! Für weitere
# Informationen besuchen Sie bitte unsere Website oder kontaktieren uns per E-Mail:
#
# email     : info@phpkit.com
# website   : http://www.phpkit.com
# licence   : http://www.phpkit.com/licence
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


if(!defined('pkFRONTEND') || pkFRONTEND!='admin')
	die('Direct access to this location is not permitted.');


if(!adminaccess('style'))
	return pkEvent('access_forbidden');


$ACTION = isset($_POST['action']) ? $_POST['action'] : 'view';
$styleid = (isset($_REQUEST['styleid']) && intval($_REQUEST['styleid'])>0) ? intval($_REQUEST['styleid']) :
	(isset($_REQUEST['styleid']) && $_REQUEST['styleid']=='new' ? 'new' : 0);


$form_action = pkLink('style');

$getstyles = $SQL->query("SELECT style_id, style_name, style_user, style_template FROM ".pkSQLTAB_STYLE." ORDER BY style_name ASC");
while($styles = $SQL->fetch_assoc($getstyles))
	{
	$style_hash[$styles['style_id']] = $styles;
	}


#redirect
if($ENV->_post_action('cancel'))
	{
	pkHeaderLocation('style');
	}


#redirect to edit
if($ENV->_post_action('edit') && $styleid>0)
	{
	pkHeaderLocation('style','','styleid='.$styleid);
	}


#redirect after current set
if($ENV->_post_action('enable') && $styleid>0)
	{
	if(isset($style_hash[$styleid]))#check if the style exists
		{
		$SQL->query("REPLACE INTO ".pkSQLTAB_CONFIG." (id,value) VALUES ('site_style','".serialize($styleid)."')");
		}
	
	pkHeaderLocation('style');
	}


#import
if($ACTION==$_POST['import'])
	{
	if($_POST['doimport']==1)
		{
		unset($filepath);
		
		if(is_uploaded_file($_FILES['import_file']['tmp_name']))
			{
#			move_uploaded_file($_FILES['import_file']['tmp_name'], "../tmp/".strrchr($_FILES['import_file']['tmp_name'], "/"));
#			$_FILES['import_file']['tmp_name'] = "../tmp/".strrchr($_FILES['import_file']['tmp_name'], "/");
			
			$filepath=$_FILES['import_file']['tmp_name'];
			$filename=$_FILES['import_file']['name'];
			}
		elseif(filecheck('../'.$_POST['import_sfile'])) 
			{
			$filepath='../'.$_POST['import_sfile'];
			$filename=basename('../'.$_POST['import_sfile']);
			}
		else
			unset($filepath);
		
		
		if($filepath && substr($filepath,-3)=='pks')
			return pkEvent('style_old_file','warning');
		
		if($filepath)
			{
			pkLoadFunc('file');
			pkLoadClass($STYLE,'style');
			
			$styledump=file($filepath);
			$styleid=$STYLE->import($styledump);

			unlink($_FILES['import_file']['tmp_name']);	

			pkHeaderLocation('style','','styleid='.$styleid);
			}
		}
	else
		{
		unset($import_message);
		}


	pkEvent('thirdparty_warning','warning');
	
	eval("\$site_body.=\"".pkTpl("style_import")."\";");
	}
elseif($ACTION==$_POST['export'] && $styleid && $styleid!='new')
	{
	pkLoadClass($STYLE,'style');
	
	$STYLE->export($styleid);
	exit();
	}
elseif($ACTION==$_POST['delete'] && $styleid==$config['site_style'])
	{
	eval("\$site_body= \"".pkTpl("style_change")."\";");
	}
elseif($ACTION==$_POST['delete'] && count($style_hash)>1 && $styleid!=$config['site_style'] && $styleid>0)
	{
	if($_POST['confirmed']=='true' && $ACTION==$_POST['delete'])
		{
		$SQL->query("DELETE FROM ".pkSQLTAB_STYLE." WHERE style_id='".$styleid."' LIMIT 1");
		
		pkHeaderLocation('style');
		}
	else
		{
		$thisstylename = pkEntities($style_hash[$styleid]['style_name']);
		
		eval("\$site_body.= \"".pkTpl("style_delete")."\";");
		}
	}
elseif($ACTION==$_POST['save'] && $styleid)
	{
	if($styleid=='new' || (isset($_POST['style_copy']) && $_POST['style_copy']==1))
		{
		$SQL->query("INSERT INTO ".pkSQLTAB_STYLE." (style_name) VALUES ('new')");
		$styleid = $SQL->insert_id();
		}

	
	#verify the brandinglogo size
	$brandingimagewidth = 0;
	$brandingimageheight = 0;

	$dir = $_POST['style_images'].'/';

	$subdir = dirname($_POST['brandingimage']);
	$subdir = empty($subdir) || $subdir=='.' ? '' : $subdir.'/';	
	$logo = basename($_POST['brandingimage']);
	$brandinglogopath = str_replace('//','/',pkDIRROOT.$dir.$subdir.$logo);
	
	if(pkFileCheck($brandinglogopath))
		{
		list($brandingimagewidth,$brandingimageheight)=@getimagesize($brandinglogopath);
		}

	unset($dir);
	
	$SQL->query("UPDATE ".pkSQLTAB_STYLE." SET 
		style_name='".$SQL->f($_POST['style_name'])."',
		style_template='".$SQL->f($_POST['style_template'])."',
		style_css='".$SQL->f($_POST['style_css'])."',
		style_addcss='".$SQL->f($_POST['style_addcss'])."',
		style_user='".$SQL->b($_POST['style_user'])."',
		style_images='".$SQL->f($_POST['style_images'])."',
		style_width='".$SQL->f($_POST['style_width'])."',
		style_align='".$SQL->f($_POST['style_align'])."',

		site_date='".$SQL->b($_POST['site_date'])."',
		site_parsertime='".$SQL->b($_POST['site_parsertime'])."',
		
		bodybgcolor='".$SQL->f($_POST['bodybgcolor'])."',
		bodybgimage='".$SQL->f($_POST['bodybgimage'])."',
		bodybgimagerepeat='".$SQL->f($_POST['bodybgimagerepeat'])."',
		bodyfont='".$SQL->f($_POST['bodyfont'])."',
		bodyfontcolor='".$SQL->f($_POST['bodyfontcolor'])."',
		bodymargintop='".$SQL->f($_POST['bodymargintop'])."',
		bodymarginleft='".$SQL->f($_POST['bodymarginleft'])."',		
		bodymarginright='".$SQL->f($_POST['bodymarginright'])."',
		bodymarginbottom='".$SQL->f($_POST['bodymarginbottom'])."',				
		
		scrollbarcolor='".$SQL->f($_POST['scrollbarcolor'])."',
		hrcolor='".$SQL->f($_POST['hrcolor'])."',
		fontsizebig='".$SQL->f($_POST['fontsizebig'])."',
		fontsize='".$SQL->f($_POST['fontsize'])."',
		fontsizesmall='".$SQL->f($_POST['fontsizesmall'])."',

		brandingheight='".$SQL->i($_POST['brandingheight'])."',
		brandingimage='".$SQL->f($_POST['brandingimage'])."',
		brandingimageposition='".$SQL->f($_POST['brandingimageposition'])."',
		brandingimagepositiontop='".$SQL->i($_POST['brandingimagepositiontop'])."',
		brandingimagepositionleft='".$SQL->i($_POST['brandingimagepositionleft'])."',
		brandingimagewidth='".$SQL->i($brandingimagewidth)."',
		brandingimageheight='".$SQL->i($brandingimageheight)."',
		brandingbgcolor='".$SQL->f($_POST['brandingbgcolor'])."',
		brandingbgimage='".$SQL->f($_POST['brandingbgimage'])."',
		brandingbgimagerepeat='".$SQL->f($_POST['brandingbgimagerepeat'])."',
		adviewbgcolor='".$SQL->f($_POST['adviewbgcolor'])."',

		pagedecorationwidth='".$SQL->f($_POST['pagedecorationwidth'])."',
		pagedecorationbgcolor='".$SQL->f($_POST['pagedecorationbgcolor'])."',
		pagedecorationbgimage='".$SQL->f($_POST['pagedecorationbgimage'])."',		
		pageheaderheight='".$SQL->f($_POST['pageheaderheight'])."',
		pageheaderbgcolor='".$SQL->f($_POST['pageheaderbgcolor'])."',
		pageheaderbgimage='".$SQL->f($_POST['pageheaderbgimage'])."',
		pagefooterheight='".$SQL->f($_POST['pagefooterheight'])."',
		pagefooterbgcolor='".$SQL->f($_POST['pagefooterbgcolor'])."',
		pagefooterbgimage='".$SQL->f($_POST['pagefooterbgimage'])."',
		
		contenttophlheight='".$SQL->f($_POST['contenttophlheight'])."',
		contenttophlpadding='".$SQL->f($_POST['contenttophlpadding'])."',
		contenttophlspacing='".$SQL->f($_POST['contenttophlspacing'])."',
		contenttophlalign='".$SQL->f($_POST['contenttophlalign'])."',
		contenttophlbgcolor='".$SQL->f($_POST['contenttophlbgcolor'])."',
		contenttophlbgimage='".$SQL->f($_POST['contenttophlbgimage'])."',		
		contenttophlfont='".$SQL->f($_POST['contenttophlfont'])."',
		contenttophlfontsize='".$SQL->f($_POST['contenttophlfontsize'])."',
		contenttophlcolor='".$SQL->f($_POST['contenttophlcolor'])."',
		contenttophlcolorhover='".$SQL->f($_POST['contenttophlcolorhover'])."',
				
		contenttopheight='".$SQL->f($_POST['contenttopheight'])."',
		contenttoppadding='".$SQL->f($_POST['contenttoppadding'])."',
		contenttopspacing='".$SQL->f($_POST['contenttopspacing'])."',
		contenttopalign='".$SQL->f($_POST['contenttopalign'])."',
		contenttopbgcolor='".$SQL->f($_POST['contenttopbgcolor'])."',
		contenttopbgimage='".$SQL->f($_POST['contenttopbgimage'])."',
		contenttopfont='".$SQL->f($_POST['contenttopfont'])."',
		contenttopfontsize='".$SQL->f($_POST['contenttopfontsize'])."',
		contenttopcolor='".$SQL->f($_POST['contenttopcolor'])."',
		contenttopcolorlink='".$SQL->f($_POST['contenttopcolorlink'])."',
		contenttopcolorhover='".$SQL->f($_POST['contenttopcolorhover'])."',
		
		contentbottomhlheight='".$SQL->f($_POST['contentbottomhlheight'])."',
		contentbottomhlpadding='".$SQL->f($_POST['contentbottomhlpadding'])."',
		contentbottomhlspacing='".$SQL->f($_POST['contentbottomhlspacing'])."',
		contentbottomhlalign='".$SQL->f($_POST['contentbottomhlalign'])."',
		contentbottomhlbgcolor='".$SQL->f($_POST['contentbottomhlbgcolor'])."',
		contentbottomhlbgimage='".$SQL->f($_POST['contentbottomhlbgimage'])."',		
		contentbottomhlfont='".$SQL->f($_POST['contentbottomhlfont'])."',
		contentbottomhlfontsize='".$SQL->f($_POST['contentbottomhlfontsize'])."',
		contentbottomhlcolor='".$SQL->f($_POST['contentbottomhlcolor'])."',
		contentbottomhlcolorhover='".$SQL->f($_POST['contentbottomhlcolorhover'])."',
				
		contentbottomheight='".$SQL->f($_POST['contentbottomheight'])."',
		contentbottompadding='".$SQL->f($_POST['contentbottompadding'])."',
		contentbottomspacing='".$SQL->f($_POST['contentbottomspacing'])."',
		contentbottomalign='".$SQL->f($_POST['contentbottomalign'])."',
		contentbottombgcolor='".$SQL->f($_POST['contentbottombgcolor'])."',
		contentbottombgimage='".$SQL->f($_POST['contentbottombgimage'])."',
		contentbottomfont='".$SQL->f($_POST['contentbottomfont'])."',
		contentbottomfontsize='".$SQL->f($_POST['contentbottomfontsize'])."',
		contentbottomcolor='".$SQL->f($_POST['contentbottomcolor'])."',
		contentbottomcolorlink='".$SQL->f($_POST['contentbottomcolorlink'])."',
		contentbottomcolorhover='".$SQL->f($_POST['contentbottomcolorhover'])."',

		contentleftwidth='".$SQL->f($_POST['contentleftwidth'])."',
		contentleftbgcolor='".$SQL->f($_POST['contentleftbgcolor'])."',
		contentleftbgimage='".$SQL->f($_POST['contentleftbgimage'])."',
		contentleftbgimagerepeat='".$SQL->f($_POST['contentleftbgimagerepeat'])."',
		contentleftpadding='".$SQL->f($_POST['contentleftpadding'])."',
		contentlefthlindent='".$SQL->f($_POST['contentlefthlindent'])."',
		contentlefthlalign='".$SQL->f($_POST['contentlefthlalign'])."',
		contentlefthlfont='".$SQL->f($_POST['contentlefthlfont'])."',
		contentlefthlfontsize='".$SQL->f($_POST['contentlefthlfontsize'])."',
		contentlefthlcolor='".$SQL->f($_POST['contentlefthlcolor'])."',
		contentlefthlcolorhover='".$SQL->f($_POST['contentlefthlcolorhover'])."',
		contentlefthlbgcolor='".$SQL->f($_POST['contentlefthlbgcolor'])."',
		contentlefthlbgimage='".$SQL->f($_POST['contentlefthlbgimage'])."',
		contentleftindent='".$SQL->f($_POST['contentleftindent'])."',
		contentleftalign='".$SQL->f($_POST['contentleftalign'])."',
		contentleftfont='".$SQL->f($_POST['contentleftfont'])."',
		contentleftfontsize='".$SQL->f($_POST['contentleftfontsize'])."',
		contentleftcolor='".$SQL->f($_POST['contentleftcolor'])."',
		contentleftcolorlink='".$SQL->f($_POST['contentleftcolorlink'])."',
		contentleftcolorhover='".$SQL->f($_POST['contentleftcolorhover'])."',
		contentleftcbgcolor='".$SQL->f($_POST['contentleftcbgcolor'])."',
		contentleftcbgimage='".$SQL->f($_POST['contentleftcbgimage'])."',
		contentleftboxspacer='".$SQL->i($_POST['contentleftboxspacer'])."',

		contentrightwidth='".$SQL->f($_POST['contentrightwidth'])."',
		contentrightbgcolor='".$SQL->f($_POST['contentrightbgcolor'])."',
		contentrightbgimage='".$SQL->f($_POST['contentrightbgimage'])."',
		contentrightbgimagerepeat='".$SQL->f($_POST['contentrightbgimagerepeat'])."',
		contentrightpadding='".$SQL->f($_POST['contentrightpadding'])."',
		contentrighthlindent='".$SQL->f($_POST['contentrighthlindent'])."',
		contentrighthlalign='".$SQL->f($_POST['contentrighthlalign'])."',
		contentrighthlfont='".$SQL->f($_POST['contentrighthlfont'])."',
		contentrighthlfontsize='".$SQL->f($_POST['contentrighthlfontsize'])."',
		contentrighthlcolor='".$SQL->f($_POST['contentrighthlcolor'])."',
		contentrighthlcolorhover='".$SQL->f($_POST['contentrighthlcolorhover'])."',
		contentrighthlbgcolor='".$SQL->f($_POST['contentrighthlbgcolor'])."',
		contentrighthlbgimage='".$SQL->f($_POST['contentrighthlbgimage'])."',
		contentrightindent='".$SQL->f($_POST['contentrightindent'])."',
		contentrightalign='".$SQL->f($_POST['contentrightalign'])."',
		contentrightfont='".$SQL->f($_POST['contentrightfont'])."',
		contentrightfontsize='".$SQL->f($_POST['contentrightfontsize'])."',
		contentrightcolor='".$SQL->f($_POST['contentrightcolor'])."',
		contentrightcolorlink='".$SQL->f($_POST['contentrightcolorlink'])."',
		contentrightcolorhover='".$SQL->f($_POST['contentrightcolorhover'])."',
		contentrightcbgcolor='".$SQL->f($_POST['contentrightcbgcolor'])."',
		contentrightcbgimage='".$SQL->f($_POST['contentrightcbgimage'])."',
		contentrightboxspacer='".$SQL->i($_POST['contentrightboxspacer'])."',

		contentmainbgcolor='".$SQL->f($_POST['contentmainbgcolor'])."',
		contentmainbgimage='".$SQL->f($_POST['contentmainbgimage'])."',
		contentmainbgimagerepeat='".$SQL->f($_POST['contentmainbgimagerepeat'])."',
		contentmainpadding='".$SQL->f($_POST['contentmainpadding'])."',

		sitecopyrightfont='".$SQL->f($_POST['sitecopyrightfont'])."',
		sitecopyrightfontsize='".$SQL->f($_POST['sitecopyrightfontsize'])."',
		sitecopyrightcolor='".$SQL->f($_POST['sitecopyrightcolor'])."',
		sitecopyrightbgcolor='".$SQL->f($_POST['sitecopyrightbgcolor'])."',
		sitecopyrightbgimage='".$SQL->f($_POST['sitecopyrightbgimage'])."',
		sitecopyrightalign='".$SQL->f($_POST['sitecopyrightalign'])."',		

		linkfont='".$SQL->f($_POST['linkfont'])."',
		linkfontsize='".$SQL->f($_POST['linkfontsize'])."',
		linkfontcolor='".$SQL->f($_POST['linkfontcolor'])."',
		linkhovercolor='".$SQL->f($_POST['linkhovercolor'])."',
		inputbgcolor='".$SQL->f($_POST['inputbgcolor'])."',
		inputfontcolor='".$SQL->f($_POST['inputfontcolor'])."',
		inputfontsize='".$SQL->f($_POST['inputfontsize'])."',
		inputfont='".$SQL->f($_POST['inputfont'])."',
		
		tablebgcolor='".$SQL->f($_POST['tablebgcolor'])."',
		tdfont='".$SQL->f($_POST['tdfont'])."',
		tdfontcolor='".$SQL->f($_POST['tdfontcolor'])."',
		tdfontsize='".$SQL->f($_POST['tdfontsize'])."',
		tdheadsbgcolor='".$SQL->f($_POST['tdheadsbgcolor'])."',
		tdheadsbgimage='".$SQL->f($_POST['tdheadsbgimage'])."',
		tdheadscolor='".$SQL->f($_POST['tdheadscolor'])."',
		tdheadscolor2='".$SQL->f($_POST['tdheadscolor2'])."',
		tdheadsfont='".$SQL->f($_POST['tdheadsfont'])."',
		tdheadsfontsize='".$SQL->f($_POST['tdheadsfontsize'])."',
		tdleftbgcolor='".$SQL->f($_POST['tdleftbgcolor'])."',
		tdrightbgcolor='".$SQL->f($_POST['tdrightbgcolor'])."',
		tdstandardbgcolor='".$SQL->f($_POST['tdstandardbgcolor'])."',
		tdhighlightbgcolor='".$SQL->f($_POST['tdhighlightbgcolor'])."',
		tdoddbgcolor='".$SQL->f($_POST['tdoddbgcolor'])."',
		tdodd2bgcolor='".$SQL->f($_POST['tdodd2bgcolor'])."',
		tdevenbgcolor='".$SQL->f($_POST['tdevenbgcolor'])."',
		tdeven2bgcolor='".$SQL->f($_POST['tdeven2bgcolor'])."',
		tdquote='".$SQL->f($_POST['tdquote'])."',
		tdquotefont='".$SQL->f($_POST['tdquotefont'])."',
		tdquotefontsize='".$SQL->f($_POST['tdquotefontsize'])."'
		WHERE style_id='".$styleid."'");
		
	pkHeaderLocation('style','','styleid='.$styleid);
	}
elseif($styleid)
	{
	if(is_array($style_hash))
		{
		$style_select='';
		
		foreach($style_hash as $styles) 
			{
			$style_select.='<option value="'.$styles['style_id'].'"';
				
			if($styleid==$styles['style_id'])
				$style_select.=' selected';
			
			$style_select.='>'.pkEntities($styles['style_name']).'</option>';
			}
		}
		
	if($styleid!='new')
		{
		$styleinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_STYLE." WHERE style_id='".$styleid."' LIMIT 1"));
		
		if($styleinfo['style_user']==1)
			$style_user1='checked';
		else
			$style_user0='checked';
		}
	else
		{
		$styleinfo['bodymargintop']=$styleinfo['bodymarginleft']=$styleinfo['bodymarginright']=$styleinfo['bodymarginbottom']=0;
		$styleinfo['contentleftwidth']=$styleinfo['contentrightwidth']=150;
		$styleinfo['contentboxspacer']=5;
		$styleinfo['fontsizebig']=14;
		$styleinfo['fontsizesmall']=11;
		$styleinfo['contenttophlfontsize']=$styleinfo['contenttopfontsize']=$styleinfo['contentbottomhlfontsize']=
		$styleinfo['contentbottomfontsize']=$styleinfo['contentlefthlfontsize']=$styleinfo['contentleftfontsize']=
		$styleinfo['contentrighthlfontsize']=$styleinfo['contentrightfontsize']=$styleinfo['sitecopyrightfontsize']=		
		$styleinfo['fontsize']=$styleinfo['linkfontsize']=$styleinfo['inputfontsize']=$styleinfo['tdheadsfontsize']=
		$styleinfo['tdfontsize']=$styleinfo['tdquotefontsize']=12;
		
		$styleinfo['bodyfont']=$styleinfo['contenttophlfont']=$styleinfo['contenttopfont']=
		$styleinfo['contentbottomhlfont']=$styleinfo['contentbottomfont']=$styleinfo['contentlefthlfont']=
		$styleinfo['contentleftfont']=$styleinfo['contentrighthlfont']=$styleinfo['contentrightfont']=
		$styleinfo['sitecopyrightfont']=$styleinfo['linkfont']=$styleinfo['tdfont']=$styleinfo['tdheadsfont']=
		$styleinfo['tdquotefont']=$styleinfo['inputfont']='Arial, Helvetica, sans-serif ';		
		
		$style_user0='checked';
		}
		

	if($styleinfo['site_date'])
		$date1=' checked="checked"';
	else
		$date0=' checked="checked"';

	if($styleinfo['site_parsertime'])
		$parser1=' checked="checked"';
	else
		$parser0=' checked="checked"';		

	if($styleinfo['style_template'] == -1)
		$template_select_d=" selected";
	elseif($styleinfo['style_template']==0)
		$template_select_0=" selected";
		
	if($styleinfo['style_width']=='100%')
		$width100=' selected';
	elseif($styleinfo['style_width']=='98%')
		$width98=' selected';
	elseif($styleinfo['style_width']=='95%')
		$width95=' selected';
	elseif($styleinfo['style_width']=='980')
		$width980=' selected';
	else
		$width770=' selected';

	if($styleinfo['style_align']=='left')
		$alignleft=' selected';
	elseif($styleinfo['style_align']=='right')
		$alignright=' selected';
	else
		$aligncenter=' selected';

	if($styleinfo['brandingimageposition']=='center')
		$brandingimagepositioncenter=' selected';
	elseif($styleinfo['brandingimageposition']=='right')		
		$brandingimagepositionright=' selected';
	elseif($styleinfo['brandingimageposition']=='manual')
		$brandingimagepositionmanual=' selected';
	else
		$brandingimagepositionleft=' selected';

	if($styleinfo['contenttophlalign']=='center')
		$contenttophlaligncenter=' selected';
	elseif($styleinfo['contenttophlalign']=='right')		
		$contenttophlalignright=' selected';
	else
		$contenttophlalignleft=' selected';

	if($styleinfo['contenttopalign']=='center')
		$contenttopaligncenter=' selected';
	elseif($styleinfo['contenttopalign']=='right')		
		$contenttopalignright=' selected';
	else
		$contenttopalignleft=' selected';

	if($styleinfo['contentbottomhlalign']=='center')
		$contentbottomhlaligncenter=' selected';
	elseif($styleinfo['contentbottomhlalign']=='right')		
		$contentbottomhlalignright=' selected';
	else
		$contentbottomhlalignleft=' selected';

	if($styleinfo['contentbottomalign']=='center')
		$contentbottomaligncenter=' selected';
	elseif($styleinfo['contentbottomalign']=='right')		
		$contentbottomalignright=' selected';
	else
		$contentbottomalignleft=' selected';
		
	if($styleinfo['contentlefthlalign']=='center')
		$contentlefthlaligncenter=' selected';
	elseif($styleinfo['contentlefthlalign']=='right')		
		$contentlefthlalignright=' selected';
	else
		$contentlefthlalignleft=' selected';

	if($styleinfo['contentleftalign']=='center')
		$contentleftaligncenter=' selected';
	elseif($styleinfo['contentleftalign']=='right')		
		$contentleftalignright=' selected';
	else
		$contentleftalignleft=' selected';
		

	if($styleinfo['contentrighthlalign']=='center')
		$contentrighthlaligncenter=' selected';
	elseif($styleinfo['contentrighthlalign']=='right')		
		$contentrighthlalignright=' selected';
	else
		$contentrighthlalignleft=' selected';

	if($styleinfo['contentrightalign']=='center')
		$contentrightaligncenter=' selected';
	elseif($styleinfo['contentrightalign']=='right')		
		$contentrightalignright=' selected';
	else
		$contentrightalignleft=' selected';
	
	if($styleinfo['contentleftbgimagerepeat']=='no-repeat')
		$contentleftbgimagerepeat_no=' selected';
	elseif($styleinfo['contentleftbgimagerepeat']=='repeat-x')		
		$contentleftbgimagerepeat_x=' selected';
	elseif($styleinfo['contentleftbgimagerepeat']=='repeat-y')
		$contentleftbgimagerepeat_y=' selected';
	else
		$bgimagerepeat=' selected';

	if($styleinfo['contentrightbgimagerepeat']=='no-repeat')
		$contentrightbgimagerepeat_no=' selected';
	elseif($styleinfo['contentrightbgimagerepeat']=='repeat-x')		
		$contentrightbgimagerepeat_x=' selected';
	elseif($styleinfo['contentrightbgimagerepeat']=='repeat-y')
		$contentrightbgimagerepeat_y=' selected';
	else
		$contentrightbgimagerepeat=' selected';

	if($styleinfo['contentmainbgimagerepeat']=='no-repeat')
		$contentmainbgimagerepeat_no=' selected';
	elseif($styleinfo['contentmainbgimagerepeat']=='repeat-x')		
		$contentmainbgimagerepeat_x=' selected';
	elseif($styleinfo['contentmainbgimagerepeat']=='repeat-y')
		$contentmainbgimagerepeat_y=' selected';
	else
		$contentmainbgimagerepeat=' selected';
		
	if($styleinfo['bodybgimagerepeat']=='no-repeat')
		$bgimagerepeat_no=' selected';
	elseif($styleinfo['bodybgimagerepeat']=='repeat-x')		
		$bgimagerepeat_x=' selected';
	elseif($styleinfo['bodybgimagerepeat']=='repeat-y')
		$bgimagerepeat_y=' selected';
	else
		$bgimagerepeat=' selected';

	if($styleinfo['brandingbgimagerepeat']=='no-repeat')
		$brandingbgimagerepeat_no=' selected';
	elseif($styleinfo['brandingbgimagerepeat']=='repeat-x')		
		$brandingbgimagerepeat_x=' selected';
	elseif($styleinfo['brandingbgimagerepeat']=='repeat-y')
		$brandingbgimagerepeat_y=' selected';
	else
		$brandingbgimagerepeat=' selected';

	if($styleinfo['sitecopyrightalign']=='center')
		$sitecopyrightaligncenter=' selected';
	elseif($styleinfo['sitecopyrightalign']=='right')		
		$sitecopyrightalignright=' selected';
	else
		$sitecopyrightalignleft=' selected';


		
	$getpacks=$SQL->query("SELECT * FROM ".pkSQLTAB_TEMPLATE_PACK." ORDER BY templatepack_name ASC");
	while($packs=$SQL->fetch_assoc($getpacks))
		{
		$templatepack_list.='<option value="'.$packs['templatepack_id'].'"'.
			( ($packs['templatepack_id']==$styleinfo['style_template'] ) ? ' selected="selected"' : '') . 
			'>'.pkEntities($packs['templatepack_name']).'</option>';
		}
		
	$style_name=pkEntities($styleinfo['style_name']);
	$style_css=pkEntities($styleinfo['style_css']);
	$style_addcss=pkEntities($styleinfo['style_addcss']);

	$styleinfo['bodybgimage']=$styleinfo['bodybgimage'];
		


	$tdheadsbgimage=pkEntities(pkWWWROOT.$styleinfo['style_images'].'/'.$styleinfo['tdheadsbgimage']);
		
	if(!pkFileCheck($tdheadsbgimage))
		{
		$tdheadsbgimage='../images/blank.gif';
		}


	foreach($styleinfo as $k=>$v)
		$styleinfo[$k]=strstr($k,'color') ? pkEntities(strtoupper($v)) : pkEntities($v);

		
	pkLoadHtml('admin');
	
	#dont look at the varnames - we changed this afterwards - top is next/up bottom is next/down and so on
	$image_pagetop=pkGetHtml('image_hl_up');
	$image_pagebottom=pkGetHtml('image_hl_down');
	$image_pageup=pkGetHtml('image_hl_left');
	$image_pagedown=pkGetHtml('image_hl_right');

	eval("\$site_body.= \"".pkTpl("style_edit")."\";");
	}
else
	{
	$style_row = '';
	
	$query = $SQL->query("SELECT * FROM ".pkSQLTAB_TEMPLATE_PACK." ORDER BY templatepack_name ASC");
	while($packs = $SQL->fetch_assoc($query))
		{
		$pack_hash[$packs['templatepack_id']] = $packs;
		}
		
	if(is_array($style_hash))
		{
		foreach($style_hash as $styles) 
			{
			$rowcolor=$row=rowcolor($rowcolor);
			
			if($styles['style_id']==$config['site_style'])
				{
				$row=standard;
				}
				
			if(trim($style_name=pkEntities($styles['style_name']))=='')
				{
				$style_name = pkGetLang('no_title_formated');
				}
				
			if($styles['style_id'] == pkGetConfig('site_style')) 
				{
				eval("\$style_name= \"".pkTpl("style_default")."\";");
				}
				
				
			$userselect = pkGetLang($styles['style_user']==1 ? 'Yes' : 'No');

			if($styles['style_template']>0)
				{
				$templatepack = $pack_hash[$styles['style_template']]['templatepack_name'];
				}
			elseif($styles['style_template']== -1 )
				{
				$styles['style_template']='default';
				$templatepack = $lang['templatepack_default'];
				}
			else
				{
				$templatepack = $lang['templatepack_modified'];
				}
				
			eval("\$style_row.= \"".pkTpl("style_row")."\";");
			}
		}
	
	
	eval("\$site_body.= \"".pkTpl("style")."\";");
	}
?>