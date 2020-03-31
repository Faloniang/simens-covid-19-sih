<?php

namespace Consultation\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Consultation\View\Helper\DateHelper;
use Zend\Json\Json;
use Consultation\Form\ConsultationForm;
use Consultation\Form\PatientForm;
use Zend\Http\Client;
use Zend\Http\Request;

class ConsultationController extends AbstractActionController {
	
	protected $personneTable;
	protected $patientTable;
	protected $consultationTable;
	
	
	
	public function getPersonneTable() {
		if (! $this->personneTable) {
			$sm = $this->getServiceLocator ();
			$this->personneTable = $sm->get ( 'Consultation\Model\PersonneTable' );
		}
		return $this->personneTable;
	}
	
	public function getPatientTable() {
		if (! $this->patientTable) {
			$sm = $this->getServiceLocator ();
			$this->patientTable = $sm->get ( 'Personnel\Model\PatientTable' );
		}
		return $this->patientTable;
	}
	
	public function getConsultationTable() {
	    if (! $this->consultationTable) {
	        $sm = $this->getServiceLocator ();
	        $this->consultationTable = $sm->get ( 'Consultation\Model\ConsultationTable' );
	    }
	    return $this->consultationTable;
	}
	
	//=============================================================================================
	//---------------------------------------------------------------------------------------------
	//=============================================================================================
	
	public function baseUrl(){
	    $baseUrl = $_SERVER['REQUEST_URI'];
	    $tabURI  = explode('public', $baseUrl);
	    return $tabURI[0];
	}
	
	public function baseUrlFile(){
	    $baseUrl = $_SERVER['SCRIPT_FILENAME'];
	    $tabURI  = explode('public', $baseUrl);
	    return $tabURI[0];
	}
	
	public function creerNumeroFacturation($numero) {
		$nbCharNum = 10 - strlen($numero);
		
		$chaine ="";
		for ($i=1 ; $i <= $nbCharNum ; $i++){
			$chaine .= '0';
		}
		$chaine .= $numero;
		
		return $chaine;
	}
	
	public function numeroFacture() {
		$derniereFacturation = $this->getFacturationTable()->getDerniereFacturation();
		if($derniereFacturation){
			return $this->creerNumeroFacturation($derniereFacturation['numero']+1);
		}else{
			return $this->creerNumeroFacturation(1);
		} 
	}
	
	protected function nbJours($debut, $fin) {
	    //60 secondes X 60 minutes X 24 heures dans une journee
	    $nbSecondes = 60*60*24;
	
	    $debut_ts = strtotime($debut);
	    $fin_ts = strtotime($fin);
	    $diff = $fin_ts - $debut_ts;
	    return (int)($diff / $nbSecondes);
	}
	
	public function prixMill($prix) {
	    $str="";
	    $long =strlen($prix)-1;
	
	    for($i = $long ; $i>=0; $i--)
	    {
	        $j=$long -$i;
	        if( ($j%3 == 0) && $j!=0)
	        { $str= " ".$str;   }
	        $p= $prix[$i];
	
	        $str = $p.$str;
	        }
	
			if(!$str){ $str = $prix; }
	
			return($str);
	}
	
