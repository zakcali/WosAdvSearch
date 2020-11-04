<?php
// to prevent Fatal error: Maximum execution time of 30 seconds exceeded 
set_time_limit(360); // for the line   $retrieve_response = $search_client->retrieve($retrieve_array);
header('Content-Type: text/html; charset=utf-8');
echo '<style>
mark {
  background-color: LightGreen;
  color: black;
}
</style>';

// to get different session id's from Web of Science for different users, and use them for one hour
if (isset($_COOKIE['wSID'])) $wSID = $_COOKIE['wSID'];	else {	
	$auth_url  = "http://search.webofknowledge.com/esti/wokmws/ws/WOKMWSAuthenticate?wsdl";
	try {
		$auth_client = @new SoapClient($auth_url);		
		$auth_response = $auth_client->authenticate();
		} catch (Exception $e) {
				echo $e->getMessage(), "<br>"; 
				if (strpos ($e->getMessage(), 'No matches returned for IP') !== false)
				exit("üzgünüz, web of scienca'a bağlanamıyor"); 
		}	
	$wSID = $auth_response->return;
	setcookie ('wSID',$wSID,time()+1*60*60) ; // delete cookie after one hour, to be able to get a new one
	} 
echo $wSID, " sessionID'si kullanılıyor. ";

$issnQ1Array = file("issnq1.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); //read issn codes from Q1-Q3 list
$issnQ2Array = file("issnq2.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$issnQ3Array = file("issnq3.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$issnQ4Array = file("issnq4.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$issnAHArray = file("issnahci.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$eissnQ1Array = file("eissnq1.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$eissnQ2Array = file("eissnq2.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$eissnQ3Array = file("eissnq3.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$eissnQ4Array = file("eissnq4.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$eissnAHArray = file("eissnahci.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$sortfield='PY';
$sortorder='D';
$wosQuery='';
$printRecordNumber=FALSE;
$printLinks=FALSE;
$printnAuthors=FALSE;
$printQ13Group= FALSE;
$printOnlyQ13= FALSE;
$printDocType= FALSE;
$qGroup='';
// create timespanBegin, check if year is entered, month is entered, and day is entered
$i=0;
if (""== trim($_POST['year1'])) $year1='1968'; 
	else {$year1= $_POST['year1']; $i++; }
if (""== trim($_POST['month1'])) $month1='01';  
	else { $month1= $_POST['month1']; $i++; } 
if (""== trim($_POST['day1']))   $day1='01'; 
	else { $day1= $_POST['day1']; $i++; } 
if ($i==0) {$timespanBegin='1968-01-01';} 
	else {$timespanBegin= $year1."-".$month1."-".$day1;}

// create timespanEnd, check if year is entered, month is entered, and day is entered
$i=0;
if (""== trim($_POST['year2'])) $year2='2030'; 
	else {$year2= $_POST['year2']; $i++; }
if (""== trim($_POST['month2'])) $month2='12';  
	else { $month2= $_POST['month2']; $i++; } 
if (""== trim($_POST['day2']))   $day2='31'; 
	else { $day2= $_POST['day2']; $i++; } 
if ($i==0) {$timespanEnd='2030-12-31';} 
	else {$timespanEnd= $year2."-".$month2."-".$day2;}

if (isset($_POST['sortfield'])) $sortfield= $_POST['sortfield'];
if (isset($_POST['sortorder'])) $sortorder= $_POST['sortorder'];
if (isset($_POST['wosQuery'])) $wosQuery= $_POST['wosQuery'];
if (isset($_POST['prrecnum'])) $printRecordNumber= TRUE; // display record numbers on printout
if (isset($_POST['prlinks'])) $printLinks= TRUE; // display WOS, citation and doi links on output
if (isset($_POST['nofauthors'])) $printnAuthors=TRUE;
if (isset($_POST['articletype'])) $printDocType=TRUE;
if (isset($_POST['onlyarticles'])) $printArticles=TRUE;
if (isset($_POST['prq13'])) $printQ13Group= TRUE; // display Q1, Q2, Q3 group
if (isset($_POST['pronlyq13'])) $printOnlyQ13= TRUE; // only display if Q<4
if (""== trim($_POST['wosQuery'])) exit("sorgu metni bulunamadı, sorgulama yapılamadı"); 

$search_url = "http://search.webofknowledge.com/esti/wokmws/ws/WokSearchLite?wsdl";
$search_client = @new SoapClient($search_url);
$search_client->__setCookie('SID',$wSID);
$search_array = array(
  'queryParameters' => array(
    'databaseId' => 'WOS',
    'userQuery' => $wosQuery,
    'editions' => array(
      array('collection' => 'WOS', 'edition' => 'SCI'),
      array('collection' => 'WOS', 'edition' => 'SSCI'),
	  array('collection' => 'WOS', 'edition' => 'AHCI')
    ),
	'timeSpan' => array('begin' => $timespanBegin, 'end' => $timespanEnd),
	'queryLanguage' => 'en'
  ),
  'retrieveParameters' => array(
    'count' => '1',
    'firstRecord' => '1',
  )
);


try{
  $search_response = $search_client->search($search_array);
} catch (Exception $e) {  
    echo $e->getMessage(),"<br>";
	exit("Bir hata oldu, sorgulama yapılamıyor"); 
}

$resp =(json_decode(json_encode($search_response->return), true));
$n = (int)$resp['recordsFound']; //total number of records to be returned

if ($n==0) exit("kayıt bulunamadı"); //no records
// PERFORM a Retrieve operation, by using queryId
$queryId=$resp['queryId'];
// echo "Performing retrieve operation by using queryId: ", $queryId, "<br>";
echo $timespanBegin, " / ", $timespanEnd, " arasında SCI-E, SSCI, AHCI dizinlerince taranan Q1,Q2,Q3,Q4 grubu ";
echo $n, " kayıt bulundu. <br> <br>";
echo "<hr>"; // thematic change
$retBase = 1; //first record to be retrieved
$retCount= $n; // total number of records to retrieved
$recNumber=0; // record number to be printed
$hundredNumber = (int) ($n / 100);
for ($pageNumber=$hundredNumber; $pageNumber>0; $pageNumber--) {
	retrievePage ($retBase, 100); 
	sleep (1);  // to prevent wos server throttle
	$retBase+=100;
	$retCount-=100; 
	}
retrievePage ($retBase, $retCount);

function retrievePage ($firstRec, $recCount) {
global $queryId, $search_client, $recNumber, $sortfield, $sortorder, $printRecordNumber, $printLinks, $printnAuthors, $printQ13Group, $printOnlyQ13, $printDocType, $printArticles;
global $issnQ1Array, $issnQ2Array,$issnQ3Array, $issnQ4Array,$issnAHArray, $eissnQ1Array, $eissnQ2Array, $eissnQ3Array, $eissnQ4Array,$eissnAHArray, $qGroup;
$preArticle= 'http://gateway.webofknowledge.com/gateway/Gateway.cgi?GWVersion=2&SrcApp=PARTNER_APP&SrcAuth=LinksAMR&KeyUT=';
$postArticle = '&DestLinkType=FullRecord&DestApp=ALL_WOS';
$preCitation = 'http://gateway.webofknowledge.com/gateway/Gateway.cgi?GWVersion=2&SrcApp=PARTNER_APP&SrcAuth=LinksAMR&KeyUT=';
$postCitation = '&DestLinkType=CitingArticles&DestApp=ALL_WOS';
$prefArticle= 'https://gateway.webofknowledge.com/gateway/Gateway.cgi?GWVersion=2&SrcApp=Publons&SrcAuth=Publons_CEL&KeyUT=';
$postfArticle = '&DestLinkType=FullRecord&DestApp=WOS_CPL';
$prefCitation = 'https://gateway.webofknowledge.com/gateway/Gateway.cgi?GWVersion=2&SrcApp=Publons&SrcAuth=Publons_CEL&KeyUT=';
$postfCitation = '&DestLinkType=CitingArticles&DestApp=WOS_CPL';

$retrieve_array = array(
	'queryId' => $queryId,
	'retrieveParameters' => array(
    'count' => $recCount,
    'sortField' => array(
      array('name' => $sortfield, 'sort' => $sortorder)
    ),
	'viewField' => array(
      array('fieldName' => 'name',
			'fieldName' => 'title')
    ),
    'firstRecord' => $firstRec
  )
);
try{
  $retrieve_response = $search_client->retrieve($retrieve_array);
} catch (Exception $e) {  
    echo $e->getMessage(),"<br>";
	exit("Bir hata oldu, sorgulama yapılamıyor"); 
}
$resp =(json_decode(json_encode($retrieve_response->return), true));
// print_r ($resp) ;  // for debugging response text

for ($a = 0; $a < count($resp['records']); $a++) {
	if (array_key_exists(0, $resp['records']) == TRUE ) 
		 { $onerecord = $resp['records'][$a]; }// iterate multi record array
	else { $onerecord = $resp['records']; $a = count($resp['records']);} // there is only one article, print only once
	
	
	for ($i=0; $i < count ($onerecord['other']); $i++) { // if issn or eissn number is in Q1-Q3 files, print Q status
			if ($onerecord['other'][$i]['label'] == "Identifier.Issn") {
			$docIssn=$onerecord['other'][$i]['value'];
			$qGroup=' ??';
				if (in_array($docIssn, $issnQ1Array)) { $qGroup= " Q1";}
				else if (in_array($docIssn, $issnQ2Array)) { $qGroup= " Q2";}
				else if (in_array($docIssn, $issnQ3Array)) { $qGroup= " Q3";}
				else if (in_array($docIssn, $issnQ4Array)) { $qGroup= " Q4";}
				else if (in_array($docIssn, $issnAHArray)) { $qGroup= " AH";}
				
				}
			else if ($onerecord['other'][$i]['label'] == "Identifier.Eissn") {
			$docEissn=$onerecord['other'][$i]['value'];
				if (in_array($docEissn, $eissnQ1Array)) { $qGroup= " Q1";}
				else if (in_array($docEissn, $eissnQ2Array)) { $qGroup= " Q2";}
				else if (in_array($docEissn, $eissnQ3Array)) { $qGroup= " Q3";}
				else if (in_array($docEissn, $eissnQ4Array)) { $qGroup= " Q4";}
				else if (in_array($docEissn, $eissnAHArray)) { $qGroup= " AH";}
				}
			}
//	find article type and article Access if exists
	$docAccess='';
	if (is_array ($onerecord['doctype']['value']))  {
		$docType = $onerecord['doctype']['value'][0]; // Doctypes are grouped as arrays
		$docAccess = ";".$onerecord['doctype']['value'][1];
	}
	else $docType = $onerecord['doctype']['value']; // Doctypes are not grouped as arrays		
// end of print article type
$showPublication = (!$printOnlyQ13)|| ($printOnlyQ13 && ($qGroup != " Q4" && $qGroup != " ??"));
	if ($printArticles == TRUE && $docType != 'Article') // if user wants to show Articles only
			$showPublication = FALSE ;	// then don't print other publications 	
//***************************start of print loop ************************
if ($showPublication) { // ticked Q1-Q2-Q3 OR ALL
	++$recNumber; // skip number if not wanted to be displayed
	echo '<span style="background-color: #DCDCDC">'; // change background text color for articles
// https://htmlcolorcodes.com/color-names/
	if ($printRecordNumber) { // print record number bold 
		echo "<b>"; print $recNumber; echo"- "; echo "</b>";
		}
	if (array_key_exists(0, $onerecord['authors']) == TRUE )  	 	
		$authorArray = $onerecord['authors'][0]; // Authors are grouped as Authors and GroupAuthors arrays
	else $authorArray = $onerecord['authors']; // Authors are not grouped as Authors and GroupAuthors arrays
	
	if (count ($authorArray['value']) == 1 ) print_r ($onerecord['authors']['value']);   //one outhor only
		else	{ 
		for ($i=0; $i < count ($authorArray['value']); $i++) { print_r ($authorArray['value'][$i]); 	
		if ($i != count ($authorArray['value'])-1) echo "; "; } // dont print ; after last author
		}  
	echo ". "; // a dot after last author
	echo "<mark  style=\"color:black;\">"; // mark text for: title

	print_r ($onerecord['title']['value']); echo ". ";
	echo "</mark>"; // restore title color
	for ($i=0; $i < count ($onerecord['source']); $i++) { // name of Source
		if ($onerecord['source'][$i]['label'] == "SourceTitle") { print_r ($onerecord['source'][$i]['value']); echo " "; }
		}
	for ($i=0; $i < count ($onerecord['source']); $i++) {	
	if ($onerecord['source'][$i]['label'] == "Published.BiblioYear") { 
		echo "<mark  style=\"color:DarkRed;\">"; // mark and color text for: years
		print_r ($onerecord['source'][$i]['value']);}
		 echo "</mark>"; // restore text color
		}
	echo ";";
	for ($i=0; $i < count ($onerecord['source']); $i++) { 
	if ($onerecord['source'][$i]['label'] == "Volume") { print_r ($onerecord['source'][$i]['value']);}
		}
	for ($i=0; $i < count ($onerecord['source']); $i++) { 
	if ($onerecord['source'][$i]['label'] == "Issue") {  echo "("; print_r ($onerecord['source'][$i]['value']); echo "):"; }
		}
	for ($i=0; $i < count ($onerecord['source']); $i++) { 
	if ($onerecord['source'][$i]['label'] == "Pages") { print_r ($onerecord['source'][$i]['value']); echo ". "; }
		}
	if ($printLinks)  { // print WOS, citation, doi links if user wants so
	echo "<br>"; 
		$articleLink= $preArticle . $onerecord['uid'] .$postArticle  ;
		echo '<a href="' , $articleLink , '" target="_blank">', $onerecord['uid'], '</a>' ; 	echo '&nbsp;&nbsp;';
		
		$citationLink=  $preCitation. $onerecord['uid'] . $postCitation;
		echo '<a href="', $citationLink , '" target="_blank">WOS da atıflar</a>'; echo '&nbsp;&nbsp;';

		$articleLink= $prefArticle. $onerecord['uid'] .$postfArticle ;
		echo '<a href="' , $articleLink , '" target="_blank">', 'WOS (ULAKBIM dışından)</a>' ; 	echo '&nbsp;&nbsp;';
		
		$citationLink=  $prefCitation. $onerecord['uid'] . $postfCitation ;
		echo '<a href="', $citationLink , '" target="_blank">WOS da atıflar (ULAKBIM dışından)</a>'; echo '&nbsp;&nbsp;';
		
		for ($i=0; $i < count ($onerecord['other']); $i++) { 
			if ($onerecord['other'][$i]['label'] == "Identifier.Doi" or 
				$onerecord['other'][$i]['label'] == "Identifier.Xref_Doi") { 
			$doiLink='https://doi.org/'.$onerecord['other'][$i]['value'];
			echo '<a href="', $doiLink,'" target="_blank">','DOI:',$onerecord['other'][$i]['value'],'</a>' ;
				}
			}
	} // end of create links
	if ($printnAuthors) {echo '&nbsp;&nbsp;','Yazar sayısı = ', count ($authorArray['value']); }
	
	if ($printQ13Group && $qGroup) {echo '&nbsp;&nbsp;', $qGroup; } // if Q1-Q3 box is checked in the main program AND Q<4
	if ($printDocType) print_r (" ".$docType.$docAccess);
			
//	echo '<br>';
 echo '</span>'; // change background color of texts
 echo '<hr>'; // thematic change
}///*****end of print loop, display Q group, or only display Q1, Q2, Q3
	}
// print_r ($resp);

}

?>