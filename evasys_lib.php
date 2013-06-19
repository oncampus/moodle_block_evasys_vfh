<?php
define('EVASYS_STUDENT_FORM', 102);
define('EVASYS_MENTOR_FORM', 76);


class Evasys_vfh
{
	public $client=false;
	 
	public function __construct($wsdl,$wsdlheader,$soapuser,$soappass,$proxy='',$proxyport=0)
	{
		$soapclient=false;
		try {
			if (($proxy!='')&&($proxyport!=0)) {
				$soapclient= new SoapClient($wsdl, array('proxy_host' => $proxy,'proxy_port' => intval($proxyport)));			
			} else {
				$soapclient= new SoapClient($wsdl);	
			}
			$ticket = $soapclient->RequestTicket($soapuser,$soappass);
			$header_input = array('Ticket' => $ticket);
			$soapHeaders = new SoapHeader($wsdlheader, 'Header', $header_input);
			$soapclient->__setSoapHeaders( $soapHeaders );
		}
		catch(Exception $e) {
			return false;
		}
		$this->client=$soapclient;
	}
	 
	function getSubunitId($institution,$program) {
		$teilbereich=array();
	$teilbereich['FHL']['MIB']=7;
	$teilbereich['FHL']['MIM']=7;
	$teilbereich['FHL']['WIG']=8;
	$teilbereich['FHK']['BWLB']=9;
	$teilbereich['FHOOW']['MIB']=10;
	$teilbereich['FHOOW']['MIM']=10;
	$teilbereich['HSEL']['MIB']=10;
	$teilbereich['HSEL']['MIM']=10;
	$teilbereich['FHBSWF']['MIB']=16;
	$teilbereich['FHBSWF']['MIM']=16;
	$teilbereich['FHBSWF']['WINFB']=16;
	$teilbereich['BHTB']['MIB']=19;
	$teilbereich['BHTB']['MIM']=19;
	$teilbereich['BHTB']['WIG']=18;
	$teilbereich['BHTB']['WINFB']=18;
	$teilbereich['FHFFM']['WIG']=21;
	$teilbereich['FHBrbg']['MIB']=17;
	$teilbereich['FHBrbg']['MIM']=17;
	$teilbereich['FHOOW']['WIG']=23;
	$teilbereich['FHOOW']['BWLB']=22;	
	$teilbereich['JHS']['WIG']=23;
	$teilbereich['JHS']['BWLB']=22;	
	$teilbereich['JH']['WIG']=23;
	$teilbereich['JH']['BWLB']=22;	
	$teilbereich['FHK']['WINFB']=9;
	$teilbereich['FHK']['IE']=24;
	$teilbereich['HSB']['MIB']=25;
	$teilbereich['FHWB']['BWLB']=27;

		$return=$teilbereich[$institution][$program];
		return $return;
	}

	function getSubunitKey($institution,$program) {
		$teilbereichkey=array();
	$teilbereichkey['FHL']['MIB']='FHL-EI';
	$teilbereichkey['FHL']['MIM']='FHL-EI';
	$teilbereichkey['FHL']['WIG']='FHL-MW';
	$teilbereichkey['FHK']['BWLB']='FHK-W';
	$teilbereichkey['FHOOW']['MIB']='HSEL-T';
	$teilbereichkey['FHOOW']['MIM']='HSEL-T';
	$teilbereichkey['HSEL']['MIB']='HSEL-T';
	$teilbereichkey['HSEL']['MIM']='HSEL-T';
	$teilbereichkey['FHBSWF']['MIB']='HAWO-I';
	$teilbereichkey['FHBSWF']['MIM']='HAWO-I';
	$teilbereichkey['FHBSWF']['WINFB']='HAWO-I';
	$teilbereichkey['BHTB']['MIB']='BHTB-IM';
	$teilbereichkey['BHTB']['MIM']='BHTB-IM';
	$teilbereichkey['BHTB']['WIG']='BHTB-WGW';
	$teilbereichkey['BHTB']['WINFB']='BHTB-WGW';
	$teilbereichkey['FHFFM']['WIG']='FHFFM-II';
	$teilbereichkey['FHBrbg']['MIB']='FHBrbg-IM';
	$teilbereichkey['FHBrbg']['MIM']='FHBrbg-IM';
	$teilbereichkey['FHOOW']['WIG']='JHS-MIT';
	$teilbereichkey['FHOOW']['BWLB']='JHS-W';		
	$teilbereichkey['JHS']['WIG']='JHS-MIT';
	$teilbereichkey['JHS']['BWLB']='JHS-W';
	$teilbereichkey['JH']['WIG']='JHS-MIT';
	$teilbereichkey['JH']['BWLB']='JHS-W';	
	$teilbereichkey['FHK']['WINFB']='FHK-W';
	$teilbereichkey['FHK']['IE']='FHK-MAW';
	$teilbereichkey['HSB']['MIB']='HSB-F2';
	$teilbereichkey['FHWB']['BWLB']='HAWO-FW';

		$return=$teilbereichkey[$institution][$program];
		return $return;
	}

