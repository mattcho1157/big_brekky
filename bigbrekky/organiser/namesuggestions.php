<?php
// get the name parameter from URL (text input)
$name = $_REQUEST['name'];
$suggestedNames = '';

//if name input isn't empty
if ($name !== "") {
	//retrieve 5 student names containing $name string in alphabetical order
	require('../globalfuncs.php');
	connectDB();
	$possibleStudents = DB::query('select username, concat(fname, " ", lname) as name from users where concat(fname, " ", lname) like "'.$name.'%" and usertype = "s" order by name limit 5');
	//concatenate each possible student's name onto $suggestedNames as a link
	foreach ($possibleStudents as $student) {
		//pass a GET variable (username) that'll be needed for displaying student's profile
		$suggestedNames .= '<a class="namesuggestions" href="students.php?username='.$student['username'].'">'.$student['name'].'</a>';
		if ($suggestedNames !== '') {
			//add line break to display suggested names in rows
			$suggestedNames .= '<br>';
		}
	}
}

//output 'No Suggestion' if no suggestion was found or output correct names 
echo $suggestedNames == '' ? '<a class="namesuggestions" id="nosuggestion">No Suggestion</a>' : $suggestedNames;
?>