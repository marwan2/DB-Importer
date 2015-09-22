<?php
//include_once"includes/config.php";
// I comment the following lines because I replace the connection with a config.php connection file (I removed the comment)

$server   = $_GET['s_nm'];
$username = $_GET['s_user'];
$password = $_GET['s_pw'];
$database = $_GET['db'];
try {
	//Connect to Server
	$con = mysql_connect($server,$username,$password);
}
catch(Exception $e) {
	echo"Error connecting to Server (".$server."): ".$e->getMessage()."";
}
if (!$con) {
  	die('Could not connect: ' . mysql_error());
}

try {
	//Connect to database
	mysql_select_db($database, $con);
} catch(Exception $e) {
	echo"Error connecting to database (".$database."): ".$e->getMessage()."";
}
?>
<style>
	* { font:normal 12px Arial, Helvetica, sans-serif; }
	.ok { color:#090; }
	.notok { color:#F00; }
</style>
<?php
	//this function to preview successfull query
	function OK($str)
	{
		return "<span class='ok'><img src='images/ok.jpg' align='absmiddle' /> ".$str."</span>";
	}
	//this function to preview worng query and mysql error
	function NotOK($str)
	{
		return "<span class='notok'><img src='images/error.jpg' align='absmiddle' /> ".$str."</span>";
	}
	function getQueryArr($array)
	{
		// variable $array is an array of many arrays >>> $array = array($array1,$array2,$array3, ......)
        global $longestArrayLength;
		$numOfPosts = count($array);
		foreach($array as $subArr)
		{
			for($i=0; $i<$numOfPosts; $i++)
			{
				if(count($array[$i]) > $longestArrayLength)
					$longestArrayLength = count($array[$i]);
			}
		}
		
		for($j=0; $j<$longestArrayLength; $j++)
		{
			$q = "";
			for($i=0; $i<$numOfPosts; $i++)
			{
				if($i==($numOfPosts-1)) {
					$q .= "'".trim($array[$i][$j])."'";
				}
				else {
					$q .= "'".trim($array[$i][$j])."',";
				}
			}	
			$arrQueries[] = $q;
		}
		return $arrQueries;
	}
	
	$perviewer = array(); //this variable will collect all queries, messages and errors to echo on the web page
	$tableName = $_GET['table'];
	$count =0;
	$numOfPosts = 0; //it will count all textarea post fields only
	
	//submitUploadData is the name of the submit button located in the file (importdata.php)
	if(isset($_POST['submitUploadData']))
	{
		if($tableName=="directory") {
			$arrExmptedFields = array("submitUploadData","SpecialtyArr","SocietyID","MembershipID","Year","Amount","InvoiceNo","InvoiceDate"); 
			//arrExmptedFields contain post values that shouldn't added to the main insert query

			if(isset($_POST["SpecialtyArr"]) && $_POST["SpecialtyArr"] !=null)
				$allNumPosts = count($_POST);
			else
				$allNumPosts = count($_POST)+1;
				
			//Sub Array for Inserting in Table "membership_details"
			$arr_Year 		= explode("<br />",nl2br($_POST['Year']));
			$arr_Amount  	  = explode("<br />",nl2br($_POST['Amount']));
			$arr_InvoiceNo   = explode("<br />",nl2br($_POST['InvoiceNo']));
			$arr_InvoiceDate = explode("<br />",nl2br($_POST['InvoiceDate']));
			$arrAllSub1[] = $arr_Year;
			$arrAllSub1[] = $arr_Amount;
			$arrAllSub1[] = $arr_InvoiceNo;
			$arrAllSub1[] = $arr_InvoiceDate;
			$arr_MembershipDetails = getQueryArr($arrAllSub1);
			
			//Sub Array for Inserting in Table "membership"
			$arr_MembershipID	= explode("<br />",nl2br($_POST['MembershipID']));
		}
		else if($tableName == "event_registration_list") {
			$arrExmptedFields = array("submitUploadData","EventIDs");
			$allNumPosts = count($_POST);
		}
		else {
			$arrExmptedFields = array("submitUploadData");
			$allNumPosts = count($_POST);
		}

		$exmptedPosts =  count($arrExmptedFields);
		$numOfPosts = $allNumPosts - $exmptedPosts;
		$longestArrayLength = 0;
		$perviewer[] = "allNumPosts: ".$allNumPosts." <br />
			  			ExmptedPosts: ".$exmptedPosts." <br />	
			  			numOfPosts: ".$numOfPosts." <hr />";;
        
        /* Define some variables */
        $strFields = "";

		foreach($_POST as $fieldName=>$fieldValue)
		{
			$count++;
			if(in_array($fieldName,$arrExmptedFields)!=null)
			{
				continue;
			}
			else
			{
				$arrs = explode("<br />",nl2br($fieldValue));
				$arrAll[] = $arrs;
				if($numOfPosts==$count) {
					$strFields .= "".$fieldName." ";
				}
				else {
					$strFields .= "".$fieldName.", ";
				}
				$perviewer[] = "".$fieldName.": ".count($arrs)." records";
			}
		}
		
		//Get the longest array (field) length
		$arrQueries = getQueryArr($arrAll);
		
		$arrIndex = 0;
		foreach($arrQueries as $q)
		{
			if($tableName=="event_registration_list")
				$MainQuery = "insert into ".$tableName." (".$strFields.",EventID) values (".$q.",'".$_POST["EventIDs"]."')";
			else
				$MainQuery = "insert into ".$tableName." (".$strFields.") values (".$q.")";
				
			if(mysql_query($MainQuery))
			{
				$perviewer[] = OK($MainQuery);
				$insertedID = mysql_insert_id();
				//for selecting table "directory" and in case of selecting a specialty 
				if(isset($_POST["SpecialtyArr"]) && $_POST["SpecialtyArr"]!=null)
				{
					foreach($_POST["SpecialtyArr"] as $key=>$value) 
					{
						if ($value > 0) 
						{
							$SpecialtyID=$value;
							$QuerySpecialty = "insert into specialties_interests (ClientID,SpecialtyID,RecordAddByUserID,CreationDate) values('".$insertedID."','".$SpecialtyID."', '".$_COOKIE['ck_userID']."', '".date("Y-m-d h:i:s")."')";
							if(mysql_query($QuerySpecialty))
							{
								$perviewer[] = OK($QuerySpecialty);
							}
							else
							{
								$perviewer[] = NotOK("".$QuerySpecialty." <br />Error: ".$e->getMessage()."");
							}
						}
					}
				}
				if(isset($_POST['SocietyID']) && $_POST['SocietyID']!=null)
				{
					try
					{
						//Insert in table "membership"
						$MembershipID = $arr_MembershipID[$arrIndex];
						$queryMembership = "insert into membership (SocietyID,ClientID,MembershipID) values('".$_POST['SocietyID']."','".$insertedID."','".$MembershipID."')";
						if(mysql_query($queryMembership))
						{
							$MemberRecordID = mysql_insert_id();
							$perviewer[] = OK($queryMembership);
							
							//Insert in table "membership_details"
							$qMembershipDetails = "insert into membership_details (MemberRecordID, Year, Amount, InvoiceNo, InvoiceDate) values ('".$MemberRecordID."',".$arr_MembershipDetails[$arrIndex].")";
							if(mysql_query($qMembershipDetails)) {
								$perviewer[] = OK($qMembershipDetails);
							}
							else {
								$perviewer[] = NotOK($qMembershipDetails);
							}
						}
						else
						{
							$perviewer[] = NotOK($queryMembership);
						}
					}
					catch(Exception $exp)
					{
						$perviewer[] = NotOK($exp->getMessage());
					}
				}
				$arrIndex++;
			}
			else
			{
				$perviewer[] = NotOK($MainQuery." <br />".mysql_error()."");
			}
		}
		
		//Perview queries and errors
		foreach($perviewer as $record)
		{
			echo"".$record." <br />";
		}
	}
?>