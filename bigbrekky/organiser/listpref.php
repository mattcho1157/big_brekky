<?php
// get the week & day parameters from URL (select input)
$week = $_REQUEST['week'];
$day = $_REQUEST['day'];
$listpref = '';

//connect to database
require('../globalfuncs.php');
connectDB();

//display preferences in table
$listpref .= '<div class="table-responsive"><table class="table table-hover preflist-table">';

//iterate through preference numbers 1 to 5
for ($prefnum=1; $prefnum<=5 ; $prefnum++) { 
	//create new row in table for each preference number
	$listpref .= '<tr><th>Preference '.$prefnum.'</th>';
	//query database for every student prefence with the correct prefnum, week, day
	$pref = DB::query('select concat(upper(u.lname), " ", u.fname) as name from users u left join preferences p on p.username = u.username where p.prefnum = %s and p.week = %s and p.day = %s order by u.lname', $prefnum, $week, $day);
	
	//if at least one student preference exists for that prefnum, week, day
	if ($pref) {
		//turn 2d array into 1d arry and separate names by comma
		$prefSeparatedByComma = implode(', &nbsp', array_column($pref, 'name'));
		$listpref .= '<td>'.$prefSeparatedByComma.'</td>';
	} else {
		$listpref .= '<td>NONE</td>';
	}
	$listpref .= '</tr>';
}
$listpref .= '</table></div>';

//output student preferences table
echo $listpref;
?>