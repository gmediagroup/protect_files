<?php
//
// +----------------------------------------------------------------------+
// | No Direct Links! v0.5                                                |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2003 Hayk Chamyan <hayk@mail.ru>                  |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it  under  the  terms  of  the  GNU Lesser General Public License as |
// | published by the Free Software Foundation; either version 2.1 of the |
// | License, or (at your option) any later version.                      |
// |                                                                      |
// | This  program is distributed in the hope that it will be useful, but |
// | WITHOUT   ANY  WARRANTY;  without  even  the  implied  warranty  of  |
// | MERCHANTABILITY  or  FITNESS  FOR A PARTICULAR PURPOSE.  See the GNU |
// | Lesser General Public License for more details.                      |
// +----------------------------------------------------------------------+
//

/**
 * No Direct Links!
 *
 * NDL is a class that gives you total control over the process of
 * downloading files from your server by hiding the actual file location.
 * Using different rules (based on IP, browser or download manager, http
 * referrer, number of simultaneous connections, authorization status) you
 * can grant or deny permission to download files.
 *
 * Besides, you can also gather statistics on the following:
 *  - download manager
 *  - http referrer
 *  - downloaded files size and more...
 *
 *
 * @author		Hayk Chamyan <hayk@mail.ru>
 * @copyright	(c) 2002-2003 Hayk Chamyan <hayk@mail.ru>
 * @link		http://phpclasses.org
 * @version		0.5
 * @access		public
 * @since		PHP 4.0.4
 */

//error_reporting(0);
@set_time_limit (0);

define ("IS_NGINX", 0);
/**
 * If your server is "nginx", change the parameter to 1
 * do it, than you know that it means
 */

define ("DEFAULT_BUF_SIZE", 8192);
/**
 * Display directly
 *
 * @const	CD_DISPLAY
 * @access	public
 */
define ("CD_DISPLAY", "inline");
/**
 * Save to disk
 *
 * @const	CD_SAVE
 * @access	public
 */
define ("CD_SAVE", "attachment");
/**
 *
 *
 * @const	CT_APP_OS
 * @access	public
 */
define ("CT_APP_OS", "application/octet-stream");
/**
 *
 *
 * @const	HDR_X_SCRIPT
 * @access	public
 */
define ("HDR_X_SCRIPT", "X-Script: No Direct Links! v0.5 hayk@mail.ru");
/**
 *
 *
 * @const	CON_STATUS_NORMAL
 * @access	private
 */
define ("CON_STATUS_NORMAL", 0);

/**
 * NDL - No Direct Links!
 *
 *
 *
 * @package		NDL
 * @author		hayk@mail.ru
 * @copyright	(c) 2002 hayk@mail.ru
 * @version		0.5
 * @access		public
 * @since		PHP 4.3.0
 */
class NDL
{

	/**
	 *
	 *
	 * @var
	 */
	var $vars;

	/**
	 *
	 *
	 * @var
	 */
	var $server;

	/**
	 *
	 *
	 * @var
	 */
	var $fileName;

	/**
	 *
	 *
	 * @var
	 */
	var $fileTime;

	/**
	 *
	 *
	 * @var
	 */
	var $storedFileName;

	/**
	 *
	 *
	 * @var
	 */
	var $contentSize;

	/**
	 *
	 *
	 * @var
	 */
	var $storageDir;

	/**
	 *
	 *
	 * @var
	 */
	var $storedFileSize;



	/**
	 *
	 *
	 * @var
	 */
	var $httpContentDisposition;

	/**
	 *
	 *
	 * @var
	 */
	var $httpContentDescription;

	/**
	 *
	 *
	 * @var
	 */
	var $httpContentType;

	/**
	 *
	 *
	 * @var
	 */
	var $bufSize;

