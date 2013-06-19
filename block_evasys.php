<?php
require_once('evasys_lib.php');

class block_evasys extends block_base {
	
	public function init() {
		$this->title = get_string('default_title', 'block_evasys');
	}
	
	public function get_content() {
		Global $USER, $SESSION, $CFG, $DB;
		$id = required_param('id', PARAM_INT);
		$course = $DB->get_record('course', array('id'=>$id));
		
		if ($this->content !== null) {
			return $this->content;
		}
		$this->content =  new stdClass;
		$this->content->text='';
		
		$global_wsdl=get_config('evasys','wsdl');
		$wsdl  = (!empty($global_wsdl)) ? $global_wsdl : false;
		$global_header=get_config('evasys','header');
		$header  = (!empty($global_header)) ? $global_header : false;
		$global_soapuser=get_config('evasys','soapuser');
		$soapuser  = (!empty($global_soapuser)) ? $global_soapuser : false;
		$global_soappass=get_config('evasys','soappass');
		$soappass  = (!empty($global_soappass)) ? $global_soappass : false;
		$global_participation_url=get_config('evasys','participation_url');
		$participation_url  = (!empty($global_participation_url)) ? $global_participation_url : false;
		$global_proxy=get_config('evasys','proxy');
		$proxy  = (!empty($global_proxy)) ? $global_proxy : '';
		$global_proxyport=get_config('evasys','proxyport');
		$proxyport  = (!empty($global_proxyport)) ? $global_proxyport : 0;		
		
		$blocktext='';
		
		if (has_capability('moodle/block:edit', $this->page->context)) {
			$isteacher=true;
		} else {
			$isteacher=false;
		}
		
		if ((!$wsdl)||(!$header)||(!$soapuser)||(!$soappass)) {
			if (has_capability('moodle/block:edit', $this->page->context)) {
				$blocktext.=get_string('warning_missing_configuration_error', 'block_evasys');
			} else {
				$blocktext.='';	
			}
		} else {
			
			
			$eva_info_found=false;
			$eva_exists=false;
				
			//var_dump($SESSION);
				
			if (!empty($SESSION->ce)){
				$debug.="Info aus Session<br>";
				$course_evaluations=$SESSION->ce;
				foreach ($course_evaluations as $course_evaluation) {
					$evaluation_course=$course_evaluation[0];
					$evaluation_tan=$course_evaluation[2];
					if ($course->id==$evaluation_course){
						$eva_info_found=true;
						if ($evaluation_tan) {
							$eva_exists=true;
						}
					}
				}
			} else {
				$debug.="Keine Info aus Session<br>";
				$course_evaluations=array();
			}			
			
			
			
			// Wenn noch keine Infos über Evaluationslink im Kurs vorliegen diese ermitteln
			if (!$eva_info_found) {
				
				try {
					$evasys=new Evasys_vfh($wsdl, $header, $soapuser, $soappass,$proxy,$proxyport);
				} catch (Exception $e) {
					$evasys=false;
				}			
				if (!$evasys->client) {
					if (has_capability('moodle/block:edit', $this->page->context)) {
						$blocktext.=get_string('warning_evays_configuration_error', 'block_online_evaluation');
					} else {
						$blocktext.='';	
					}
				} else {					
				
				
				$debug.="Keine Evainfos vorhanden<br>";

				$course_info = explode('-',$course->idnumber);
				$institution=$course_info[0];
				$programcode=$course_info[2];
				$component=$course_info[3];
				$semester=$course_info[4];
				
				$debug.='semester:'.$semester.'<br>';

				$programinfo=$evasys->getProgramInfo($programcode);
				$program=$programinfo[0];
				$curriculum=$programinfo[1];

				$temp_s=substr($semester,2,1);
				$temp_y=intval(substr($semester,0,2));
				if ($temp_s=='S') {
					$periode='SS'.$temp_y;
				} else {
					$periode='WS'.$temp_y.'/'.($temp_y+1);
				}
				$debug.="Periode:".$periode.":<br>";
				
				
			
					
					if(!$period_id=$evasys->getPeriodId($periode)) {
						$eva_exists=false;
						$debug.="Periode nicht gefunden<br>";
						
					} else {
						$debug.="Perioden_id:".$period_id.":<br>";
						
						$subunit=$evasys->getSubunitId($institution,$program);
						$component_search='['.$program.'|'.$curriculum.'|'.$component.']';
						if(!$component_dozent_id=$evasys->getSubunitUser($subunit,$component_search)) {
							$eva_exists=false;
							$debug.="Komponenten-Dozent nicht gefunden<br>";
							
						} else{
							$debug.="Komponenten-Dozent:".$component_dozent_id.":<br>";
							
							$subunitkey=$evasys->getSubunitKey($institution,$program);
							
							$course_search=$subunitkey.'-'.$program.'-'.$curriculum.'-'.$component;
							if(!$eva_course_id=$evasys->getCourseByKeyAndUser($component_dozent_id,$course_search)) {
								$eva_exists=false;
								$debug.="Lehrveranstaltung nicht gefunden<br>";
								
							} else {
								$debug.="Lehrveranstaltung:".$eva_course_id.":<br>";							
								
								if(!$allsurveys=$evasys->GetSurveysByCourse ($eva_course_id)) {
									//var_dump('Surveys nicht gefunden');
									$eva_exists=false;
									$debug.="Keine Umfragen gefunden<br>";
									//echo $debug;
								} else {
									$debug.="Umfragen gefunden<br>";
																	
									foreach ($allsurveys as $survey) {
	
										$survey_form=$survey->m_nFrmid;
										$survey_id=$survey->m_nSurveyId;
										$survey_period=$survey->m_oPeriod->m_nPeriodId;
										$survey_open=$survey->m_nOpenState;
										$survey_title=$survey->m_sTitle;
	
										if ($survey_form==EVASYS_STUDENT_FORM) {
											$survey_formtitle='Fragebogen zur Evaluation von Online-Lehrveranstaltungen';
										}
										if ($survey_form==EVASYS_MENTOR_FORM) {
											$survey_formtitle='Fragebogen zur Evaluation der Online-Lehre (Mentoren/Professoren)';
										}
											
										$form_allowed=true;
											
										if (($isteacher)&&($survey_form==EVASYS_STUDENT_FORM)){
											$form_allowed=false;
										}
										if ((!$isteacher)&&($survey_form==EVASYS_MENTOR_FORM)){
											$form_allowed=false;
										}
										
										$debug.='survey_form:'.$survey_form.'<br>';
										$debug.='survey_id:'.$survey_id.'<br>';
										$debug.='survey_period:'.$survey_period.'<br>';
										$debug.='survey_open:'.$survey_open.'<br>';
										$debug.='survey_title:'.$survey_title.'<br>';
										$debug.='survey_formtitle:'.$survey_formtitle.'<br>';
										$debug.='form_allowed:'.$form_allowed.'<br>';
										
										if (($survey_period==$period_id)&&($survey_open==1)&&($form_allowed)) {
											if (!$teilnahmelink=$evasys->GetOnlineSurveyLinkByEmail ($survey_id,$USER->email)) {
												$debug.="schon teilgenommen<br>";
														
											} else {
												$tanarray=explode('=', $teilnahmelink);
												$tan=$tanarray[1];
												$course_evaluations[]=array($course->id,$survey_title,$tan,$survey_formtitle);
												$SESSION->ce=$course_evaluations;
												$eva_exists=true;
														
											}
										}
									}									
									
								}	
								
							}	
							
						}	
						
					}
					
				}				
				
				
				if(!$eva_exists) {
					$course_evaluations[]=array($course->id,false,false,false);
					$SESSION->ce=$course_evaluations;
				}
				
				
				
			}			
			
			
			
			if ($eva_exists) {
				$blocktext = '';
				$blocktext .= '<center>';
				$blocktext .= get_string('entrytext', 'block_evasys').'<br/><br>';

				foreach ($course_evaluations as $course_evaluation) {
					$evaluation_course=$course_evaluation[0];
					$evaluation_title=$course_evaluation[1];
					$evaluation_tan=$course_evaluation[2];
					$evaluation_formtitle=$course_evaluation[3];

					if ($evaluation_course==$course->id){
						$blocktext .= '<a href="'.$participation_url.$evaluation_tan.'" target="_blank"><span style="font-weight:bold;font-size:14px;text-decoration:underline;">'.$evaluation_formtitle.' ('.$evaluation_title.')</span></a><br/><br/>';
					}
				}
				$blocktext .= get_string('footertext', 'block_evasys');
				$blocktextt .= '</center>';

			} else {
				$blocktext='';
			}			
			
			
			
		}

		$this->content->text = $blocktext;
		$this->content->footer = '';
		
		return $this->content;  		
		
	}
	
	public function instance_allow_multiple() {
		return false;
	}	

	public function html_attributes() {    
		$attributes = parent::html_attributes(); 
		$attributes['class'] .= ' block_evasys'; 
		return $attributes;
	}	

	public function applicable_formats() {  
		return array('course-view' => true);
	}	
	
	function has_config() {
		return true;
	}		
	
	
}
		