	function getProgramInfo($programcode) {
		switch ($programcode) {
			case 'MIB':
				$program='MIB';
				$curriculum='2001';
				break;
			case 'MIB08':
				$program='MIB';
				$curriculum='2008';
				break;
			case 'MIM':
				$program='MIM';
				$curriculum='2004';
				break;
			case 'WIG':
				$program='WIG';
				$curriculum='2007';
				break;
			case 'IE':
				$program='IE';
				$curriculum='2008';
				break;
			case 'WINF':
				$program='WINFB';
				$curriculum='2008';
				break;
			case 'BWL':
				$program='BWLB';
				$curriculum='2009';
				break;
		}
		return array($program,$curriculum);
	}	
	
function getSubunitUser($subunit_id,$surname) {
	$return=false;
	try {
		$subunit = $this->client->GetSubunit(intval($subunit_id),'INTERNAL',true);
		if ($subunit) {
			if (is_array($subunit->m_aUsers->Users)) {
				$subusers=$subunit->m_aUsers->Users;
			} else {
				$subusers=$subunit->m_aUsers;
			}
			foreach ($subusers as $subunituser) {
				if ($subunituser->m_sSurName==$surname) {
					$return=$subunituser->m_nId;
				}
			}
		}
	}
	catch(Exception $e) {}
	return $return;
}	
	
	
	function getPeriodId($period_key) {
		$return=false;
		try {
			$periods = $this->client->GetAllPeriods();
			foreach ($periods->Periods as $period) {
				if ($period->m_sTitel==$period_key) {
					$return=$period->m_nPeriodId;
				}
			}
		}
		catch(Exception $e) {}
		return $return;
	}	
	
	public function getSubunits()
	{
		$return=false;
		try {
			$return = $this->client->GetSubunits();
		}
		catch(Exception $e) {}
		return $return;
	}

	public function getSubunit($SubunitId, $IdType='INTERNAL', $IncludeInstructors=false)
	{
		$return=false;
		try {
			$return = $this->client->GetSubunit(intval($SubunitId),$IdType, $IncludeInstructors);
		}
		catch(Exception $e) {}
		return $return;
	}

	public function GetUsersBySubunit($nSubunitId)
	{
		$return=false;
		try {
			$return = $this->client->GetUsersBySubunit($nSubunitId);
		}
		catch(Exception $e) {}
		return $return;
	}

	public function GetUser($UserId,$IdType='INTERNAL',$IncludeCourses=false,$IncludeSurveys=false,$IncludeParticipants=false)
	{
		$return=false;
		try {
			$return = $this->client->GetUser($UserId,$IdType,$IncludeCourses,$IncludeSurveys,$IncludeParticipants);
		}
		catch(Exception $e) {}
		return $return;
	}

	public function InsertUser($User)
	{
		$return=false;
		try {
			$return = $this->client->InsertUser($User);
		}
		catch(Exception $e) {}
		return $return;
	}

	public function GetCoursesByUserId($nUserId)
	{
		$return=false;
		try {
			$return = $this->client->GetCoursesByUserId($nUserId);
		}
		catch(Exception $e) {}
		return $return;
	}

	public function GetCourse($CourseId,$IdType='INTERNAL',$IncludeSurveys=false,$IncludeParticipants=false)
	{
		$return=false;
		try {
			$return = $this->client->GetCourse($CourseId,$IdType='INTERNAL',$IncludeSurveys,$IncludeParticipants);
		}
		catch(Exception $e) {}
		return $return;
	}

	public function InsertCourse($oCourse)
	{
		$return=false;
		try {
			$return = $this->client->InsertCourse($oCourse);
		}
		catch(Exception $e) {}
		return $return;
	}

	public function GetOnlineSurveyLinkByEmail($SurveyId, $EmailAddress, $AddRecipientToSurvey=true, $AutoIncreasePSWDCount=true)
	{
		$return=false;
		try {
			$return = $this->client->GetOnlineSurveyLinkByEmail(intval($SurveyId), $EmailAddress, $AddRecipientToSurvey, $AutoIncreasePSWDCount);
		}
		catch(Exception $e) {}
		return $return;
	}

