<?php

$user='vkthakur' ;
$pass='api';
$run = true;
$private = false;
$code='';
$input='';

//echo "Hello you reached here 1" ;

$subStatus = array(

			0 =>'Not Running', 
			1=>'Compiled',
			3=>'Running',
			11=>'Compilation Error',
			12=>'Runtime Error',
			13=>'Time Limit Exceeded',
			15=>'Success',
			17=>'Memory Limit Exceeded',
			19=>'Illegal system call',
			20=>'Internal Error');

$error = array(
			'OK'=>'Everything went ok',
			'AUTH_ERROR'=>'User name or user password are invalid',
			'PASTE_NOT_FOUND'=>'Submission with a specified link could not be found',
			'WRONG_LANG_ID'=>'Language with a specified id does not exist',
			'ACCESS_DENIED'=>'Access to the resource is denied for the specified user',
			'CANNOT_SUBMIT_THIS_MONTH_ANYMORE'=>'You have reached a monthly limit',
			'...'=>'Other error codes will be added in the future' );

    $lang = isset( $_POST['lang'] ) ? intval( $_POST['lang'] ) : 1;
    $input = trim( $_POST['input'] );
    $code = trim( $_POST['source'] );

   //echo "Hello you reached here 2" ;


$client = new SoapClient( "http://ideone.com/api/1/service.wsdl" );

$result = $client->createSubmission($user,$pass,$code,$lang,$input,$run,$private);

	//echo "Hello you reached here 3" ;

	//echo $result['error'];

if($result['error']=='OK')	{

	//echo "Hello you reached " ;
	// here we have to extract the link..
	$status =$client->getSubmissionStatus($user,$pass,$result['link']);
		if($status['error']=='OK')	{

			//echo "Hello you reached here 4" ;


			while($status['status']!=0)	{
				sleep(5);
				$status =$client->getSubmissionStatus($user,$pass,$result['link']);
			}
			// submission details.. :)
		$details=$client->getSubmissionDetails($user,$pass,$result['link'],true,true,true,true,true);	
		
		if($details['error']=='OK'){

			//echo "Hello you reached here 5" ;


			if($details['status'] < 0)	{
				$status='Waiting For Complilation';
			}
			else {
				 $k=$details['result'];
				 $status = $subStatus[$k];
			}
		
		$answer = array(
						'status' =>$status,
						'source' =>$details['source'],
						'input' =>$details['input'],
						'output' =>$details['output'],
						'cmpinfo' =>$details['cmpinfo'],
						'memory' =>$details['memory'] 
						);

		//echo "Hello you reached here 6" ;

		echo "Status is:";
		echo "<br />";
		echo $answer['status'];

		if($k==15)	{

			echo "<br />";

			echo "<br />Source is:";
			echo "<br />";
			echo $answer['source'];

			echo "<br />";

			echo "<br />Input is:";
			echo "<br />";
			echo $answer['input'];

			echo "<br />";

			echo "<br />Output is:";
			echo "<br />";
			echo $answer['output'];

			}
		}
		else
		{
			 //if($details['error']=='OK')
			 echo $error[$details['error']];
		}
	
		}
		else
		{
			//if(status['error']=='OK')
			echo $error[$status['error']];
		}

	}
	else
	{
		//if($result['error']=='OK')
		echo $error[$result['error']];
	}
?>