	public function etatCivilPatientAction($idpatient) {
	    
	    //MISE A JOUR DE L'AGE DU PATIENT
	    //MISE A JOUR DE L'AGE DU PATIENT
	    //MISE A JOUR DE L'AGE DU PATIENT
	    //$personne = $this->getPatientTable()->miseAJourAgePatient($idpatient);
	    //*******************************
	    //*******************************
	    //*******************************
	     
	    $patient = $this->getPatientTable()->getPatient($idpatient);
	    $personne = $this->getPersonneTable()->getPersonne($idpatient);
		
	    $date_naissance = null;
	    if($personne->date_naissance){ $date_naissance = (new DateHelper())->convertDate( $personne->date_naissance ); }
	    
	    $listeProfessions = $this->getPersonneTable()->getListeProfessions();
	    $listeStatutMatrimonial = $this->getPersonneTable()->getListeStatutMatrimonial();
	    $listeRegimeMatrimonial = $this->getPersonneTable()->getListeRegimeMatrimonial();
	    $listeRegion = $this->getPersonneTable()->getListeRegion();
	    $listeNationalite = $this->getPersonneTable()->getListeNationalites();
	     
	    $personne->photo = $personne->photo ? $personne->photo : 'identite.jpg';
	    $personne->profession = ($personne->profession) ? $listeProfessions[$personne->profession] : '';
	    $patient->departement = ($patient->departement) ? $this->listeDepartement($patient->region)[$patient->departement] : '';
	    $patient->region      = ($patient->region)      ? $listeRegion[$patient->region] : '';
	    $personne->regime_matrimonial = ($personne->regime_matrimonial) ? $listeRegimeMatrimonial[$personne->regime_matrimonial] : '';
	    $personne->statut_matrimonial = ($personne->statut_matrimonial) ? $listeStatutMatrimonial[$personne->statut_matrimonial] : '';
	    $patient->nationalite = ($patient->nationalite) ? $listeNationalite[$patient->nationalite] : '';
	    
	    
	    $typepatient = '';
	    if($patient->type_patient == 1){ $typepatient = '<span style="color: orange;">Cas suspect</span>'; }else
	    if($patient->type_patient == 2){ $typepatient = '<span style="color: #945b05;">Cas contact</span>'; }else
	    if($patient->type_patient == 3){ $typepatient = '<span style="color: red;">D&eacute;psit&eacute; positif</span>'; }else
	    if($patient->type_patient == 4){ $typepatient = '<span style="color: green;">D&eacute;pist&eacute; n&eacute;gatif</span>'; }

	    
	    $html ="
	  
	    <div style='width: 100%; margin-top: 8px;' align='center'>
	  
	    <table style='width: 96%; height: 100px; margin-top: 8px;' >
		
	        <tr style='width: 100%'>
				<td colspan='3' style='height: 25px; '> 
				
				  <a id='precedent' style='text-decoration: none; font-family: police2; width:50px; color: black; cursor: pointer;'>
	                <img src='".$this->baseUrl()."public/images_icons/transfert_gauche.png' />
		            Pr&eacute;c&eacute;dent
		          </a>
	        		
				</td>
				 
			</tr>
	                		
			<tr style='width: 100%;' class='dateNaissanceDiv'>
	  
			    <td style='width: 15%;' >
				  <img id='photo' src='".$this->baseUrl()."public/images/photos_patients/".$personne->photo."' style='width:105px; height:105px; margin-bottom: 10px; margin-top: 5px;'/>";
	     
	    //Gestion des AGES
	    //Gestion des AGES
	    //Gestion des AGES
	    if($personne->age && !$personne->date_naissance){
	    	$html .="<div style=' margin-left: 20px; margin-top: 145px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$personne->age." ans </span></div>";
	    }else{
	    	$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
	    	$age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
	    	$age_annees = (int)($age_jours/365);
	    
	    	if($age_annees == 0){
	    
	    		if($age_jours < 31){
	    			$html .="<div style='margin-left: 20px; margin-top: 145px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span></div>";
	    		}else if($age_jours >= 31) {
	    			 
	    			$nb_mois = (int)($age_jours/31);
	    			$nb_jours = $age_jours - ($nb_mois*31);
	    			if($nb_jours == 0){
	    				$html .="<div style='margin-left: 20px; margin-top: 145px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m </span></div>";
	    			}else{
	    				$html .="<div style='margin-left: 20px; margin-top: 145px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span></div>";
	    			}
	    
	    		}
	    
	    	}else{
	    		$age_jours = $age_jours - ($age_annees*365);
	    
	    		if($age_jours < 31){
	    
	    			if($age_annees == 1){
	    				if($age_jours == 0){
	    					$html .="<div style='margin-left: 15px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an </span></div>";
	    				}else{
	    					$html .="<div style='margin-left: 10px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$age_jours." j </span></div>";
	    				}
	    			}else{
	    				if($age_jours == 0){
	    					$html .="<div style='margin-left: 15px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans </span></div>";
	    				}else{
	    					$html .="<div style='margin-left: 10px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$age_jours."j </span></div>";
	    				}
	    			}
	    			 
	    		}else if($age_jours >= 31) {
	    			 
	    			$nb_mois = (int)($age_jours/31);
	    			$nb_jours = $age_jours - ($nb_mois*31);
	    
	    			if($age_annees == 1){
	    				if($nb_jours == 0){
	    					$html .="<div style='margin-left: 5px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:18px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m </span></div>";
	    				}else{
	    					$html .="<div style='margin-left: 2px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m ".$nb_jours."j </span></div>";
	    				}
	    
	    			}else{
	    				if($nb_jours == 0){
	    					$html .="<div style='margin-left: 5px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:18px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m </span></div>";
	    				}else{
	    					$html .="<div style='margin-left: 2px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m ".$nb_jours."j </span></div>";
	    				}
	    			}
	    
	    		}
	    
	    	}
	    
	    }
	    
	    //Fin gestion des ages
	    //Fin gestion des ages
	     
	    $html .="</td>
	  
				 <td style='width: 72%;' >
	  
					 <!-- TABLEAU DES INFORMATIONS -->
				     <!-- TABLEAU DES INFORMATIONS -->
					 <!-- TABLEAU DES INFORMATIONS -->
	    
				     <table id='etat_civil' style='width: 100%;'>
                        <tr>
            			   	<td style='width:27%; font-family: police1;font-size: 12px;'>
            			   		<div id='aa'><a style='text-decoration: underline; '>Sexe</a><br>
            			   		  <p style='font-weight: bold;font-size: 19px;'>
            			   		     ".$personne->sexe."
            			   	
            			   		  </p>
            			   		</div>
            			   		    
            			    </td>
            	
            			   	<td style='width:35%; font-family: police1;font-size: 12px;'>
            			   		<div id='aa'><a style='text-decoration: underline;'>Adresse</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->adresse." </p></div>
            			   	</td>
            	
            			    <td style='width:38%; font-family: police1;font-size: 12px;'>
            			       <div id='aa'><a style='text-decoration: underline;'>Profession</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->profession." </p></div>
            			   	</td>
	
			             </tr>
	  
			            <tr>
            			    <td style=' font-family: police1;font-size: 12px;'>
            			   		<div id='aa'><a style='text-decoration: underline;'>Pr&eacute;nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->prenom." </p></div>
            			   	
            			    </td>
            	    
                	        <td style=' font-family: police1;font-size: 12px;'>
                                <div id='aa'><a style='text-decoration: underline;'>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->telephone." </p></div>
            			   	</td>
            	
            	            <td style=' font-family: police1;font-size: 12px;'>
                                <div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute;</a><br><p style='font-weight: bold;font-size: 19px;'> ".$patient->nationalite." </p></div>
            			   	</td>
	
			           </tr>
	  
						       
					   <tr>
            			    <td style=' font-family: police1;font-size: 12px;'>
            			   	    <div id='aa'><a style='text-decoration: underline;'>Nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nom." </p></div>			   	
            			    </td>
            	
            			    <td style=' font-family: police1;font-size: 12px;'>
            			   		<div id='aa'><a style='text-decoration: underline;'>T&eacute;lephone 2</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->telephone_2." </p></div>
            			   	</td>
            	
            			   	<td style=' font-family: police1;font-size: 12px;'>
                                <div id='aa'><a style='text-decoration: underline;'>Statut matrimonial</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->statut_matrimonial." </p></div>
            			   	</td>
	
			             </tr>   
						       
						<tr>
            			   	<td style='width: 27%; font-family: police1;font-size: 12px; vertical-align: top;'>
            			   		<div id='aa'><a style='text-decoration: underline;'>Date de naissance</a><br><p style='font-weight: bold;font-size: 19px;'> ".$date_naissance." </p></div>
            			   	</td>
            	
            			    <td style='width: 195px; font-family: police1;font-size: 12px;'>
            		   		   <div id='aa'><a style='text-decoration: underline;'>R&eacute;gion</a><br><p style='font-weight: bold;font-size: 19px;'> $patient->region </p></div>
            			   	</td>
            	
            			    <td  style='width: 195px; font-family: police1;font-size: 12px;'>
            			   		<div id='aa'><a style='text-decoration: underline;'>R&eacute;gime matrimonial</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->regime_matrimonial." </p></div>
            			   	</td>
			             </tr>       
			        
            			   		    
            			 <tr>
            			   	<td style='width: 27%; font-family: police1;font-size: 12px; vertical-align: top;'>
            			   		<div id='aa'><a style='text-decoration: underline;'>Lieu de naissance</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->lieu_naissance." </p></div>
            			   	</td>
            	
            			    <td style='width: 195px; font-family: police1;font-size: 12px;'>
            			   		<div id='aa'><a style='text-decoration: underline;'>D&eacute;partement</a><br><p style='font-weight: bold;font-size: 19px;'>  $patient->departement </p></div>
            			   	</td>
            	
            			    <td  style='width: 195px; font-family: police1;font-size: 12px;'>
            			       <div id='aa'><a style='text-decoration: underline;'>Type de patient</a><br><p style='font-weight: bold;font-size: 19px;'> ".$typepatient." </p></div>
            			   	</td>
			             </tr> ";
						   
	    $html .="                 
	                 </table>
	        
 					 <!-- FIN TABLEAU DES INFORMATIONS -->
           			 <!-- FIN TABLEAU DES INFORMATIONS -->
			   		 <!-- FIN TABLEAU DES INFORMATIONS -->
	        
        	        <div id='barre' style='width: 100%; margin-left: 0px; '></div>
				</td>
	  
				<td style='width: 10%;' >
				  <span style='color: white; '>
                    <img src='".$this->baseUrl()."public/images/photos_patients/".$personne->photo."' style='width:105px; height:105px; opacity: 0.09; margin-top: 5px;'/>
                    <div style='margin-top: 20px; width: 105px; margin-right: 40px; font-size:17px; font-family: Iskoola Pota; color: green; float: right; font-style: italic; opacity: 1;'>".$patient->numero_dossier." </div>
                  </span>
				</td>
	  
			</tr>
		</table>
	  
		</div>";
	     
	    return $html;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//GESTION DE CREATION DES DOSSIERS PATIENTS --- GESTION DE CREATION DES DOSSIERS PATIENTS
	//GESTION DE CREATION DES DOSSIERS PATIENTS --- GESTION DE CREATION DES DOSSIERS PATIENTS
	//GESTION DE CREATION DES DOSSIERS PATIENTS --- GESTION DE CREATION DES DOSSIERS PATIENTS
	
	public function creerDossierAction() {
	    $this->layout ()->setTemplate ( 'layout/consultation' );
	    
		$form = new PatientForm();
		
		$listeProfessions = $this->getPersonneTable()->getListeProfessions();
		$listeNationalite = $this->getPersonneTable()->getListeNationalites();
		$listeStatutMatrimonial = $this->getPersonneTable()->getListeStatutMatrimonial();
		$listeRegimeMatrimonial = $this->getPersonneTable()->getListeRegimeMatrimonial();
		$listeRegion = $this->getPersonneTable()->getListeRegion();
		
		$form->get('PROFESSION')->setvalueOptions($listeProfessions);
		$form->get('STATUT_MATRIMONIAL')->setvalueOptions($listeStatutMatrimonial);
		$form->get('REGIME_MATRIMONIAL')->setvalueOptions($listeRegimeMatrimonial);
		$form->get('REGION')->setvalueOptions($listeRegion);
		$form->get('NATIONALITE_ACTUELLE')->setvalueOptions($listeNationalite);
		
		$form->populateValues(array('NATIONALITE_ACTUELLE' => 193));
		
		$request = $this->getRequest();
		
		
		if ($request->isPost()) {
		
		    $donnees = $this->getRequest()->getPost()->toArray();
		    
// 		    echo "<pre>";
// 		    var_dump($donnees); exit();
// 		    echo "</pre>";
		    
		    
		    $personne = array();
		    
		    $fileBase64 = substr($this->params ()->fromPost ('fichier_tmp'), 23);
		    if($fileBase64){ $img = imagecreatefromstring(base64_decode($fileBase64)); } else { $img = false; }
		    
		    if ($img != false) {
		        $photo = (new \DateTime ( 'now' )) ->format ( 'dmy_His' ).'.jpg';
		        imagejpeg ( $img, $this->baseUrlFile().'public/images/photos_patients/' . $photo );
		        $personne['PHOTO'] = $photo;
		    }
		    
		    //Enregistrement donn�es personne
		    $idpersonne = $this->getPersonneTable() ->savePersonne($donnees, $personne);

		    //Enregistrement donn�es patient
		    if($donnees['SEXE'] == 'Masculin'){ $sexe = 1; }else{ $sexe = 2; }
		    $idemploye = $this->layout()->user['id_employe'];
		    $this->getPatientTable()->savePatient($donnees, $idpersonne, $idemploye, $sexe);
		    
		    
		    if(array_key_exists('creerDossier', $donnees)){
		        return $this->redirect()->toRoute('consultation' , array('action'=>'liste-dossiers-patients') );
		    }elseif (array_key_exists('creerDossierAdmettre', $donnees)){
		        return $this->redirect()->toUrl( $this->baseUrl().'public/consultation/admettre-patient/idpatient/'.$idpersonne);
		    }
		    
		}
		
		
		return array (
				'form' => $form,
		);
	}
	
	
	public function listeDossiersPatientsAjaxAction() {
	    $output = $this->getPatientTable()->getListePatientsAjax();
	    return $this->getResponse ()->setContent ( Json::encode ( $output, array ( 'enableJsonExprFinder' => true ) ) );
	}
	
	public function listeDossiersPatientsAction() {
	    $this->layout ()->setTemplate ( 'layout/consultation' );
	}
	
	private function listeDepartement($idregion) {
	    
	    if ( $idregion == 1 ) { return array( 1 => 'DAKAR', 2 => 'GUEDIAWAYE', 3 => 'PIKINE', 4 => 'RUFISQUE' ); }
	    if ( $idregion == 2 ) { return array( 5 => 'BAMBEY', 6 => 'DIOURBEL', 7 => 'MBACKE'); }
	    if ( $idregion == 3 ) { return array( 8 => 'FATICK', 9 => 'FOUNDIOUCK', 10 => 'GOSSAS'); }
	    if ( $idregion == 4 ) { return array( 11 => 'BIRKELANE', 12 =>'KAFFRINE', 13 => 'KOUNGHEUL', 14 => 'MALEM HODDAR' ); }
	    if ( $idregion == 5 ) { return array( 15 => 'GUINGUINEO', 16 =>'KAOLACK', 17 => 'NIORO'); }
	    if ( $idregion == 6 ) { return array( 18 => 'KEDOUGOU', 19 => 'SALEMATA', 20 => 'SARAYA'); }
	    if ( $idregion == 7 ) { return array( 21 => 'KOLDA', 22 => 'MEDINA YORO FOULAH', 23 => 'VELINGARA', ); }
	    if ( $idregion == 8 ) { return array( 24 => 'KEBEMER', 25 => 'LINGUERE', 26 => 'LOUGA'); }
	    if ( $idregion == 9 ) { return array( 27 => 'KANEL', 28 => 'MATAM', 29 => 'RANEROU'); }
	    if ( $idregion == 10 ) { return array( 30 => 'DAGANA', 31 => 'PODOR', 32 => 'SAINT LOUIS'); }
	    if ( $idregion == 11 ) { return array( 33 => 'BOUNKILING', 34 => 'GOUDOMP', 35 => 'SEDHIOU'); }
	    if ( $idregion == 12 ) { return array( 36 => 'BAKEL', 37 => 'GOUDIRY', 38 => 'KOUPENTOUM', 39 => 'TAMBACOUNDA'); }
	    if ( $idregion == 13 ) { return array( 40 => 'MBOUR', 41 => 'THIES', 42 => 'TIVAOUANE'); }
	    if ( $idregion == 14 ) { return array( 43 => 'BIGNONA', 44 => 'OUSSOUYE', 45 => 'ZIGUINCHOR'); }
	     
	}
	
	public function infosPatientAction() {
	
	    $id = ( int ) $this->params ()->fromPost ( 'id', 0 );
	
	    $control = new DateHelper();
	
	    $patient = $this->getPatientTable()->getPatient($id);
	    $personne = $this->getPersonneTable()->getPersonne($id);

	    $date_naissance = null;
	    if($personne->date_naissance){ $date_naissance = $control->convertDate( $personne->date_naissance ); }
	
	    $listeProfessions = $this->getPersonneTable()->getListeProfessions();
	    $listeStatutMatrimonial = $this->getPersonneTable()->getListeStatutMatrimonial();
	    $listeRegimeMatrimonial = $this->getPersonneTable()->getListeRegimeMatrimonial();
	    $listeRegion = $this->getPersonneTable()->getListeRegion();
	    $listeNationalite = $this->getPersonneTable()->getListeNationalites();
	    
	    $personne->photo = $personne->photo ? $personne->photo : 'identite.jpg';
	    $personne->profession = ($personne->profession) ? $listeProfessions[$personne->profession] : '';
	    $patient->departement = ($patient->departement) ? $this->listeDepartement($patient->region)[$patient->departement] : '';
	    $patient->region      = ($patient->region)      ? $listeRegion[$patient->region] : '';
	    $personne->regime_matrimonial = ($personne->regime_matrimonial) ? $listeRegimeMatrimonial[$personne->regime_matrimonial] : '';
	    $personne->statut_matrimonial = ($personne->statut_matrimonial) ? $listeStatutMatrimonial[$personne->statut_matrimonial] : '';
	    $patient->nationalite = ($patient->nationalite) ? $listeNationalite[$patient->nationalite] : '';
	     
	    
	    $html ="
	
	    <div style='width: 100%;'>
	
        <img id='photo' src='../images/photos_patients/".$personne->photo."' style='float:left; margin-right:40px; width:105px; height:105px;'/>";
	
	    //Gestion des AGES
	    //Gestion des AGES
	    if($personne->age && !$personne->date_naissance){
	        $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$personne->age." ans </span></div>";
	    }else{
	        	
	        $aujourdhui = (new \DateTime() ) ->format('Y-m-d');
	        $age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
	
	        $age_annees = (int)($age_jours/365);
	
	        if($age_annees == 0){
	
	            if($age_jours < 31){
	                $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span></div>";
	            }else if($age_jours >= 31) {
	                 
	                $nb_mois = (int)($age_jours/31);
	                $nb_jours = $age_jours - ($nb_mois*31);
	                if($nb_jours == 0){
	                    $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m </span></div>";
	                }else{
	                    $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span></div>";
	                }
	
	            }
	
	        }else{
	            $age_jours = $age_jours - ($age_annees*365);
	
	            if($age_jours < 31){
	
	                if($age_annees == 1){
	                    if($age_jours == 0){
	                        $html .="<div style=' left: 60px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an </span></div>";
	                    }else{
	                        $html .="<div style=' left: 60px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$age_jours." j </span></div>";
	                    }
	                }else{
	                    if($age_jours == 0){
	                        $html .="<div style=' left: 60px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans </span></div>";
	                    }else{
	                        $html .="<div style=' left: 60px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$age_jours."j </span></div>";
	                    }
	                }
	                 
	            }else if($age_jours >= 31) {
	                 
	                $nb_mois = (int)($age_jours/31);
	                $nb_jours = $age_jours - ($nb_mois*31);
	
	                if($age_annees == 1){
	                    if($nb_jours == 0){
	                        $html .="<div style=' left: 50px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m </span></div>";
	                    }else{
	                        $html .="<div style=' left: 50px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m ".$nb_jours."j </span></div>";
	                    }
	
	                }else{
	                    if($nb_jours == 0){
	                        $html .="<div style=' left: 50px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m </span></div>";
	                    }else{
	                        $html .="<div style=' left: 50px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m ".$nb_jours."j </span></div>";
	                    }
	                }
	
	            }
	
	        }
	
	    }
	
	    //Fin gestion des ages
	    //Fin gestion des ages
	    $typepatient = '';
	    if($patient->type_patient == 1){ $typepatient = '<span style="color: orange;">Cas suspect</span>'; }else
	    if($patient->type_patient == 2){ $typepatient = '<span style="color: #945b05;">Cas contact</span>'; }else
	    if($patient->type_patient == 3){ $typepatient = '<span style="color: red;">D&eacute;psit&eacute; positif</span>'; }else
	    if($patient->type_patient == 4){ $typepatient = '<span style="color: green;">D&eacute;pist&eacute; n&eacute;gatif</span>'; }
	    
	    
	    $html .="<p>
         <img id='photo' src='../images/photos_patients/".$personne->photo."' style='float:right; margin-right:15px; width:95px; height:95px; color: white; opacity: 0.09;'/>
        </p>
       
        <table id='etat_civil'>
             <tr>
			   	<td style='width:27%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline; '>Sexe</a><br>
			   		  <p style='font-weight: bold;font-size: 19px;'>
			   		     ".$personne->sexe."
			   	
			   		  </p>
			   		</div>
			   		    
			    </td>
	
			   	<td style='width:35%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Adresse</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->adresse." </p></div>
			   	</td>
	
			    <td style='width:38%; font-family: police1;font-size: 12px;'>
			       <div id='aa'><a style='text-decoration: underline;'>Profession</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->profession." </p></div>
			   	</td>
	
			 </tr>
	
			   		    
			 <tr>
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Pr&eacute;nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->prenom." </p></div>
			   	
			    </td>";
	
	    
	        $html .="<td style=' font-family: police1;font-size: 12px;'>
                    <div id='aa'><a style='text-decoration: underline;'>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->telephone." </p></div>
			   	</td>";
	
	    $html .="<td style=' font-family: police1;font-size: 12px;'>
                    <div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute;</a><br><p style='font-weight: bold;font-size: 19px;'> ".$patient->nationalite." </p></div>
			   	</td>
	
			 </tr>
	
			 <tr>
			    <td style=' font-family: police1;font-size: 12px;'>
			   	    <div id='aa'><a style='text-decoration: underline;'>Nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nom." </p></div>			   	
			    </td>
	
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>T&eacute;lephone 2</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->telephone_2." </p></div>
			   	</td>
	
			   	<td style=' font-family: police1;font-size: 12px;'>
                    <div id='aa'><a style='text-decoration: underline;'>Statut matrimonial</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->statut_matrimonial." </p></div>
			   	</td>
	
			 </tr>
	
			 <tr>
			   	<td style='width: 27%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Date de naissance</a><br><p style='font-weight: bold;font-size: 19px;'> ".$date_naissance." </p></div>
			   	</td>
	
			    <td style='width: 195px; font-family: police1;font-size: 12px;'>
		   		   <div id='aa'><a style='text-decoration: underline;'>R&eacute;gion</a><br><p style='font-weight: bold;font-size: 19px;'> $patient->region </p></div>
			   	</td>
	
			    <td  style='width: 195px; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>R&eacute;gime matrimonial</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->regime_matrimonial." </p></div>
			   	</td>
			 </tr>
			           
			 <tr>
			   	<td style='width: 27%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Lieu de naissance</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->lieu_naissance." </p></div>
			   	</td>
	
			    <td style='width: 195px; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>D&eacute;partement</a><br><p style='font-weight: bold;font-size: 19px;'>".  $patient->departement." </p></div>
			   	</td>
	
			    <td  style='width: 195px; font-family: police1;font-size: 12px;'>
			       <div id='aa'><a style='text-decoration: underline;'>Type de patient</a><br><p style='font-weight: bold;font-size: 19px;'>". $typepatient ."</p></div>
			   	</td>
			 </tr>";
	     
	    $html .="
			 
           </table>
	
           <div id='barre'></div>
	
           <div style='color: white; opacity: 1; width:95px; height:40px; float:right'>
             <img  src='../images_icons/fleur1.jpg' />
           </div>
	       <table id='numero' style=' padding-top:5px; width: 60%; '>
           <tr>
             <td style='padding-top:3px; padding-left:25px; padding-right:5px; font-size: 14px; width: 95px;'> Numero dossier: </td>
             <td style='font-weight: bold; '>".$patient->numero_dossier."</td>
             <td style='font-weight: bold;padding-left:20px;'>|</td>
             <td style='padding-top:5px; padding-left:10px; font-size: 14px;'> Date d'enregistrement: </td>
             <td style='font-weight: bold;'>". $control->convertDateTime( $patient->date_enregistrement ) ."</td>
           </tr>
           </table>
	
	    <div class='block' id='thoughtbot' style=' vertical-align: bottom; padding-left:60%; padding-top: 35px; font-size: 18px; font-weight: bold;'><button id='terminer'>Terminer</button></div>
        
        <div style=' height: 80px; width: 100px; '> </div>
   
        </div> ";
	
	
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function modifierDossierAction() {
	    $this->layout ()->setTemplate ( 'layout/consultation' );
	     
	    $idpersonne = (int) $this->params()->fromRoute('val', 0);
	    $form = new PatientForm();
	
	    $listeProfessions = $this->getPersonneTable()->getListeProfessions();
	    $listeStatutMatrimonial = $this->getPersonneTable()->getListeStatutMatrimonial();
	    $listeRegimeMatrimonial = $this->getPersonneTable()->getListeRegimeMatrimonial();
	    $listeRegion = $this->getPersonneTable()->getListeRegion();
	    $listeNationalite = $this->getPersonneTable()->getListeNationalites();
	    
	    $form->get('PROFESSION')->setvalueOptions($listeProfessions);
	    $form->get('STATUT_MATRIMONIAL')->setvalueOptions($listeStatutMatrimonial);
	    $form->get('REGIME_MATRIMONIAL')->setvalueOptions($listeRegimeMatrimonial);
	    $form->get('REGION')->setvalueOptions($listeRegion);
	    $form->get('NATIONALITE_ACTUELLE')->setvalueOptions($listeNationalite);

	    
	    $request = $this->getRequest();
	    if ($request->isPost()) {
	
	        $donnees = $this->getRequest()->getPost()->toArray();
	        
	        $idpersonne = $donnees['ID_PERSONNE'];
	        
	        $personne = array();
	
	        $fileBase64 = substr($this->params ()->fromPost ('fichier_tmp'), 23);
	        if($fileBase64){ $img = imagecreatefromstring(base64_decode($fileBase64)); } else { $img = false; }
	
	        if ($img != false) {
	            
	            $lepatient = $this->getPersonneTable()->getPersonne($idpersonne);
	            $ancienneImage = $lepatient->photo;
	            
	            if($ancienneImage) {
	                unlink ( $this->baseUrlFile().'public/images/photos_patients/' . $ancienneImage . '.jpg' );
	            }
	            
	            $photo = (new \DateTime ( 'now' )) ->format ( 'dmy_His' ).'.jpg';
	            imagejpeg ( $img, $this->baseUrlFile().'public/images/photos_patients/' . $photo );
	            $personne['PHOTO'] = $photo;
	        }
	
	        //Modifier donn�es personne
	        $this->getPersonneTable() ->updatePersonne($idpersonne, $donnees, $personne);
	
	
	        //Modifier donn�es patient
	        $patient = $this->getPatientTable()->getPatient($idpersonne);
	        

	        $idemploye = $this->layout()->user['id_employe'];
	        $this->getPatientTable()->updatePatient($donnees, $idpersonne, $idemploye, $patient);
	
	        
	        return $this->redirect()->toRoute('consultation' , array('action'=>'liste-dossiers-patients') );
	    }
	
	
	    $personne = $this->getPersonneTable()->getPersonne($idpersonne);
	    $patient = $this->getPatientTable()->getPatient($idpersonne);
	    
	    $data = array();
	    
	    $data['ID_PERSONNE'] = $idpersonne;
	    $data['NOM'] = $personne->nom;
	    $data['PRENOM'] = $personne->prenom;
	    $data['DATE_NAISSANCE'] = $personne->date_naissance;
	    $data['LIEU_NAISSANCE'] = $personne->lieu_naissance;
	    $data['ADRESSE'] = $personne->adresse;
	    $data['SEXE'] = $personne->sexe;
	    $data['AGE'] = $personne->age;
	    $data['TELEPHONE'] = $personne->telephone;
	    $data['TELEPHONE_2'] = $personne->telephone_2;
	    $data['PROFESSION'] = $personne->profession;
	    $data['STATUT_MATRIMONIAL'] = $personne->statut_matrimonial;
	    $data['REGIME_MATRIMONIAL'] = $personne->regime_matrimonial;
	    $data['REGION'] = $patient->region;
	    $data['DEPARTEMENT_MEMO'] = $patient->departement;
	    $data['NATIONALITE_ACTUELLE'] = $patient->nationalite;
	    $data['TYPE_PATIENT'] = $patient->type_patient;
	    
	    
	    $photo = $personne->photo ? $personne->photo : 'identite.jpg';
	    
	    if($personne->date_naissance){ $data['DATE_NAISSANCE'] = (new DateHelper())->convertDate( $personne->date_naissance ); }
	    $form->populateValues($data);

	    return array (
	        'form' => $form,
	        'photo' => $photo,
	    );
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//GESTION DE L'ADMISSION DES PATIENTS --- GESTION DE L'ADMISSION DES PATIENTS
	//GESTION DE L'ADMISSION DES PATIENTS --- GESTION DE L'ADMISSION DES PATIENTS
	//GESTION DE L'ADMISSION DES PATIENTS --- GESTION DE L'ADMISSION DES PATIENTS
	
	public function listePatientsAdmettreAjaxAction() {
		$output = $this->getPatientTable()->getListePatientsAAdmettre();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array ( 'enableJsonExprFinder' => true ) ) );
	}
	
	public function listePatientsAdmettreAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
	}
	