	public function GetPDFReport($nSurveyId,$nUserId=0,$nCustomPDFId=0,$nLanguageID=0)
	{
		$return=false;
		try {
			$return = $this->client->GetPDFReport($nSurveyId,$nUserId,$nCustomPDFId,$nLanguageID);
		}
		catch(Exception $e) {}
		return $return;
	}

	public function GetAllPeriods()
	{
		$return=false;
		try {
			$return = $this->client->GetAllPeriods();
		}
		catch(Exception $e) {}
		return $return;
	}

	public function InsertCentralSurvey($nUserId, $nCourseId, $nFormId, $nPeriodId, $sSurveyType='o', $sNotice='')
	{
		$return=false;
		try {
			$return = $this->client->InsertCentralSurvey($nUserId, $nCourseId, $nFormId, $nPeriodId, $sSurveyType, $sNotice);
		}
		catch(Exception $e) {}
		return $return;
	}

	public function GetSurveysByCourse($nCourseId, $nFormId='', $nPeriodId='')
	{
		$return=false;
		try {
			$surveys = $this->client->GetSurveysByCourse($nCourseId, $nFormId, $nPeriodId);
			if (is_array($surveys->Surveys)) {
				$return=$surveys->Surveys;
			} else {
				$return=$surveys;
			}			
		}
		catch(Exception $e) {}
		return $return;
	}
	
	public function GetAllForms($IncludeCustomReports=false)
	{
		$return=false;
		try {
			$return = $this->client->GetAllForms($IncludeCustomReports);
		}
		catch(Exception $e) {}
		return $return;
	}	

	public function GetCourseTypes($OnlyModuleCourseTypes=false)
	{
		$return=false;
		try {
			$return = $this->client->GetCourseTypes($OnlyModuleCourseTypes);
		}
		catch(Exception $e) {}
		return $return;
	}		
	
	public function OpenSurvey($nSurveyId)
	{
		$return=false;
		try {
			$return = $this->client->OpenSurvey($nSurveyId);
		}
		catch(Exception $e) {}
		return $return;
	}		
	
	public function CloseSurvey($nSurveyId)
	{
		$return=false;
		try {
			$return = $this->client->CloseSurvey($nSurveyId);
		}
		catch(Exception $e) {}
		return $return;
	}	

	public function GetPDFPswd($SurveyId)
	{
		$return=false;
		try {
			$return = $this->client->GetPDFPswd($SurveyId);
		}
		catch(Exception $e) {}
		return $return;
	}	
	
	public function GetSurveyById($nSurveyId)
	{
		$return=false;
		try {
			$return = $this->client->GetSurveyById($nSurveyId);
		}
		catch(Exception $e) {}
		return $return;
	}		
	
	public function UpdateSurvey($oSurvey)
	{
		$return=false;
		try {
			$return = $this->client->UpdateSurvey($oSurvey);
		}
		catch(Exception $e) {}
		return $return;
	}	

	public function GetPDFQuestionnaire($FormId, $SurveyId='')
	{
		$return=false;
		try {
			$return = $this->client->GetPDFQuestionnaire($FormId, $SurveyId='');
		}
		catch(Exception $e) {var_dump($e);}
		return $return;
	}	
	
	
function getCourseByKeyAndUser($user_id,$course_key) {
	$return=false;
	try {
		$usercourses = $this->client->GetCoursesByUserId(intval($user_id));
		foreach ($usercourses as $usercourse) {
			if ($course_key==$usercourse->m_sPubCourseId) {
				$return=$usercourse->m_nCourseId;
			}
		}		
	}
	catch (Exeption $e) {}
	return $return;
}	
	
	
	public function SendEvasysMail($empfaenger,$betreff='',$inhalt='',$datei=false,$dateiname=false) {
		$id = md5(uniqid(time()));
		$kopf = "From: Evaluationsteam <do_not_reply@oncampus.de>\n";
		$kopf .= "MIME-Version: 1.0\n";
		$kopf .= "Content-Type: multipart/mixed; boundary=$id\n\n";
		$kopf .= "This is a multi-part message in MIME format\n";
		$kopf .= "--$id\n";
		$kopf .= "Content-Type: text/plain\n";
		$kopf .= "Content-Transfer-Encoding: 8bit\n\n";
		$kopf .= utf8_decode($inhalt);
		if ($datei) {
			$dateiinhalt = fread(fopen($datei, "r"), filesize($datei));
			if (!$dateiname) {
				$dateiname=basename($datei);
			}
			$kopf .= "\n--$id";
			$kopf .= "\nContent-Type: application/pdf; name=$dateiname\n";
			$kopf .= "Content-Transfer-Encoding: base64\n";
			$kopf .= "Content-Disposition: attachment; filename=$dateiname\n\n";
			$kopf .= chunk_split(base64_encode($dateiinhalt));
			$kopf .= "\n--$id--";
		}
		mail($empfaenger, $betreff, "", $kopf);
	}
	
