<?php

include '../../classes/Validator.php';
include '../../includes/Authenticate.php';
include '../../compiler/Compiler.php';
include '../../classes/student.php';

if (!Authenticate::isLoggedIn())
{
	Authenticate::logout();
}
//protects the student section
if (Authenticate::getUserType() == "ADMIN")
{
	Authenticate::redirect();
}
	if (!empty($_POST['sourcecode']))
	{
		
		$sourceCode = ($_POST['sourcecode']);
		$language = $_POST['language'];
		//retrieve the number of test cases
		$queryResult = Validator::getTestCases($_GET['qid']);
		$isSample = Validator::getIsSample($queryResult);
		$result = Validator::validateCode($sourceCode,$language,$queryResult);

//		$memory           = $result->getMemory();
//		$time             = $result->getTime();
//		$output           = $result->getOutput();
//		$error            = $result->getError();
//		$output           = $result->getOutput();
//		$compileMessage   = $result->getCompileMessage();
						
						
								
								
						
	}
	else
	{
		$qstring = "gnooble.org/student/practice/validatecode.php?id=".$_GET['qid'];
		header($qstring);
		exit();
	
	}

	// algorithm 1: show the output,compilation message,memory and time needed by his program to the user
	 //make sure when he traverses he has his source code into the page.
	//get an array of booleans
	$isPassed = $result->isPassed();
	$jsonOutput = array();
	$areAllPassed = true;
    $flag_compile_message = true;
    $compilerOutput = array();
	for( $index = 0 ; $index < count($isPassed); $index++)
		{
			if ($isPassed[$index] == "Failed")
				$areAllPassed = false;

			$statusEachTestCase = array(

				"isPassed" => $isPassed[$index],
				"time" =>  $result->getTime($index),
				"memory" => $result->getMemory($index),
				"error" => $result->getError($index),
				"sample" =>$isSample[$index],
				"message" => $result->getMessage($index),
				"outputTestCase" => $result->getOutputCase($index),
				"stderror" => $result->getError($index)

				);
			$jsonOutput[$index] = $statusEachTestCase;

		}

		$compilerOutput["compilationError"] = $result->getCompileMessage();
		$compilerOutput["compilationResult"] = $jsonOutput;

		if ($areAllPassed)
		{
			
			  $status = 'All Test Cases Passed!';

			  //check whether the user has already solved the question
			  //get the time
				date_default_timezone_set('Asia/Kolkata');
				$solvedTime = date('Y-m-d H:i:s');
			    $isSolved = Student::isSolvedQuestion($_SESSION['userid'],$_GET['qid']);

			// if the user hasn't solved the question then update the scoreboard

				if (!$isSolved)
					Student::updateMyScoreBoard($_GET['qid'], $_SESSION['userid'],"Solved",$sourceCode,$solvedTime);

		}
		else
		{

			$status = 'All Test Cases Failed!';
		}

	//$jsonOutput[count($jsonOutput)] = array ("status" => $status);



	//echo json_encode($json_output);
	//echo json_encode($jsonOutput);
     echo json_encode($compilerOutput);
	
	
	 ?>
	

