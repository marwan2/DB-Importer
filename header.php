<?php include_once("../includes/config.php"); 
//include_once("functions.php"); ?>
<?php $PageTitle="COB Events Admin Panel"; ?>
<?php
	function SaveUserPageViews($UserID,$PageURL)
	{
		if(mysql_query("insert into cpusers_activites_pageview (PageURL,UserID,ViewTime,LocationIPAddress) values('".$PageURL."','".$UserID."','".date("Y-m-d h:i:s")."','".$_SERVER['REMOTE_ADDR']."')")) {
			return true;
		}
		else {
			return false;
		}
	}
	function getFileName($URL)
		{
			$filename = basename($URL);
			if(strstr('?',$filename)) {
				$arrFileName = split('?',$filename);
				return $arrFileName[0];
			}
			else {
				return $filename;
			}
		}
	function highLight($current, $compareWith)
	{
		if($current==$compareWith)
			$val = " class='current' ";
		else if (strstr($current,$compareWith))
		{
			$val = " class='current' ";
		}
		return $val;
	}
	//Run SQL Query script
	if($_GET['runQuery']!=null)
	{		
		if(mysql_query("".$_GET['runQuery']."")) {
			if(isset($_GET['backURL'])) {
				header("location: ".$_GET['backURL']."");
			}
			else {
				header("location: ".getFileName($_SERVER['PHP_SELF'])."");
			}
		}
		else {
			$eMsg="<script>window.alert('Sorry there is an error Error in ".mysql_error()."');</script>";
		}
	}//end of Run SQL Query script
	//To check if the user cookie expired or try to access the admin panel pages without sucessfull login
	if(isset($_COOKIE['ck_userID']))
	{
		$CookieUserID = $_COOKIE['ck_userID'];
		$DBTable = "cpusers";
		$DBIDField = "UserID";
		$queryCPUser = mysql_query("select * from ".$DBTable." where ".$DBIDField."='".$CookieUserID."' ");
		if(mysql_num_rows($queryCPUser)>0)
		{
			while($rowUser = mysql_fetch_array($queryCPUser))
			{
				$UserID 	 = $rowUser['UserID'];
				$Name   	   = $rowUser['Name'];
				$UserEmail  = $rowUser["Email"];
				$Title  	  = $rowUser['Title'];
				$Privilages = $rowUser["Privilages"];
				$IsAccessUsersTasks = $rowUser["IsAccessUsersTasks"];
				$IsAccessDirectory = $rowUser["IsAccessDirectory"];
			}
		}
		else
		{
			header("location: login.php?backURL=".$_SERVER['REQUEST_URI']."");
		}
		if(SaveUserPageViews($CookieUserID,$_SERVER['REQUEST_URI'])) {
		}
		else {
			die("Can't save user activities");
		}
	}
	else
	{
		header("location: login.php?backURL=".$_SERVER['REQUEST_URI']."");
	}
	$arrEventItems = array("Registration"=>"events_registration.php",
					   "Accomodation"=>"events_accommodation_hotels.php",
					   "List"=>"events_reservation_list.php",
					   "Search"=>"events_search_list.php",
					   "Abstracts"=>"events_abstracts.php",
					   "Program"=>"event_program/ProgramBackEndRelease.php",
					   "Budget"=>"events_budget.php",
					   "Sponsors"=>"events_sponsors.php",
					   "Exhibition"=>"events_exhibition.php",
					   "Marketing"=>"events_marketing.php",
					   "Invitations"=>"events_invitations.php",
					   "Site"=>"events_site_builder.php",
					   "Printings"=>"events_printing_materials.php",
					   "File Coordinator"=>"events_file_coordinator.php",
					   "Access Pages"=>"events_access_pages.php");
