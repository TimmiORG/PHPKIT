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


class pkBbcode
	{
	protected $text = '';
	
	var $urldetect=1;
	var $urlcut=1;
	var $urlmaxwidth=60;
	var $urlwidth1=50;
	var $urlwidth2=-10;
	
	var $censoruse=1;
	var $imageresize=0;
	var $textmaxlength=0;
	
	protected $parse_bbcode = 1;
	protected $parse_smilies = 1;
	protected $parse_html = 0;
	protected $parse_images = 1;

	protected $smilies_search = array();
	protected $smilies_replace = array();	
	
	protected $censorreplace = '*';
	protected $censor_badword_hash = array();
	
	var $bbhash='';
	var $smiliehash='';
		
	protected $urldetect_search = array(
#			'#(?:(?<!http://|https://|ftp://|ftps://)(www\.([^\s]*)))#iuS', #not working cause every char is allowed in urls
			'#(?:(?<!img]|mgl]|mgr]|url=|url]|ef="|rc="|on="])((ht|f)tp(s?)://([^\s]*)))#iuS', #clean except links as linktexts
			);

	protected $urldetect_replace = array(
#			'[url=http://\\1]\\1[/url]', #not working cause every char is allowed in urls
			'[url]\\1[/url]',
			);
			

	#@Method: 	__construct
	#@Access:	public
	#@Param:	void
	#@Return:	void
	#@Desc:		Constructor. Predefines smilies, bbcodes, and censor words.
	public function __construct()
		{
		$this->bbhash = pkCfgData('bbcode');

		#smilies
		$obj = new smilies;
		$array = $obj->getSmilieCache();
		$array = is_array($array) ? $array : array();
		
		foreach($array as $smilie)
			{
			$this->smilies_search[] = pkEntities($smilie['smilie_code']);
			$this->smilies_replace[] = '<img src="'.$smilie['smilie_path'].'" alt="'.$smilie['smilie_title'].'" />';
			}
		
		#censorlist
		$badwords = pkGetConfig('censor_badword');
		$badwords = is_string($badwords) ? $badwords : '';
		$badwords = explode("\n", $badwords);
		$this->censor_badword_hash = $badwords;
		}
	#@END Method: __construct
	
	
	#@Method: 	parse
	#@Access:	public
	#@Param:	string text
	#@Param:	bool html
	#@Param:	bool bb
	#@Param:	bool smilies
	#@Param:	bool images
	#@Param:	bool censor
	#@Param:	bool imageresize
	#@Param:	bool textmaxlength
	#@Return:	string
	#@Desc:		Primary method to parses texts.
	public function parse($text='',$html=0,$bb=0,$smilies=0,$images=0,$censor=0,$imageresize=0,$textmaxlength=0)
		{
		if(empty($text))
			{
			return '';
			}
	
		$this->text = ' '.$text.' '; #s simple solution for smiles and other tags at the beginning to be parsed when containing a leading blank-space
		$this->imageresize = $imageresize;
		$this->textmaxlength = $textmaxlength;
		$this->urlmaxwidth = $textmaxlength;
		$this->urlmaxwidth ? NULL : $this->urlcut=0;
		
		$this->parse_bbcode = $bb ? 1 : 0;
		$this->parse_smilies = $smilies ? 1 : 0;
		$this->parse_images = $images ? 1 : 0;
		$this->parse_html = $html ? 1 : 0;
		

		#should be removed in the future, sessions ID are not visible anymore
		$this->text = pkRemoveSessionId($this->text);
			
		#censor
		if($censor)
			{
			$this->textcensor();
			}
		
		#urldetect - not when html is active
		if($this->urldetect && !$this->parse_html)
			{
			$this->urldetect();
			}
		
		$this->text = $this->parse_html ? $this->prepare_html($this->text) : pkEntities($this->text);
		
		if(!$this->parse_images)
			{
			$this->remove_images();
			}
	
		if($this->parse_bbcode)
			{
			$this->parse_bbcode();
			}
			
		if($this->parse_smilies)
			{
			$this->parse_smilies();
			}

		$this->text = nl2br($this->text);

		#html allowed
		if($this->parse_html)
			{
			$this->text = str_replace('&quot;','"',$this->text);
			}
		else
			{
			$this->text = preg_replace("/(vb|java)script:/i","\\1 script:",$this->text);		
			}

		if($this->textmaxlength)
			{
			$this->text = pk::txtwrap($this->text,$this->textmaxlength);
			}

		#remove not need whitespaces
		$this->text = trim($this->text);

		return $this->text;
		}
	#@END Method: parse


	#@Method:	prepare_html
	#@Access:	protected
	#@Param:	string text
	#@Return:	string
	#@Desc:
	function prepare_html($text='')
		{
		$text = preg_replace("#<table(.*)>([ |\n|\r\s]{1,})<tr#iUs","<table\\1><tr",$text);
		$text = preg_replace("#<tr(.*)>([ |\n|\r\s]{1,})<td#iUs","<tr\\1><td",$text);
		$text = preg_replace("#</td>([ |\n|\r\s]{1,})</tr>#iUs","</td></tr>",$text);
		$text = preg_replace("#</tr>([ |\n|\r\s]{1,})</table>#iUs","</tr></table>",$text);
		
		return str_replace('"',"&quot;",$text);
		}
	#@END Method: prepare_html


	#@Method:	urldetect
	#@Access:	protected
	#@Param:	void
	#@Return:	void
	#@Desc:		
	protected function urldetect()
		{
		$this->text = preg_replace($this->urldetect_search,$this->urldetect_replace,$this->text);
		}
	#@END Method: urldetect	
	
	
	#@Method:	parse_bbcode
	#@Access:	protected
	#@Param:	void
	#@Return:	void
	#@Desc:
	protected function parse_bbcode()
		{
		$text = &$this->text;#reference - for a smarter use
		
		#search & replace standard bbcodes
		foreach($this->bbhash as $code)
			{
			switch($code['type'])
				{
				case 'single' :
					$text = preg_replace("#\[".$code['tag']."\]#isuSU",$code['html'],$text);
					break; #END case single

				case 'double' :
					$pattern = "#\[(".$code['tag'].")=(.*)\](.*)\[/\\1\]#eisuSU";
					
					$text = preg_replace($pattern,"\$this->replace_bbcode('$code[html]','\\3','\\2')",$text);
					break; #END case double
					
				case 'img' :
				
					if($this->parse_images)
						{
						$pattern = "#\[(".$code['tag'].")]([^\"\?\&]*\.(gif|jpg|jpeg|bmp|png))([\s]?)\[\/\\1\]#eiU";
					
						$text = preg_replace($pattern,"\$this->replace_image('$code[html]','\\2')",$text);
						}
					break; #END case img
				case 'url' :
					$func = 'replace_url';

					$pattern = "#\[(".$code['tag'].")\](.*)\[/\\1\]#eisuSU"; #format: [url]link[/url]
					$text = preg_replace($pattern,"\$this->$func('$code[html]','\\2')",$text);

					$pattern = "#\[(".$code['tag'].")=(.*)\](.*)\[/\\1\]#eisuSU"; #format [url=link]text[/url]
					$text = preg_replace($pattern,"\$this->$func('$code[html]','\\2','\\3')",$text);					
					
					break; #END case url
				case 'list' :
				default :
					$func = 'replace_bbcode';
					$func = $code['type']=='list' ? 'replace_list' : $func;
					
					$pattern = "#\[(".$code['tag'].")\](.*)\[/\\1\]#eisuSU";
					
					while(preg_match($pattern,$text))
						{
						$text = preg_replace($pattern,"\$this->$func('$code[html]','\\2')",$text);
						}

					break; #END default
				}#END switch
			}#END foreach bbhash
		}
	#@END Method: parse_bbcode

	#@Method: 	parse_smilies
	#@Access:	protected
	#@Param:	void
	#@Return:	void
	#@Desc:		
	protected function parse_smilies()
		{
		$this->text = str_replace($this->smilies_search,$this->smilies_replace,$this->text);
		}
	#@END Method: parse_smilies
	

	#@Method: 	replace_list
	#@Access:	protected
	#@Param:	string html
	#@Param:	string text
	#@Param:	string option
	#@Return:	string
	#@Desc:		
	protected function replace_bbcode($html,$text,$option='')
		{
		$option = trim($option);
		$option = empty($option) ? $text : $option;
		
		$html = str_replace("{text}",$text,$html);
		$html = str_replace("{option}",$option,$html);
		
		return $html;
		}		
	#@END Method: replace_bbcode
	
		
	#@Method: 	replace_list
	#@Access:	protected
	#@Param:	string html
	#@Param:	string text
	#@Return:	string
	#@Desc:
	protected function replace_list($html,$text)
		{
		#need trim here to avoid empty cols between list-elements
		#text should contains some li/* elements
		$text = trim($text);

		#empty list-tags (ul,ol,dl) arnt allowed - so return an empty string in this case
		if(empty($text))
			{
			return '';
			}

		#replace correctly with <li></li> without linebreaks
		$pattern = "#\[(li)\](.*)\[/\\1\]#isuSU";
		$text = preg_replace($pattern,'<li>\\2</li>',$text);
	
		
		#do we have simple sinlge tags?
		if(strstr($text,'[*]'))
			{
			#split by the simple single tag [*]
			$matches = explode('[*]',$text);
			
			foreach($matches as $i=>$str)
				{
				$str = trim($str);#empty lines will be removed
				
				if(empty($str))
					{
					unset($matches[$i]);
					continue;
					}
				
				$matches[$i] = '<li>'.$str.'</li>';
				}
			
			$text = implode('',$matches);
			}#END simple single tags
			
		
		#created the whole list and clean it up
		$text = str_replace("{text}",$text,$html);
		$text = preg_replace("#(\/li|ul|ol type=\"a\"|ol type=\"1\")>(.*)*<(li|\/ol|\/ul){1}>#sSU",'\\1><\\3>',$text);#removes everything between list related tags
		$text = $text==str_replace("{text}",'',$html) ? '' : $text; # if the list is now empty nothing will be displayed, otherwise it wouldnt not be valid XHTML
		
		return $text;
		}
	#@END Method: replace_list
	
	
	#@Method:	replace_url
	#@Access:	protected
	#@Param:	string html
	#@Param:	string url
	#@Param:	[string text]
	function replace_url($html,$url,$text='')
		{
		$url	= trim($url);
		$text 	= empty($text) ? $url : $text;
		$text 	= trim($text);
		
		if($this->urlcut && strlen($text)>$this->urlmaxwidth && !preg_match("/[><\[\]]/",$text))
			{
			$text = substr($text,0,$this->urlwidth1)."...".substr($text,$this->urlwidth2);
			}
	
		#prevent double-links when imageresize is on
		if($this->imageresize && preg_match("/\<a (.*)<\/a>/i",$text))
			{
			return str_replace("\\\"","\"",$text);
			}
		
		$str = str_replace("{text}",$text,$html);
		$str = str_replace("{option}",$url,$str);
		$str = str_replace("\\\"","\"",$str);
		
		return $str;
		}
	#@END Method: replace_url


	#@Method:	replace_ images
	#@Access:	protected
	#@Param:	string html
	#@Param:	string link
	#@Return:	string
	#@Desc:
	protected function replace_image($html,$link)
		{
		if($this->imageresize)
			{
			$html = '<a href="'.$link.'" target="_blank">'.$html.'</a>';
			$link = pkLinkFx('rsimg','src='.base64_encode($link));
			}
		
		$str = str_replace("{image}",$link,$html);
		
		return $str;
		}
	#@END Method: replace_image
	
	
	#@Method:	remove_images
	#@Access:	protected
	#@Param:	void
	#@Return:	void
	#@Desc:	
	protected function remove_images()
		{
		$pattern = "#\[(/?)(img|imgr|imgl)\]#i";
		
		$this->text = preg_replace($pattern,'[\\1url]',$this->text);
		}
	#@END Method: remove_images
	

	#@Method:	textcensor
	#@Access:	protected
	#@Param:	void
	#@Return:	void
	#@Desc:
	protected function textcensor()
		{
		if(empty($this->censor_badword_hash) || $this->censoruse!=1)
			{
			return;
			}

		$replacer = $this->censorreplace;
		$censorlist = $this->censor_badword_hash;
		
		
		foreach($censorlist as $badword)
			{
			$badword = trim($badword);
			
			if(empty($badword))
				{
				continue;
				}
			
			#badword length
			$len = strlen($badword);
			$replace = '';
			
			if(preg_match("#\{(.*)\}#isU",$badword))#exact match 
				{
				$len = $len-2;
				$replace = str_repeat($replacer,$len);
					
				$this->text = preg_replace($badword," ".$replace." ",$this->text);
				}
			else
				{
				$replace = str_repeat($replacer,$len);
				
				$this->text = eregi_replace($badword,$replace,$this->text);
				}
			}#END foreach
		}
	#@END Method: textcensor
	}
#@END Method: pkBbcode
?>