	public function GetMailText($anrede=0, $titel=false, $nachname=false, $mailtype='umfrage',$sprache='deutsch',$coursename=false,$linkurl=false) {
		if (($nachname==false) || ($nachname=='') || ($anrede==0)) {
			$begruessung_de='Sehr geehrte Dame, sehr geehrter Herr,';
			$begruessung_en='Dear Madam, dear Sir,';
		} elseif ($anrede==1) {
			$begruessung_de='Sehr geehrter Herr ';
			if (($titel!=false)&&($titel!=''))
				{$begruessung_de.=$titel.' ';}
			$begruessung_de.=$nachname.',';
			$begruessung_en='Dear ';
			if (($titel!=false)&&($titel!=''))
				{$begruessung_en.=$titel.' ';}
			$begruessung_en.=$nachname.',';
		} else {
			$begruessung_de='Sehr geehrte Frau ';
			if (($titel!=false)&&($titel!=''))
				{$begruessung_de.=$titel.' ';}
			$begruessung_de.=$nachname.',';
			$begruessung_en='Dear ';
			if (($titel!=false)&&($titel!=''))
				{$begruessung_en.=$titel.' ';}
			$begruessung_en.=$nachname.',';
		}
		
		$mail_footer_de='Mit freundlichen Gr��en, 
		
Das Evaluationsteam';
		$mail_footer_en='Yours Sincerely,
		 
The evaluation team';		
		
		$mailbody=array();
		$mailbody['umfrage']['deutsch']=$begruessung_de.'

Sie sind hiermit zur Stimmabgabe bei einer Online-Befragung berechtigt. Bitte folgen Sie dem Link, um den Fragebogen zu �ffnen.

'.$linkurl.'

'.$mail_footer_de;
		$mailbody['umfrage']['englisch']=$begruessung_en.'
		
This email entitles you to respond to an online survey. Please follow the link to open the questionnaire.

'.$linkurl.'

'.$mail_footer_en;
		$mailbody['tans']['deutsch']=$begruessung_de.'
		
Sie erhalten im Anhang dieser E-Mail die TANs f�r Ihre Umfrage '.$coursename.'. Bitte drucken Sie diese Seite(n) nun aus. Die TANs k�nnen dann ausgeschnitten werden, um sie sp�ter nach dem Zufallsprinzip an Studenten zu verteilen.

Diese TANs gelten nur f�r diese Umfrage.
(Um die TANs darstellen zu k�nnen, mu� der Adobe Acrobat Reader auf Ihrem Rechner installiert sein.)

'.$mail_footer_de;
		$mailbody['tans']['englisch']=$begruessung_en.'
		
This email contains the passwords (PSWD) for your survey '.$coursename.'. Please print the page(s) now. The PSWDs can later be cut out to be distributed randomly to students.  These PSWDs apply only to this survey. 

Note: Adobe Acrobat Reader must be installed on your computer in order to view the files.

'.$mail_footer_en;
		$mailbody['report']['deutsch']=$begruessung_de.'

Sie erhalten hier die Ergebnisse der automatisierten Auswertung der Lehrveranstaltungsevaluation zur Veranstaltung '.$coursename.'.		
		
'.$mail_footer_de;
		$mailbody['report']['englisch']=$begruessung_en.'
		
In the attachment you will find the evaluation results of the survey '.$coursename.'.	
		
'.$mail_footer_en;
		$mailbody['reminder']['deutsch']=$begruessung_de.'

wir m�chten Sie erinnern, dass Sie zur Stimmabgabe bei einer Online-Befragung berechtigt sind. Bitte folgen Sie dem Link, um den Fragebogen zu �ffnen.

'.$linkurl.'		
		
'.$mail_footer_de;
		$mailbody['reminder']['englisch']=$begruessung_en.'
		
This email should remind you that you are entitled respond to a survey. Please follow the link to open the questionnaire.
 
'.$linkurl.'
		
'.$mail_footer_en;		
		return $mailbody[$mailtype][$sprache];
	}
		
	
	
}


?>