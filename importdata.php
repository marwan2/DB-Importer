<?php	
function mysql_tables($database='')
{
    $tables = array();
    $list_tables_sql = "SHOW TABLES FROM {$database};";
    $result = mysql_query($list_tables_sql);
    if($result)
    while($table = mysql_fetch_row($result))
    {
        $tables[] = $table[0];
    }
    return $tables;
}
	function getQueryString($except)
	{
        $URLVars = "";
		foreach($_GET as $keyname => $value)
		{
			if($keyname!=$except)
				$URLVars .= "&".$keyname."=".$value."";
		}
		return $URLVars;
	}
	
	$s_nm 	= "Server Name or URL";
	$s_user  = "Server username";
	$s_pw    = "Server password";
	$db 	  = "Database name";
	
	if(isset($_GET['s_nm'])) {
		$s_nm = $_GET['s_nm'];
	}
	if(isset($_GET['s_user'])) {
		$s_user = $_GET['s_user'];
	}
	if(isset($_GET['s_pw'])) {
		$s_pw = $_GET['s_pw'];
	}
	if(isset($_GET['db'])) {
		$db = $_GET['db'];
		mysql_connect($s_nm, $s_user, $s_pw) or die(mysql_error());
        	mysql_select_db($db) or die(mysql_error());
	}
	$msg = "";	
	if(isset($_POST['submitInsert']))
	{
		$query = $_POST['query'];
		try
		{
			if(mysql_query($query))
			{
			}
			else
			{
				$msg = "Error: ".mysql_error()."";
			}
		}
		catch(Exception $exp)
		{
			$msg = "Error while executing query ".$query." <br />Error Message: ".$exp->getMessage()."";
		}
	}