	public function admettrePatientVueAction() {
		$idpatient = ( int ) $this->params ()->fromPost ( 'idpatient', 0 );
		
		$html = $this->etatCivilPatientAction($idpatient);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	public function admettrePatientAction() {
		$idpatient = (int) $this->params()->fromRoute('val', 0);
		$idemploye = $this->layout()->user['id_employe'];
		
		
		$this->getPatientTable()->admettrePatient($idpatient, $idemploye);
	
		return $this->redirect()->toRoute('consultation' , array('action'=>'liste-consultations') );
	}
	
	
	public function listePatientsAdmisAjaxAction() {
		$output = $this->getPatientTable()->getListePatientsAdmisAjax();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array ( 'enableJsonExprFinder' => true ) ) );
	}
	
	
	public function listePatientsAdmisAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
	}
	
	
	public function supprimerAdmissionPatientAction() {
		
		$idadmission = (int) $this->params()->fromRoute('val', 0);
		$this->getPatientTable()->supprimerAdmission($idadmission);
		
		return $this->redirect()->toRoute('consultation' , array('action'=>'liste-patients-admis') );
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS
	//GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS
	//GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS
	
	public function listeConsultationsAjaxAction() {
	     
	    $output = $this->getConsultationTable ()->getListeConsultations();
	    return $this->getResponse ()->setContent ( Json::encode ( $output, array ( 'enableJsonExprFinder' => true ) ) );
	}
	
	
	public function listeConsultationsAction() {
	    //$output = $this->getConsultationTable ()->getListeConsultations();
	    $this->layout ()->setTemplate ( 'layout/consultation' );
	}
	
	
	public function consulterAction() {
	     
	    $this->layout ()->setTemplate ( 'layout/consultation' );
	
	    $user = $this->layout()->user;
	    $idmedecin = $user['id_personne'];
	
	    $idadmission = $this->params ()->fromQuery ( 'idadmission', 0 );
	    $idpatient = $this->params ()->fromQuery ( 'idpatient', 0 );
	
	    $liste = $this->getConsultationTable ()->getInfoPatient ( $idpatient );
	    $patient = $this->getPatientTable()->getPatient( $idpatient );
	    $personne = $this->getPersonneTable()->getPersonne($idpatient);
	
	    $form = new ConsultationForm ();
	    $idcons = $form->get ( "idcons" )->getValue ();
	    $date = $form->get ( "date" )->getValue ();
	    $heure = $form->get ( "heure" )->getValue ();
	
	    $listeProfessions = $this->getPersonneTable()->getListeProfessions();
	    $listeStatutMatrimonial = $this->getPersonneTable()->getListeStatutMatrimonial();
	    $listeRegimeMatrimonial = $this->getPersonneTable()->getListeRegimeMatrimonial();
	    $listeRegion = $this->getPersonneTable()->getListeRegion();
	    $listeNationalite = $this->getPersonneTable()->getListeNationalites();
	     
	    $personne->photo = $personne->photo ? $personne->photo : 'identite.jpg';
	    $personne->profession = ($personne->profession) ? $listeProfessions[$personne->profession] : '';
	    $patient->departement = ($patient->departement) ? $this->listeDepartement($patient->region)[$patient->departement] : '';
	    $patient->region      = ($patient->region)      ? $listeRegion[$patient->region] : '';
	    $personne->regime_matrimonial = ($personne->regime_matrimonial) ? $listeRegimeMatrimonial[$personne->regime_matrimonial] : '';
	    $personne->statut_matrimonial = ($personne->statut_matrimonial) ? $listeStatutMatrimonial[$personne->statut_matrimonial] : '';
	    $patient->nationalite = ($patient->nationalite) ? $listeNationalite[$patient->nationalite] : '';
	    
	    
	    $data = array (
	        'idpatient' => $idpatient,
	    );
	
	    //var_dump($personne); exit();
	
	
	    return array (
	
	        'form' => $form,
	        'lesdetails' => $liste,
	        'patient' => $patient,
	        'personne' => $personne,
	        	
	        /*ETAT CIVIL --- ETAT CIVIL*/
	        'listeProfessions' => $listeProfessions,
	        'listeStatutMatrimonial' => $listeStatutMatrimonial,
	        'listeRegimeMatrimonial' => $listeRegimeMatrimonial,
	        'idcons' => $idcons,
	        'date' => $date,
	        'heure' => $heure,
	
	       
	    );
	
	}
	
	
	
	
	
	
	private $url = 'http://www.simens.sn/admin?action=';
	
	public function autoSignalementAction() {
	    $this->layout ()->setTemplate ( 'layout/consultation' );
	    
	    $user = $this->layout()->user;
	    $idmedecin = $user['id_personne'];
	    
	}
	
	
	
	public function listeAutoSignalementAjaxAction() {
	    
	    /*
	     * RECUPERATION DE LA LISTE DE LA DB DISTANT
	     */
	    
	    $output = array(
	        "iTotalDisplayRecords" => 0,
	        "aaData" => array()
	    );
	    
	    $request = new Request();
	    $request->setUri($this->url.'getInfosAutoSignalement');
	    $request->setMethod('GET');
	     
	    $client = new Client();
	    $response = $client->dispatch($request);
	     
	    if ($response->isSuccess()) {
	        
	        // the POST was successful
	        $donnees = json_decode($response->getBody());
	         
	        // Donn�es r�cup�r�es
	        $data = $donnees->data;
	        $output["iTotalDisplayRecords"] = count($data);
	        $tabDonnneesParPatient = "";
	         
	        for ($i=0 ; $i<count($data) ; $i++){
	            $tab =  explode('<@>',openssl_decrypt($data[$i]->infosEtatCivil, 'BF-ECB', 'passInfosCovid19*1234'));
	            
	            $date_naissance = ($tab[3] == -1) ? "": (new DateHelper())->convertDate($tab[3]);
	            $sexe = ($tab[0] == 2) ? "F":"M";
	            
	            $visualiser = '<img onclick="voirPlusDetails('.$data[$i]->idpatient.');" style="cursor: pointer; margin-right: 17%;" src="../images_icons/voir2.png" title="Voir plus.."> <img id="iconeChargement_'.$data[$i]->idpatient.'" class="iconeChargement_1234" style="width:40px; height: 20px; display: none;" src="../images_icons/chargement-infos-covid19.gif">';
	            if($data[$i]->vue == 0){
	               $visualiser .= '<span id="nonEncoreVueInfosPatient_'.$data[$i]->idpatient.'" style="color: red; position: absolute; font-size: 14px;" title="Non vue">&#9733;</span>';
	            }
	            
	            /*Liste des patients*/
	            $output["aaData"][] = array( $tab[1], $tab[2], $date_naissance, $tab[4], $sexe, "<div>".$tab[7]."</div>", $tab[8], $visualiser);
	        
	            
	        }
	         
	    }
	     
	    
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $output ) );
	}
	
	
	
	
	
	public function listeVueDetailsInfosPatientAutoSignalementAction() {
	    
	    $idpatient = (int)$this->params ()->fromPost ( 'idpatient', 0 );
	    $listeRegion = $this->getPersonneTable()->getListeRegion();
	    
	    /*
	     * RECUPERATION DE LA LISTE DE LA DB DISTANT
	     */
	    $request = new Request();
	    $request->setUri($this->url.'getInfosPatientsAutoSignalement&idpatient='.$idpatient);
	    $request->setMethod('GET');
	
	    $client = new Client();
	    $response = $client->dispatch($request);
	
	    if ($response->isSuccess()) {
	         
	        // the POST was successful
	        $donnees = json_decode($response->getBody());
	
	        // Donn�es r�cup�r�es
	        $data = $donnees->data;
	        
	        $tab = array();
	        $tab =  explode('<@>',openssl_decrypt($data->infosEtatCivil, 'BF-ECB', 'passInfosCovid19*1234'));
	        
	        $sexe = ($tab[0] == 2) ? "F&eacute;minin":"Masculin";
	        $date_naissance = ($tab[3] == -1) ? "": $tab[3];
	        $telephone2 = ($tab[9] == -1) ? "": $tab[9];
	        $email = ($tab[10] == -1) ? "": $tab[10];
	        $date_enregistrement = $data->date_enregistrement;
	
	        
	        $tab2 = array();
	        $tab2 =  explode('<@>',openssl_decrypt($data->infosSymptomes, 'BF-ECB', 'passInfosCovid19*1234'));
	    
	        $tab3 = array();
	        $tab3 =  explode('<@>',openssl_decrypt($data->infosAntecedents, 'BF-ECB', 'passInfosCovid19*1234'));
	        
	    }

	    
	    
	     
	    $html ="
	    
	    <div style='width: 100%;'>
	    
        <img id='photo' src='../images/photos_patients/identite.jpg' style='float:left; margin-right:40px; width:105px; height:105px;'/>";
	    
	    $age = 10;
	    //Gestion des AGES
	    //Gestion des AGES
	    if($age && !$date_naissance){
	        $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$tab[4]." ans </span></div>";
	    }else{
	    
	        $aujourdhui = (new \DateTime() ) ->format('Y-m-d');
	        $age_jours = $this->nbJours($date_naissance, $aujourdhui);
	    
	        $age_annees = (int)($age_jours/365);
	    
	        if($age_annees == 0){
	    
	            if($age_jours < 31){
	                $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span></div>";
	            }else if($age_jours >= 31) {
	    
	                $nb_mois = (int)($age_jours/31);
	                $nb_jours = $age_jours - ($nb_mois*31);
	                if($nb_jours == 0){
	                    $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m </span></div>";
	                }else{
	                    $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span></div>";
	                }
	    
	            }
	    
	        }else{
	            $age_jours = $age_jours - ($age_annees*365);
	    
	            if($age_jours < 31){
	    
	                if($age_annees == 1){
	                    if($age_jours == 0){
	                        $html .="<div style=' left: 60px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an </span></div>";
	                    }else{
	                        $html .="<div style=' left: 60px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$age_jours." j </span></div>";
	                    }
	                }else{
	                    if($age_jours == 0){
	                        $html .="<div style=' left: 60px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans </span></div>";
	                    }else{
	                        $html .="<div style=' left: 60px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$age_jours."j </span></div>";
	                    }
	                }
	    
	            }else if($age_jours >= 31) {
	    
	                $nb_mois = (int)($age_jours/31);
	                $nb_jours = $age_jours - ($nb_mois*31);
	    
	                if($age_annees == 1){
	                    if($nb_jours == 0){
	                        $html .="<div style=' left: 50px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m </span></div>";
	                    }else{
	                        $html .="<div style=' left: 50px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m ".$nb_jours."j </span></div>";
	                    }
	    
	                }else{
	                    if($nb_jours == 0){
	                        $html .="<div style=' left: 50px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m </span></div>";
	                    }else{
	                        $html .="<div style=' left: 50px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m ".$nb_jours."j </span></div>";
	                    }
	                }
	    
	            }
	    
	        }
	    
	    }
	    
	    $date_naissance = ($date_naissance) ? (new DateHelper())->convertDate($date_naissance) : '';
	    
	    //Fin gestion des ages
	    //Fin gestion des ages
	    
	    $html .="<p>
         <img id='photo' src='../images/photos_patients/identite.jpg' style='float:right; margin-right:15px; width:95px; height:95px; color: white; opacity: 0.09;'/>
        </p>
    
        <table id='etat_civil'>
             <tr>
			   	<td style='width:27%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline; '>Sexe</a><br>
			   		  <p style='font-weight: bold;font-size: 19px;'>
			   		     ".$sexe."
			   		  </p>
			   		</div>
	    
			    </td>
	    
			   	<td style='width:35%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Adresse</a><br><p style='font-weight: bold;font-size: 19px;'> ".$tab[7]." </p></div>
			   	</td>
	    
			    <td style='width:38%; font-family: police1;font-size: 12px;'>
			   	    <div id='aa'><a style='text-decoration: underline;'>R&eacute;gion</a><br><p style='font-weight: bold;font-size: 19px;'> ".$listeRegion[$tab[5]]." </p></div>
	    	    </td>
	    
			 </tr>
	    
	    
			 <tr>
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Pr&eacute;nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$tab[1]." </p></div>
			 
			    </td>";
	    
	     
	    $html .="<td style=' font-family: police1;font-size: 12px;'>
                    <div id='aa'><a style='text-decoration: underline;'>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 19px;'> ".$tab[8]." </p></div>
			   	</td>";
	    
	    $html .="<td style=' font-family: police1;font-size: 12px;'>
                    <div id='aa'><a style='text-decoration: underline;'>D&eacute;partement</a><br><p style='font-weight: bold;font-size: 19px;'>  ".$this->listeDepartement($tab[5])[$tab[6]]." </p></div>
	    	     </td>
	    
			 </tr>
	    
			 <tr>
			    <td style=' font-family: police1;font-size: 12px;'>
			   	    <div id='aa'><a style='text-decoration: underline;'>Nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$tab[2]." </p></div>
			    </td>
	    
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>T&eacute;lephone 2</a><br><p style='font-weight: bold;font-size: 19px;'> ".$telephone2." </p></div>
			   	</td>
	    
			   	<td style=' font-family: police1;font-size: 12px;'>
                    <div id='aa'><a style='text-decoration: underline;'></a><br><p style='font-weight: bold;font-size: 19px;'>  </p></div>
			   	</td>
	    
			 </tr>
	    
			 <tr>
			   	<td style='width: 27%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Date de naissance</a><br><p style='font-weight: bold;font-size: 19px;'> ".$date_naissance." </p></div>
	    			   		</td>
	    
	    			   		<td style='width: 195px; font-family: police1;font-size: 12px;'>
	    			   		<div id='aa'><a style='text-decoration: underline;'>Email</a><br><p style='font-weight: bold;font-size: 19px;'> ".$email." </p></div>
	    			   		</td>
	    
	    			   		<td  style='width: 195px; font-family: police1;font-size: 12px;'>
	    			   		<div id='aa'><a style='text-decoration: underline;'>Date de signalement:</a><br><p style='font-weight: bold;font-size: 19px;'> ".(new DateHelper())->convertDateTime($date_enregistrement)."</p></div>
			   	</td>
			 </tr>
	         </table>
			 ";
	    
	    $html .=" <div style='margin-left: 165px; margin-top: 15px; margin-bottom: -15px; color: green; font-size: 21px; font-family: time new roman;' ><i> Les sympt&ocirc;mes </i></div> ";
	    $html .=" <div id='barre' ></div> ";
	    
	    $nbLignes = (count($tab2))-1;
	    
	    $html .="
	        <div style='margin-left: 165px; margin-top: 10px; width: 80%; font-family: police1; color: green;'>
	          <p style='text-align: justify; line-height: 30px; font-weight: bold;font-size: 19px;'>&nbsp;";
	    
	    for ($i=0 ; $i<$nbLignes ; $i++){
	         
	            $html .="<span style='font-size: 14px;'>&#10148;</span>".
	                     $this->getLibelleSymptome($tab2[$i])." &nbsp;&nbsp;&nbsp;";
	    }
	    
	    $html .="
	          </p>
			</div>";
	    

	    $html .=" <div style='margin-left: 165px; margin-top: 15px; margin-bottom: -15px; color: green; font-size: 21px; font-family: time new roman;' ><i> Les habitudes de vie et ant&eacute;c&eacute;dents </i></div> ";
	    $html .=" <div id='barre' ></div> ";
	    
	    $nbLignes = (count($tab3))-1;
	     
	    $html .="
	        <div style='margin-left: 165px; margin-top: 10px; width: 80%; font-family: police1; color: green;'>
	          <p style='text-align: justify; line-height: 30px; font-weight: bold; font-size: 19px;'>&nbsp;";
	     
	    for ($i=0 ; $i<$nbLignes ; $i++){
	    
	        $html .="<span style='font-size: 14px;'>&#10148;</span>".
	            $this->getLibelleAntecedents($tab3[$i])." &nbsp;&nbsp;&nbsp;";
	    }
	     
	    $html .="
	          </p>
			</div>";
	    
	    
	    
	    $html .="<div class='block' id='thoughtbot' style=' vertical-align: bottom; padding-left:60%; padding-top: 35px; font-size: 18px; font-weight: bold;'><button id='terminer'>Terminer</button></div>
	    
        <div style=' height: 80px; width: 100px; '> </div>
  
        </div> ";
	    
	    
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	public function getLibelleSymptome($idsymptome){
	    $tab = array(1=>'Fi&egrave;vre', 2=>'Fatigue', 3=>'Tousser et/ou Toux seche', 4=>'Difficult&eacute;s &agrave; respirer',
	                 5=>'Douleurs &agrave; la respiration', 6=>'Douleurs aux muscles', 7=>'Douleurs aux articulations',
	                 8=>'Mal de gorge', 9=>'Nez bouch&eacute;', 10=>'Nez qui coule', 11=>'Vertige', 12=>'Perte d\'app&eacute;tit', 13=>'Diarrh&eacute;e'
	    );
	    
	    return $tab[$idsymptome];
	}
	
	
	public function getLibelleAntecedents($idantecedent){
	    $tab = array(1=>'Fumeur', 2=>'Consommation d\'alcool', 3=>'Diab&egrave;te', 4=>'Hypertension art&eacute;rielle',
	                 5=>'Asthmatique', 6=>'Maladie cardiaque', 7=>'Ob&eacute;sit&eacute;',
	                 8=>'Contact avec un malade', 9=>'Retour de voyage');
	     
	    return $tab[$idantecedent];
	}
	
	
	public function signifierInfosPatientVueAction() {
	    
	    $idpatient = (int)$this->params ()->fromPost ( 'idpatient', 0 );
	    
	    /*
	     * RECUPERATION DE LA LISTE DE LA DB DISTANT
	     */
	    $request = new Request();
	    $request->setUri($this->url.'getSignifierInfosPatientVue&idpatient='.$idpatient);
	    $request->setMethod('GET');
	    
	    $client = new Client();
	    $response = $client->dispatch($request);
	    
	    if ($response->isSuccess()) {
	    
	        // the POST was successful
	        $donnees = json_decode($response->getBody());
	    
	        // Donn�es r�cup�r�es
	        $data = $donnees->data;
	         
	        $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	        return $this->getResponse ()->setContent ( Json::encode ( $data ) );
	    }
	    
	}
	
	
	
	public function nbreInfosPatientNonVueAction() {
	    /*
	     * RECUPERATION DE LA LISTE DE LA DB DISTANT
	     */
	    $request = new Request();
	    $request->setUri($this->url.'getNbreInfosPatientNonVue');
	    $request->setMethod('GET');
	     
	    $client = new Client();
	    $response = $client->dispatch($request);
	     
	    if ($response->isSuccess()) {
	         
	        // the POST was successful
	        $donnees = json_decode($response->getBody());
	         
	        // Donn�es r�cup�r�es
	        $data = $donnees->data;
	    
	        $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	        return $this->getResponse ()->setContent ( Json::encode ( $data ) );
	    }
	}
	
	
	
	
}
