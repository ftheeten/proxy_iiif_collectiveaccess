<?php
  header('Content-Type: application/json');
$image_id=$_REQUEST["image_id"];
$object_id=$_REQUEST["object_id"];

$BASE_URL="https://darwinweb.africamuseum.be/providence/pawtucket/index.php/Detail/GetMediaData/identifier/representation:".$image_id."/context/objects/object_id/".$object_id;

$find='"@id":"/providence';
$replace='"@id":"https://darwinweb.africamuseum.be/providence';
if(isset($image_id)&&isset($object_id))
{
	
   $ch = curl_init( );
    
    
    curl_setopt($ch, CURLOPT_URL,$BASE_URL);
    #curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    # Return response instead of printing.
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    # Send request.
    $response = curl_exec($ch);
    curl_close($ch);

    
	$doc = new DOMDocument();
    $doc->loadHTML($response);
	
	$xpath = new DOMXpath($doc);
	$elements = $xpath->query('//div[contains(@class,"detail")]');
	if($elements!==null)
	{
		if(count($elements)>0)
		{
			$result=trim($elements[0]->nodeValue);
			$result=str_replace($find,$replace,$result);
			print($result);
		}
	}		
}
?>