	/**
	 * NDL class constructor.
	 *
	 * @param 	$file	string
	 * @param 	$storage	string
	 * @param 	$description	string
	 * @param 	$type	integer
	 * @param 	$content	string
	 * @access	public
	 * @final
	 */
	function NDL ($file, $storage=DEFAULT_STORAGE, $description=false, $type=CD_SAVE, $content=CT_APP_OS)
	{
		$this->storageDir = $storage;
		$this->bufSize = DEFAULT_BUF_SIZE;
		$this->fileName = $file;
		$path_tmp = preg_replace('#.*\.\.#', '', trim($file) );
		$this->storedFileName = $path_tmp;
		$UserBrowser = '';
		if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) {
			$UserBrowser = "Opera";
		}
		elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) {
			$UserBrowser = "IE";
		}
		/// important for download im most browser
		$this->httpContentType = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ?
		 'application/octetstream' : 'application/octet-stream';
		$this->httpContentDisposition = $type;
		$this->httpContentDescription = $description;
		if (isset($HTTP_GET_VARS))
		{ $this->vars = array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_POST_FILES); }
		else
		{ $this->vars = &$_REQUEST; }
		if (isset($_SERVER))
		{ $this->server = &$_SERVER; }
		else
		{ $this->server = &$GLOBALS["HTTP_SERVER_VARS"]; }
		
		if($this->storageDir{strlen($this->storageDir)}!='/') $this->storageDir.='/';

	} // end function NDL

	/**
	 * NDL class destructor.
	 *
	 * @access	public
	 * @final
	 */
	function _NDL()
	{

	} // end function _NDL

	/**
	 *
	 *
	 * @access	public
	 * @final
	 */
	function send ($db)
	{
		if ( (!isset($this->storedFileName)) || empty($this->storedFileName) || (! file_exists( $this->storageDir . $this->storedFileName)) )	{
			$this->http404 ();
		//	die($this->storageDir . $this->storedFileName);
			//if(!file_exists( $this->storageDir . $this->storedFileName)) die('552454');
			$this->updateStat ("404");
		} else	{ 
			$this->fileTime = filemtime ($this->storageDir . $this->storedFileName);
			$this->storedFileSize = filesize ( $this->storageDir . $this->storedFileName);
			$fd = fopen ($this->storageDir.$this->storedFileName, "rb");
			if ( isset($this->server["HTTP_RANGE"]) ) {
				preg_match ("/bytes=(\d+)-/", $this->server["HTTP_RANGE"], $m);
				$offset = intval($m[1]);
				$this->contentSize = $this->storedFileSize - $offset;
				fseek ($fd, $offset);
				$this->updateStat ("206");
				$this->http206 ();
			} else {
				$this->contentSize = $this->storedFileSize;
				$this->updateStat ("200");
				$this->http200 ();
			}

			if(IS_NGINX){
				$file = $_GET["file"];
				header("X-Accel-Redirect: ".$this->storageDir." " . $file);
			} else {
				$contents='';
				while ( !feof($fd) && (connection_status() == CON_STATUS_NORMAL) ) {

					$contents = fread ($fd, $this->bufSize);
					echo $contents;
					if($this->contentSize < $this->bufSize) $this->contentSize=0;
					else $this->contentSize -= $this->bufSize;
				}
				fclose ($fd);
			}

			if($this->contentSize == 0) {  
				/*end download*/ 
			}
		}
	} // end function send

	/**
	 *
	 *
	 * @access	private
	 * @final
	 */
	function http200 ()
	{

		@ob_end_clean(); /// decrease cpu usage extreme

		header ("HTTP/1.1 200 OK");
		header ("Date: " . $this->getGMTDateTime ());
		header ("X-Powered-By: PHP/" . phpversion());
		header (HDR_X_SCRIPT);
		header ("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
		header ("Last-Modified: " . $this->getGMTDateTime ($this->fileTime) );
		header ("Cache-Control: None");
		header ("Pragma: no-cache");
		header ("Accept-Ranges: bytes");
		header ("Content-Disposition: " . $this->httpContentDisposition . "; filename=\"" . $this->storedFileName  . "\"");
		header ("Content-Type: " . $this->httpContentType);
		if ($this->httpContentDescription)
			header ("Content-Description: " . $this->httpContentDescription );
		header ("Content-Length: " . $this->contentSize);
		header ("Proxy-Connection: close");
		header ("");
	} // end function http200

	/**
	 *
	 *
	 * @access	private
	 * @final
	 */
	function http206 ()
	{
		$p1 = $this->storedFileSize - $this->contentSize;
		$p2 = $this->storedFileSize - 1;
		$p3 = $this->storedFileSize;

		header ("HTTP/1.1 206 Partial Content");
		header ("Date: " . $this->getGMTDateTime ());
		header ("X-Powered-By: PHP/" . phpversion());
		header (HDR_X_SCRIPT);
		header ("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
		header ("Last-Modified: " . $this->getGMTDateTime ($this->fileTime) );
		header ("Cache-Control: None");
		header ("Pragma: no-cache");
		header ("Accept-Ranges: bytes");
		header ("Content-Disposition: " . $this->httpContentDisposition . "; filename=\"" . $this->storedFileName  . "\"");
		header ("Content-Type: " . $this->httpContentType);
		if ($this->httpContentDescription)
			header ("Content-Description: " . $this->httpContentDescription );
		header ("Content-Range: bytes " . $p1 . "-" . $p2 . "/" . $p3);
		header ("Content-Length: " . $this->contentSize);
		header ("Proxy-Connection: close");
		header ("");
	} // end function http206

	/**
	 *
	 *
	 * @access	private
	 * @final
	 */
	function http404 ()
	{
		header ("HTTP/1.1 404 Object Not Found");
		header ("X-Powered-By: PHP/" . phpversion());
		header (HDR_X_SCRIPT);
	} // end function http404

	/**
	 *
	 *
	 * @access	private
	 * @final
	 */
	function http403 ()
	{
		header ("HTTP/1.1 403 Forbidden");
		header ("X-Powered-By: PHP/" . phpversion());
		header (HDR_X_SCRIPT);
		header ("");
	} // end function http403

	/**
	 *
	 * @param	int		$time	UNIX timestamp
	 * @return	string	GMT formated time
	 * @access	public
	 * @final
	 */
	function getGMTDateTime ($time=NULL)
	{
		$offset = date("O");
		$roffset = "";
		if ($offset[0] == "+")
		{
			$roffset = "-";
		}
		else
		{
			$roffset = "+";
		}
		$roffset .= $offset[1].$offset[2];
		if (!$time)
		{
			$time = Time();
		}
		return (date ("D, d M Y H:i:s", $time+$roffset*3600 ) . " GMT");
	} // end function getGMTDateTime


	/**
	 *
	 * @param	string	$code HTTP code
	 * @access	public
	 * @abstract
	 */
	function updateStat ($code)
	{
		return true;
	} // end function updateStat
} // end class NDL

?>