?>
<?php
	//This part to check if the user navigating inside the Events pages, 
	//if yes e will be redirected to the main events page to select the 
	//event then continue navigating on the selected event items
	if(strstr(getFileName($_SERVER['PHP_SELF']),"event"))
	{
		session_start();
		$SelectedEventID = $_SESSION['eventID'];
		if(isset($_SESSION['eventID']))
		{
			$queryEvent = mysql_query("select * from events where EventID='".$SelectedEventID."'");
			if(mysql_num_rows($queryEvent)>0)
			{
				while($rowEvent=mysql_fetch_array($queryEvent))
				{
					$SelectedEventID 		= $rowEvent["EventID"];
					$SelectedEventTitle 	 = $rowEvent['EventTitle'];
					$SelectedEventVenue 	 = $rowEvent['Venue'];
					$SelectedEventDateFrom  = $rowEvent['DateFrom'];
					$SelectedEventDateTo 	= $rowEvent['DateTo'];
					$SelectedEventImage 	 = $rowEvent['Image'];
					$UploadsDir = "../uploads/events/";
					
					if(is_file("".$UploadsDir."".$SelectedEventImage."")) {
						$EventImage="<img src='".$UploadsDir."".$SelectedEventImage."' style='height:45px; margin-right:6px;' align='left' />";
					}
					else {
						$EventImage="";
					}
					
					if($Privilages=="Super") {
						$EventPagesAccess = "<a href='events_access_pages.php?EventID=".$SelectedEventID."' title='Edit Control Panel User Access Pages'>Users Access Pages</a> | ";
					}
					$SelectedEvent = "
					<div style='width:1100px; margin:0 auto; '>
						<div style='float:left; background:#a0a0a0 url(images/e_left.jpg); width:10px;height:60px;'></div>
						<div style='float:left; background:#a0a0a0 url(images/e_mid.jpg); height:60px; width:1080px;'>
							<table style='width:100%; margin-top:8px;' cellpadding='0' cellspacing='0'>
							<tr><td><h2>".$EventImage." ".$SelectedEventTitle."</h2>
							<br />".$SelectedEventVenue." - ".formatEventDate($SelectedEventDateFrom,$SelectedEventDateTo)." 
							<a href='events.php?backURL=".$_SERVER['REQUEST_URI']."'>Change</a></td>
							<td align='right' nowrap='nowrap'> ".$EventPagesAccess."
							<a href='events_file_coordinator.php' title='File Coordinator Check List'>File Coordinator</a> | 
							<a href=\"javascript:EventRegListAdd('".$SelectedEventID."')\">
							<img src='images/plus.gif' align='absmiddle'> Add new Registrant</a></td>
							</tr>
							</table>
						</div>
						<div style='float:right; background:#a0a0a0 url(images/e_right.jpg); width:10px;height:60px;'></div>
					</div>";
					
					//Check if Logged in user has access to event pages he open
					$EventFileName = getFileName($_SERVER['REQUEST_URI']);
					if(in_array($EventFileName,$arrEventItems))
					{
						if(strtolower($Privilages)!="super")
						{
							$AccPagesEventItemKey = array_search($EventFileName, $arrEventItems);
							$queryEventAccess = mysql_query("select * from event_access_pages where EventID='".$SelectedEventID."' and UserID='".$CookieUserID."' and EventItem='".$AccPagesEventItemKey."'");
							if(mysql_num_rows($queryEventAccess)>0) 
							{
								/*while($rowEvAccessPage = mysql_fetch_array($queryEventAccess))
								{
									if($rowEvAccessPage["EventItem"]==$ArrAccPagesKey)
									{
										break;
									}
									else
									{
										header("location: events_no_access.php");
									}
								}*/
							}
							else {
								header("location: events_no_access.php");
								//echo"results not found";
							}
							//echo"results found, <br />ArrAccPagesKey:".$ArrAccPagesKey." <br /> EventFileName:".$EventFileName."";
						}
					}
				}
			}
			else
			{
				unset($_SESSION["eventID"]);
				header("location: events.php?backURL=".$_SERVER['REQUEST_URI']."");
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon" />
<link href="css/styles.css" type="text/css" rel="stylesheet" />
<script src="js/scripts.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../css/stickytooltip.css" />
<script type="text/javascript" src="../js/stickytooltip.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>
<script src="js/jquery.timePicker.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/timePicker.css" type="text/css" />
<!-- Comet Chat Plug-in -->
<link type="text/css" href="/admin/control/cometchat/cometchatcss.php" rel="stylesheet" charset="utf-8">
<script type="text/javascript" src="/admin/control/cometchat/cometchatjs.php" charset="utf-8"></script>
</head>
<body>
<div style="margin-right:auto; margin-left:auto; width:100%; text-align:center; background:#f9f9f9 url(images/cp_header_bg.jpg) repeat-x 0 0; height:96px;">
  <div style="margin-left:auto; margin-right:auto; width:1100px; height:62px;">
    <div style='float:left; font:bold 20px Arial, Helvetica, sans-serif; color:#CCC;'> <img src="images/admin_panel_txt.png" vspace="12" /> </div>
    <div id="UserBlock">
      <table cellpadding="0" cellspacing="0" height="40" style="color:#f0f0f0; font:normal 10px Verdana, Geneva, sans-serif;">
        <tr>
          <td style="background:url(images/header_block_left.jpg) no-repeat 0 bottom; width:7px;"></td>
          <td style="background:#000; font:normal 10px Verdana, Geneva, sans-serif;">Welcome <?php echo"".$Title." ".$Name.""; ?>, <a href='logout.php?backURL=<? echo"".$_SERVER['REQUEST_URI'].""; ?>'>Sign out</a> | <a href='cp_users_profile.php' title="Go to My profile">My profile</a> | <a href='../index.php' target="_blank" title="Go to website">Go to website</a></td>
          <td style="background:url(images/header_block_right.jpg) no-repeat 0 bottom; width:7px;"></td>
        </tr>
      </table>
    </div>
  </div>
  <div style="margin-left:auto; margin-right:auto; width:1100px; height:34px;">
    <div id="MainMenu" style="width:800px; float:left;">
      <ul>
        <?php
			$MenuItems = array
					("Home"=>array("index.php"=>array("Tasks"=>"index.php",
													  "Statistics"=>"statistics.php",
													  "Feedbacks"=>"contactus_feedbacks_messages.php"
													  )//End of Sub menu Array
								  )//End of value array
					 ,
					 "Directory"=>array("directory.php"=>array("+ Add new"=>"directory_add.php?backURL=".$_SERVER['REQUEST_URI']."",
													  		   "View Directory"=>"directory.php",
															   "Search"=>"directory_search.php",
															   "Import"=>"importdata.php",
															   "Export"=>"directory_export_app.php",
															   "Mailing"=>"directory_mailing.php",
															   "Duplicates"=>"directory_duplicates_operations.php",
															   "Operations"=>"directory_operations.php",
															   "Directory Updates"=>"directory_updates_via_sites.php",
															   " "=>"directory_edit.php",
															   ""=>"directory_mailshot_report.php"
													  )//End of Sub menu Array
								  )//End of value array
					 ,
					 "Events"=>array("events.php"=>array("Add new event"=>"events_add.php",
														 "Registration"=>"events_registration.php",
														 "Accomodation"=>"events_accommodation_hotels.php",
														 "List"=>"events_reservation_list.php",
														 "Search"=>"events_search_list.php",
														 "Abstracts"=>"events_abstracts.php",
														 "Program"=>"event_program/",
														 "Budget"=>"events_budget.php",
														 "Sponsors"=>"events_sponsors.php",
														 "Exhibition"=>"events_exhibition.php",
														 "Marketing"=>"events_marketing.php",
														 "Site"=>"events_site_builder.php",
														 "Printings"=>"events_printing_materials.php",
														 "Cord"=>"events_file_coordinator.php",
														 "  "=>"events_access_pages.php",
														 " "=>"events_start.php",
														 ""=>"events_no_access.php"
														 )//End of Sub menu Array
									)//End of value array
					,
					 "Membership"=>array("membership.php"=>array("Membership"=>"membership.php",
					 											 "+ Add new member"=>"membership_add.php",
																 " "=>"membership_edit.php",
					 											 ""=>"membership_renewal.php"
														 )//End of Sub menu Array
									)//End of value array
					,
					 "News"=>array("news.php"=>array("News"=>"news.php",
													 "+ Add new article"=>"news_add.php",
													 "News Sections"=>"news_cats.php",
													 "+ Add new section"=>"news_cats_add.php",
													 " "=>"news_edit.php",
													 " "=>"news_cats_edit.php"
												    )//End of Sub menu Array
									)//End of value array
					,
					"Sources"=>array("sources.php"=>array("Societies/Groups"=>"sources_societies.php",
														  "Bank Accounts"=>"sources_bankaccounts.php",
														  "Hotels"=>"sources_hotels.php",
														  "Countries"=>"sources_countries.php",
														  "Cities"=>"sources_cities.php",
														  "Specialties"=>"sources_specialties.php",
														  "Budget"=>"sources_budget_categories.php",
														  "Business Mates"=>"sources_businessmates.php",
														  "Mail Messages"=>"mailmessages_view.php",
														  "Sponosor Packages"=>"sources_sponsors_packages.php"
														 )//End of Sub menu Array
									)//End of value array
					,
					"Uploader"=>array("uploader.php"=>array("View files"=>"uploader.php",
															"+ Upload new file"=>"uploader_add.php",
															"+ Add new category"=>"uploader_cat_add.php",
															" "=>"uploader_edit.php"
														 )//End of Sub menu Array
									)//End of value array
					,
					"Media"=>array("media.php"=>array("Media"=>"media.php",
													  "Photo Albums"=>"media_showalbums.php",
													  "Videos"=>"media_showvideos.php",
													  "+ Add new Album"=>"media_album_add.php",
													  "+ Add new video"=>"media_add.php",
													  " "=>"media_edit.php",
													  " "=>"media_album_edit.php",
													  " "=>"media_edit.php",
													  " "=>"media_edit.php"
										 )//End of Sub menu Array
									)//End of value array
					,
					"CP Users"=>array("cp_users.php"=>array("User Accounts"=>"cp_users.php",
															"+ Add new account"=>"cp_users_add.php",
															"My Profile"=>"cp_users_profile.php",
															""=>"cp_users_edit.php"
										 )//End of Sub menu Array
									)//End of value array
					
					);//End of MenuItems Array
					
foreach($MenuItems as $MainItemTitle=>$SubArray)
{
	foreach($SubArray as $MainItemURL=>$SubMenuItemsArray)
	{
		if(strstr($_SERVER['PHP_SELF'],$MainItemURL))
		{
			echo"<li><a href='".$MainItemURL."' class='current' title='".$MainItemTitle."'>".$MainItemTitle."</a></li>";
			$PageTitle = "".$MainItemTitle."";
		}
		else
		{
			// this is to check if the sub menu URL is inside a certain 
			// array of main items to highlight the main item
			if(in_array(getFileName($_SERVER['PHP_SELF']),$SubMenuItemsArray))  
			{
				echo"<li><a href='".$MainItemURL."' class='current' title='".$MainItemTitle."'>".$MainItemTitle."</a></li>";
				$PageTitle = "".$MainItemTitle."";
				foreach($SubMenuItemsArray as $SubMenuTitle=>$SubMenuURL)
				{
					if(strstr($_SERVER['PHP_SELF'],$SubMenuURL))
					{
						$SubMenuItems .="<li><a href='".$SubMenuURL."' class='current' title='".$SubMenuTitle."'>".$SubMenuTitle."</a></li>";
						$PageTitle .= " > ".$SubMenuTitle."";
					}
					else
					{
						$SubMenuItems .="<li><a href='".$SubMenuURL."' title='".$SubMenuTitle."'>".$SubMenuTitle."</a></li>";
					}
				}				
			}
			else
			{
				echo"<li><a href='".$MainItemURL."' title='".$MainItemTitle."'>".$MainItemTitle."</a></li>";
			}
		}
		
		//Retreive Submenu items if the user click on one of the Main Menu Items
		if(strstr($_SERVER['PHP_SELF'],$MainItemURL))
		{
			foreach($SubMenuItemsArray as $SubMenuTitle=>$SubMenuURL)
			{
				if(strstr($_SERVER['PHP_SELF'],$SubMenuURL))
				{
					$SubMenuItems .="<li><a href='".$SubMenuURL."' class='current' title='".$SubMenuTitle."'>".$SubMenuTitle."</a></li>";
					$PageTitle .= " > ".$SubMenuTitle."";
				}
				else
				{
					$SubMenuItems .="<li><a href='".$SubMenuURL."' title='".$SubMenuTitle."'>".$SubMenuTitle."</a></li>";
				}
			}
		}
		else
		{
			//No need to implement any code here, as it has been handled above
		}
	}
}
		?>
      </ul>
    </div>
    <?php
		if(isset($_GET['q']) && $_GET['q']!="")
		{
			$reset="<a href='".getFileName($_SERVER['PHP_SELF'])."' title='Clear search results' alt='clear'><img src='images/cross.png' /></a>";
		}

		foreach($_GET as $keyname => $value) 
		{
			$hiddenFieldsAsQStrings .= "<input type='hidden' name='".$keyname."' value='".$value."' />";
		}
	?>
    <div style="float:right; text-align:right;"><form action="<?php echo"".$_SERVER['REQUEST_URI'].""; ?>" method="get" style="display:inline"><table cellpadding="0" cellspacing="0" align="right"><tr><td><?php echo"".$reset.""; ?> <?php echo"".$hiddenFieldsAsQStrings.""; ?><input type="text" value="<?php echo"".$_GET['q'].""; ?>" name="q" id="q" style="width:220px; background:url(images/search_box_bg.jpg) repeat-x; height:19px; border:1px solid #999; font:normal 13px Arial,verdana; padding:4px 2px;" onfocus="this.select();" /></td><td><input type="image" src="images/search_btn.png" align="absmiddle" title="Search" class="SearchBtn2" /></td></tr></table></form></div>
  </div>
</div>
<div style="margin-right:auto; margin-left:auto; width:100%; text-align:center; background:#f9f9f9 url(images/cp_submenu_bg.jpg) repeat-x 0 bottom; height:35px; border-bottom:7px solid #dfdfdf;">
  <div style="margin-left:auto; margin-right:auto; width:1100px;">
    <div id="SubMenu">
      <ul>
        <?php
			echo"".$SubMenuItems."";
		?>
      </ul>
    </div>
  </div>
</div>
<head>
<title><?php echo"".$PageTitle.""; ?></title>
</head>
<div id="cp_body">
<?php
	echo"".$SelectedEvent."";
?>