?>
<script type="text/javascript">
function countLines(textarea,label)
{
  var area = document.getElementById(textarea);        // trim trailing return char if exists
  var text = area.value.replace(/\s+$/g,"");
  var splitT = text.split("\n");
  document.getElementById(label).innerHTML = "( "+splitT.length+" )";
}
</script>
<script type='text/javascript'> function TxtBoxOn(txtID, msg) { var txtVal = txtID.value; if(txtVal == msg) { txtID.value = ''; } else { txtID.value = txtVal; txtID.select(); } txtID.style.color = '#303030'; txtID.style.fontStyle='normal'; } function TxtBoxOut(txtID, msg) { var txtVal = txtID.value; if(txtVal == msg) { txtID.value = msg; } else if(txtVal == '') { txtID.style.color = '#a9a9a9'; txtID.style.fontStyle='italic'; txtID.value = msg; } else { txtID.value = txtVal; } } </script>
<script type='text/javascript'> function validateForm() { do { var Field_0; Field_0=document.getElementById('s_nm').value; if((Field_0.length==0) || (Field_0.charAt(0)==' ') || (Field_0=='Server Name or URL')) { alert('Please enter your Server'); document.getElementById('s_nm').value=''; document.getElementById('s_nm').focus(); document.getElementById('s_nm_label').style.color='#EA9602'; return false; break; }	else { document.getElementById('s_nm_label').style.color='#000000'; }var Field_1; Field_1=document.getElementById('s_user').value; if((Field_1.length==0) || (Field_1.charAt(0)==' ') || (Field_1=='Username (like: root)')) { alert('Please enter your Username'); document.getElementById('s_user').value=''; document.getElementById('s_user').focus(); document.getElementById('s_user_label').style.color='#EA9602'; return false; break; }	else { document.getElementById('s_user_label').style.color='#000000'; }var Field_2; Field_2=document.getElementById('s_pw').value; if((Field_2.length==0) || (Field_2.charAt(0)==' ') || (Field_2=='Password (like: root)')) { alert('Please enter your Password'); document.getElementById('s_pw').value=''; document.getElementById('s_pw').focus(); document.getElementById('s_pw_label').style.color='#EA9602'; return false; break; }	else { document.getElementById('s_pw_label').style.color='#000000'; }var Field_3; Field_3=document.getElementById('db').value; if((Field_3.length==0) || (Field_3.charAt(0)==' ') || (Field_3=='Enter Database Name')) { alert('Please enter your Database'); document.getElementById('db').value=''; document.getElementById('db').focus(); document.getElementById('db_label').style.color='#EA9602'; return false; break; }	else { document.getElementById('db_label').style.color='#000000'; } } while(0) } </script>
<style>
	legend { font:bold 16px Arial, Helvetica, sans-serif; color:#C00; }
	a { color:#00C; }
	textarea { width:250px; line-height:18px; background:url(images/inverse_bg.jpg); border:1px solid #c0c0c0; margin:0px; padding:0px; }
	label { font-weight:bold; color:#666 }
	label:hover { color:#000; }
	.selectedField { font-weight:bold; color:#F60; }
	input [type="submit"] { cursor:pointer; }
	.selectOption { font-size:11px; padding:1px; width:280px; }
</style>
<div id="cp_body">
<?php
	echo"<table><tr><td valign='top' nowrap='nowrap'><fieldset><legend>Step 1: Database Connection</legend><form action='".$_SERVER['REQUEST_URI']."' method='get' onSubmit='return validateForm()'><table border='0'><tr><td><label for='s_nm' id='s_nm_label'>Server <span style='font:bold 14px Arial, Helvetica, sans-serif; color:#F00;'>*</span></label></td><td><input type='text' name='s_nm' id='s_nm' value='".$s_nm."' onfocus=\"TxtBoxOn(s_nm, '".$s_nm."')\" onblur=\"TxtBoxOut(s_nm, '".$s_nm."')\" style='color:#999999; font-style:italic; ' /></td></tr><tr><td><label for='s_user' id='s_user_label'>Username <span style='font:bold 14px Arial, Helvetica, sans-serif; color:#F00;'>*</span></label></td><td><input type='text' name='s_user' id='s_user' value='".$s_user."' onfocus=\"TxtBoxOn(s_user, '".$s_user."')\" onblur=\"TxtBoxOut(s_user, '".$s_user."')\" style='color:#999999; font-style:italic;' /></td></tr><tr><td><label for='s_pw' id='s_pw_label'>Password <span style='font:bold 14px Arial, Helvetica, sans-serif; color:#F00;'>*</span></label></td><td><input type='text' name='s_pw' id='s_pw' value='".$s_pw."' onfocus=\"TxtBoxOn(s_pw, '".$s_pw."')\" onblur=\"TxtBoxOut(s_pw, '".$s_pw."')\" style='color:#999999; font-style:italic; ' /></td></tr><tr><td><label for='db' id='db_label'>Database <span style='font:bold 14px Arial, Helvetica, sans-serif; color:#F00;'>*</span></label></td><td><input type='text' name='db' id='db' value='".$db."' onfocus=\"TxtBoxOn(db, '".$db."')\" onblur=\"TxtBoxOut(db, '".$db."')\" style='color:#999999; font-style:italic; ' /></td></tr><tr><td></td><td><input type='submit' value='Go' /></td></tr></table></form></fieldset><br />	<fieldset><legend>Step 2: Select Table</legend><ul style='list-style-type:none; padding:5px; margin:5px;'>";
	//Get database tables
	if(isset($_GET['db']))
	{
		try
		{
			//$tables = mysql_list_tables($db);
			//$tables = mysql_query("SHOW TABLES FROM ".$db."");
			$tables = mysql_tables($db);
			//var_dump($db);
			$count = 0;
			while ($count < count($tables))
			{
				//$table = mysql_tablename($tables,$count);
				$table = $tables[$count];
				if(isset($_GET['table']) && $_GET['table']==$table)
					echo"<li><a href='importdata.php?".getQueryString('table')."&table=".$table."' style='background:#006699; color:#fff;'><strong>".$table."</strong></a></li>";
				else
					echo"<li><a href='importdata.php?".getQueryString('table')."&table=".$table."'>".$table."</a></li>";
				$count++;
			}
		}
		catch(Exception $exp)
		{
			echo"<li>Error: ".$exp->getMessage()."</li>";
		}
		echo"</ul>";
	}
	echo"</fieldset>";
	echo"</td><td valign='top' nowrap='nowrap'><fieldset><legend>Step 3: Select Fields</legend><form action='".$_SERVER['REQUEST_URI']."' method='post'>";
	
	//Get selected table columns list
	if(isset($_GET['table']))
	{
		$query   = mysql_query("select * from ".$_GET['table']."");
		if(mysql_num_rows($query)>0)
		{
			$row 	 = mysql_fetch_assoc($query);
			$columns = array_keys($row);
			foreach($columns as $key=>$val)
			{
				if(isset($_POST['submitBtn']))
				{
					if($_POST["fieldsIndex"]!=null)
					{
						if(in_array($val,$_POST["fieldsIndex"])!=null) {
							echo"<label><input type='checkbox' value='".$val."' name='fieldsIndex[]' checked='checked' /><span class='selectedField'>".$val."</label><br />";
						}
						else {
							echo"<label><input type='checkbox' value='".$val."' name='fieldsIndex[]' />".$val."</label><br />";
						}
					}
					else {
						echo"<label><input type='checkbox' value='".$val."' name='fieldsIndex[]' />".$val."</label><br />";
					}
				}
				else
				{
					echo"<label><input type='checkbox' value='".$val."' name='fieldsIndex[]' />".$val."</label><br />";
				}
			}
			echo"<input type='submit' name='submitBtn' value=' Create textboxes ' />";
		}
		else
		{
			//If the selected table has no data, its fields will not appear
			echo"".$msg." <form action='".$_SERVER['REQUEST_URI']."' method='post'><label>Table is empty 
				<br />please insert at least one record to be able to view its fields <br />
				<textarea name='query'>insert into ".$_GET['table']." () values()</textarea></label><br />
				<input type='submit' name='submitInsert' value='Run query' /></form>";
		}
	}
	echo"</form></fieldset></td><td valign='top'><fieldset><legend>Step 3: Insert Data</legend>";
	echo"<form action='_ImportData.php?".getQueryString('')."' method='post' target='txtareaFrame'>";
	//Get the selected columns (fields) after click on submit button
	if(isset($_POST['submitBtn']))
	{
		if($_POST["fieldsIndex"]!=null)
		{
			foreach($_POST["fieldsIndex"] as $key => $value) 
			{
				echo"<label><b>".$value."</b> <label id='lbl_".$value."'></label><br /><textarea name='".$value."' onkeyup=\"countLines('".$value."','lbl_".$value."')\" onchange=\"countLines('".$value."','lbl_".$value."')\" id='".$value."'></textarea></label> <hr />";
			}
		}
		
		//if table "directory" selected
		if($_GET['table']=="directory")
		{
			echo"<fieldset><legend>In case of selecting table (directory)</legend>";
			$querySocieties = mysql_query("select * from societies_list where IsDeleted='0'");
			if(mysql_num_rows($querySocieties)>0)
			{
				echo"<br /><strong>Society Membership:</strong> <select style='font-size:11px; padding:1px; width:280px;' name='SocietyID'><option></option>";
				while($row = mysql_fetch_array($querySocieties))
				{
					echo"<option value='".$row['SocietyID']."'>".$row['SocietyName']."</option>";
				}
				echo"</select> ";
				$arrSub = array("MembershipID","Year","Amount","InvoiceNo","InvoiceDate");
				foreach($arrSub as $subField)
				{
					echo"".$subField." <label id='lbl_".$subField."'></label><br /><textarea name='".$subField."' id='".$subField."' onkeyup=\"countLines('".$subField."','lbl_".$subField."')\" onchange=\"countLines('".$subField."','lbl_".$subField."')\"></textarea><hr />";
				}
			}

			$querySpecialties = mysql_query("select * from specialties_list order by Title asc");
			$numRows = mysql_num_rows($querySpecialties);
			$countSp=0;
			if(mysql_num_rows($querySpecialties)>0)
			{
				echo"<strong>Specialty:</strong><table><tr><td valign='top' nowrap='nowrap'>";
				while($row=mysql_fetch_array($querySpecialties))
				{
					if(round($numRows/2) == ($countSp+1)){
						echo"<label><input type='checkbox' title='".$row['SpecialtyID']."' name='SpecialtyArr[]' value='".$row['SpecialtyID']."' />".$row['Title']."</label><br /></td><td valign='top' nowrap='nowrap'>";
					}
					else {
						echo"<label><input type='checkbox' title='".$row['SpecialtyID']."' name='SpecialtyArr[]' value='".$row['SpecialtyID']."' />".$row['Title']."</label><br />";
					}
					$countSp++;
				}
				echo"</tr></table></fieldset>";
			}
		}
		
		//if table "event_registration_list" selected
		if($_GET['table']=="event_registration_list")
		{
			$queryEvents = mysql_query("select * from events");
			if(mysql_num_rows($queryEvents)>0)
			{
				echo"<strong>Select Event:</strong><br /><select class='selectOption' name='EventIDs'><option></option>";
				while($rowEvent = mysql_fetch_array($queryEvents))
				{
					echo"<option value='".$rowEvent['EventID']."'>".$rowEvent['EventTitle']."</option>";
				}
				echo"</select>";
			}
		}
		echo"<br /><input type='submit' value='Upload data' name='submitUploadData' />";
	}

	echo"</form></fieldset></td>";
	
	// Get the data from textarea selected from fields by Ajax in the file (_ImportData.php)
	echo"<td valign='top'><fieldset><legend>Step 4: Upload Data</legend><iframe name='txtareaFrame' id='txtareaFrame' width='300' height='500' frameborder='0'></iframe></fieldset></td>";
	echo"</tr></table>";
?>
</div>