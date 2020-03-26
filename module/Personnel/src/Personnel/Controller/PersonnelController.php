<?php
namespace Personnel\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Personnel\Form\PersonnelForm;
use Personnel\Model\Personnel;
use Personnel\Form\TypePersonnelForm;
use Zend\Json\Json;
use Personnel\View\Helper\DateHelper;
use Personnel\Form\TransfertPersonnelForm;
use Personnel\Model\Transfert1;
use Zend\Form\View\Helper\FormRow;
use Zend\Form\View\Helper\FormText;
use Zend\Form\View\Helper\FormSelect;
use Zend\Form\View\Helper\FormTextarea;
use Zend\Form\View\Helper\FormHidden;
use Personnel\Form\InterventionPersonnelForm;
use Personnel\Model\Intervention1;

class PersonnelController extends AbstractActionController {
	
	protected $formPersonnel;
	protected $dateHelper;
	
	protected $patientTable;
	protected $personnelTable;
	protected $medecinTable;
	protected $medicotechniqueTable;
	protected $logistiqueTable;
	protected $affectationTable;
	protected $typePersonnelTable;
	protected $serviceTable;
	protected $transfertTable;
	protected $hopitalserviceTable;
	protected $interventionTable;

	public function getPatientTable() {
		if (! $this->patientTable) {
			$sm = $this->getServiceLocator ();
			$this->patientTable = $sm->get ( 'Personnel\Model\PatientTable' );
		}
		return $this->patientTable;
	}
	public function getPersonnelTable() {
		if (! $this->personnelTable) {
			$sm = $this->getServiceLocator ();
			$this->personnelTable = $sm->get ( 'Personnel\Model\PersonnelTable' );
		}
		return $this->personnelTable;
	}
	public function getMedecinTable() {
		if (! $this->medecinTable) {
			$sm = $this->getServiceLocator ();
			$this->medecinTable = $sm->get ( 'Personnel\Model\MedecinTable' );
		}
		return $this->medecinTable;
	}
	public function getMedicoTechniqueTable() {
		if (! $this->medicotechniqueTable) {
			$sm = $this->getServiceLocator ();
			$this->medicotechniqueTable = $sm->get ( 'Personnel\Model\MedicotechniqueTable' );
		}
		return $this->medicotechniqueTable;
	}
	public function getLogistiqueTable() {
		if (! $this->logistiqueTable) {
			$sm = $this->getServiceLocator ();
			$this->logistiqueTable = $sm->get ( 'Personnel\Model\LogistiqueTable' );
		}
		return $this->logistiqueTable;
	}
	public function getAffectationTable() {
		if (! $this->affectationTable) {
			$sm = $this->getServiceLocator ();
			$this->affectationTable = $sm->get ( 'Personnel\Model\AffectationTable' );
		}
		return $this->affectationTable;
	}
	public function getTypePersonnelTable() {
		if (! $this->typePersonnelTable) {
			$sm = $this->getServiceLocator ();
			$this->typePersonnelTable = $sm->get ( 'Personnel\Model\TypepersonnelTable' );
		}
		return $this->typePersonnelTable;
	}
	public function getServiceTable() {
		if (! $this->serviceTable) {
			$sm = $this->getServiceLocator ();
			$this->serviceTable = $sm->get ( 'Personnel\Model\ServiceTable' );
		}
		return $this->serviceTable;
	}
	public function getTransfertTable() {
		if (! $this->transfertTable) {
			$sm = $this->getServiceLocator ();
			$this->transfertTable = $sm->get ( 'Personnel\Model\TransfertTable' );
		}
		return $this->transfertTable;
	}
	public function getHopitalServiceTable() {
		if (! $this->hopitalserviceTable) {
			$sm = $this->getServiceLocator ();
			$this->hopitalserviceTable = $sm->get ( 'Personnel\Model\HopitalServiceTable' );
		}
		return $this->hopitalserviceTable;
	}
	public function getInterventionTable() {
		if (! $this->interventionTable) {
			$sm = $this->getServiceLocator ();
			$this->interventionTable = $sm->get ( 'Personnel\Model\InterventionTable' );
		}
		return $this->interventionTable;
	}
	
	/**
	 ***************************************************************************
	 *
	 *==========================================================================
	 *
	 * *************************************************************************
	 */
	Public function getDateHelper(){
		$this->dateHelper = new DateHelper();
	}
	
	public function baseUrl(){
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
		return $tabURI[0];
	}
	
	public function baseUrlRacine() {
		$baseUrl = $_SERVER ['SCRIPT_FILENAME'];
		$tabURI = explode ( 'public', $baseUrl );
		return $tabURI[0];
	}
	
	public function getFormPersonnel() {
		if (! $this->formPersonnel) {
			$this->formPersonnel = new PersonnelForm();
		}
		return $this->formPersonnel;
	}
	
	public function  indexAction(){
		$this->layout()->setTemplate('layout/personnel');
		$view = new ViewModel();
		return $view;
	}
	
	public function dossierPersonnelAction(){
		$user = $this->layout()->user;
		$id_employe = $user['id_personne'];
		
		$this->layout()->setTemplate('layout/personnel');
		$this->getFormPersonnel();
		$formPersonnel = $this->formPersonnel;
		
		$formPersonnel->get('nationalite')->setvalueOptions($this->getPatientTable()->listeDeTousLesPays());
		$data = array('nationalite' => 'Sénégal');
		$formPersonnel->get('service_accueil')->setValueOptions($this->getPatientTable()->listeServices());
		$formPersonnel->get('type_personnel')->setValueOptions($this->getPatientTable()->getTypePersonnel());

		$formPersonnel->populateValues($data);
		
		$request = $this->getRequest();
 		if ($request->isPost()) {
 			$personnel =  new Personnel();
 			$formPersonnel->setInputFilter($personnel->getInputFilter());
 			$formPersonnel->setData($request->getPost());
 			if ($formPersonnel->isValid()) {
 				$personnel->exchangeArray($formPersonnel->getData());
				//**************************************************************
			
			    //============ ENREGISTREMENT DE L'ETAT CIVIL ==================
			
			    //**************************************************************
				$today = new \DateTime ( 'now' );
				$nomPhoto = $today->format ( 'dmy_His' );
				$fileBase64 = $this->params ()->fromPost ('fichier_tmp');
				$fileBase64 = substr($fileBase64, 23);
				
				if($fileBase64){
					$img = imagecreatefromstring(base64_decode($fileBase64));
				} else {
					$img = false;
				}
				
			    if ($img != false) {
			    	imagejpeg ( $img, $this->baseUrlRacine().'public/images/photos_personnel/' . $nomPhoto . '.jpg' );

			    	//ON ENREGISTRE AVEC LA PHOTO
			    	$id_personnel = $this->getPersonnelTable()->savePersonnel($personnel,$nomPhoto);
			    	
			    } else {
			    	//ON ENREGISTRE SANS LA PHOTO
			    	$id_personnel = $this->getPersonnelTable()->savePersonnel($personnel);
			    	
			    }
			    
			    //***************************************************************
			    	
			    //============ ENREGISTREMENT DES DONNEES DES COMPLEMENTS =======
			    	
			    //***************************************************************
			    
			    if($personnel->type_personnel == 1) {
			    	$this->getMedecinTable()->saveMedecin($personnel, $id_personnel, $id_employe);
			    }else
			    if($personnel->type_personnel == 2){
			    	$this->getMedicoTechniqueTable()->saveMedicoTechnique($personnel, $id_personnel, $id_employe);
			    }else
			    if($personnel->type_personnel == 3){
			    	$this->getLogistiqueTable()->saveLogistique($personnel, $id_personnel, $id_employe);
			    }
			   		    	
 			    //***************************************************************
			    	    
 			    //========== ENREGISTREMENT DES DONNEES SUR L'AFFECTATION =======
			    	    
 			    //***************************************************************
			    
 			    $this->getAffectationTable()->saveAffectation($personnel, $id_personnel, $id_employe);
			    	
 			    
 			    return $this->redirect ()->toRoute ( 'personnel', array ('action' => 'liste-personnel'));
 			} 
 			//Quelque soit alpha le formulaire doit etre valide avant d'enregistrer les donnees. Donc pas besoin de ELSE
 		}
		
		return array (
				'form' =>$formPersonnel,
		);
	}
	
	public function listePersonnelAjaxAction() {
		$output = $this->getPersonnelTable()->getListePersonnel();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listePersonnelAction(){
		$this->layout()->setTemplate('layout/personnel');
		
		$formTypePersonnel = new TypePersonnelForm();
		$formTypePersonnel->get('type_personnel')->setvalueOptions($this->getTypePersonnelTable()->listeTypePersonnel());
		
		return array(
			'form' => $formTypePersonnel,
		);
	}
	
	public function supprimerAction() {
		$id_personne = (int) $this->params() ->fromPost('id');
		$agent = $this->getPersonnelTable()->getPersonne($id_personne);

		if($agent->type_personnel == 'Logistique'){
			$donneesComplement = $this->getLogistiqueTable()->deleteLogistique($id_personne);
		}else
		if($agent->type_personnel == 'Médico-technique'){
			$donneesComplement = $this->getMedicoTechniqueTable()->deleteMedicoTechnique($id_personne);
		}else
		if($agent->type_personnel == 'Médecin'){
			$donneesComplement = $this->getMedecinTable()->deleteMedecin($id_personne);
		}
		
		$this->getAffectationTable()->deleteAffectation($id_personne);
		$this->getPersonnelTable()->deletePersonne($id_personne);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode (  ) );
	}
	
	public function infoPersonnelAction() {
		
		//$identif = (int) $this->params() ->fromPost('identif', 0);
		
		$id_personne = (int) $this->params() ->fromPost('id');
		$this->getDateHelper();
		
		$unAgent = $this->getPersonnelTable()->getPersonne($id_personne);
		$photoAgent = $this->getPersonnelTable()->getPhoto($id_personne);
		
		/****************************************************************
		 * ======= COMPLEMENTS DES INFORMATIONS SUR L'AGENT==============
		 * **************************************************************
		 * **************************************************************/
 		$donneesComplement = "";
 		if($unAgent['id_type_employe'] == 3){
 			$donneesComplement = $this->getLogistiqueTable()->getLogistique($id_personne);
 		}
 		else
		if($unAgent['id_type_employe']  == 2){
			$donneesComplement = $this->getMedicoTechniqueTable()->getMedicoTechnique($id_personne);
		}else
		if($unAgent['id_type_employe']  == 1){
			$donneesComplement = $this->getMedecinTable()->getMedecin($id_personne);
		}
		
		/****************************************************************
		 * = COMPLEMENTS DES INFORMATIONS SUR L'AFFECTATION DE L'AGENT ==
		 * **************************************************************
		 * **************************************************************/
		 $donneesAffectation = $this->getAffectationTable()->getAffectation($id_personne);
		 if($donneesAffectation){
		 	$leService = $this->getServiceTable()->getServiceAffectation($donneesAffectation->service_accueil);
		 }
		
		/****************************************************************
		 * ========= AFFICHAGE DES INFORMATION SUR LA VUE ===============
		 * **************************************************************
		 * **************************************************************/
		 
		 $date_naissance = $unAgent['DATE_NAISSANCE'];
		 if($date_naissance){ $date_naissance = $this->dateHelper->convertDate($date_naissance); } else { $date_naissance = ""; } 
		$html ="<div style='width: 100%;'>
		
		       <img id='photo' src='".$this->baseUrl()."public/images/photos_personnel/" . $photoAgent . "'  style='float:left; margin-left:20px; margin-right:40px; width:105px; height:105px;'/>
		
		       <p style='color: white; opacity: 0.09;'>
		         <img id='photo' src='".$this->baseUrl()."public/images/photos_personnel/" . $photoAgent . "'   style='float:right; margin-right:15px; width:95px; height:95px;'/>
		       </p>
		         		
		       <table>
                 <tr>
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Pr&eacute;nom</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['PRENOM']."</p></div>
			   	   </td>

			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Date de naissance</a><br><p style='font-weight: bold;font-size: 17px;'>".$date_naissance."</p></div>
			   	   </td>

			       <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['TELEPHONE']."</p></div>
			   	   </td>
			   		   		
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   	   </td>
			   		   		
			      </tr>
			   		   		
			   	  <tr>
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Nom</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['NOM']."</p></div>
			   	   </td>

			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Lieu de naissance</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['LIEU_NAISSANCE']."</p></div>
			   	   </td>

			       <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute;</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['NATIONALITE_ACTUELLE']."</p></div>
			   	   </td>
			   		   		
			   	   <td style='width:160px; font-family: police1;font-size: 12px; padding-left: 15px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>@-Email</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['EMAIL']."</p></div>
			   	   </td>
			   		   		
			      </tr>
			   		   		
			   	  <tr>
			   	   <td style='width:160px; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Sexe</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['SEXE']."</p></div>
			   	   </td>

			   	   <td style='width:160px; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Profession</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['PROFESSION']."</p></div>
			   	   </td>

			       <td style='width:180px; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Adresse</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['ADRESSE']."</p></div>
			   	   </td>
			   		   		
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   	   </td>
			   		   		
			      </tr>
			   		   		
		        </table>

			    <div id='titre_info_deces'>Compl&eacute;ments informations (Personnel ".$unAgent['NOM_TYPE'].") </div>
			    <div id='barre'></div>";
		   		   		
		if($unAgent['id_type_employe'] == 3 && $donneesComplement){
			$html .="<table style='margin-top:10px; margin-left:17%;'>";
			$html .="<tr>";
			$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Matricule:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->matricule_logistique."</div></td>";
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Grade:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->grade_logistique."</div></td>";
			$html .="<td style='width:270px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Domaine:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->domaine_logistique."</div></td>";
			$html .="<td style='width:230px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'></a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'></div></td>";
			$html .="</tr>";
			$html .="</table>";
		}else
		if($unAgent['id_type_employe'] == 2 && $donneesComplement){
			$html .="<table style='margin-top:10px; margin-left:185px;'>";
			$html .="<tr>";
			$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Matricule:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->matricule_medico."</div></td>";
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Grade:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->grade_medico."</div></td>";
			$html .="<td style='width:270px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Domaine:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->domaine_medico."</div></td>";
			$html .="<td style='width:230px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Fonction:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->autres."</div></td>";
			$html .="</tr>";
			$html .="</table>";
		}else
		if($unAgent['id_type_employe'] == 1 && $donneesComplement){
			$html .="<table style='margin-top:10px; margin-left:185px;'>";
			$html .="<tr>";
			$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Matricule:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->matricule."</div></td>";
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Grade:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->grade."</div></td>";
			$html .="<td style='width:270px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Specialit&eacute;:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->specialite."</div></td>";
			$html .="<td style='width:230px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Fonction:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->fonction."</div></td>";
			$html .="</tr>";
			$html .="</table>";
		}

		
		$html .="<div id='titre_info_deces' style='margin-top: 25px;' >Affectation</div>";
		$html .="<div id='barre'></div>";
		if($donneesAffectation){
			$dateAffectDeb = $donneesAffectation->date_debut;
			$dateAffectFin = $donneesAffectation->date_fin;
			
		$html .="<table style='margin-top:10px; margin-left:185px; margin-bottom: 30px;'>";
		$html .="<tr>";
		$html .="<td style='width:310px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Service:</a><div id='inform' style='float:left; font-weight:bold; font-size:15px;'>".$leService->nom."</div></td>";

		if($dateAffectDeb){ 
			$dateAffectDeb = $this->dateHelper->convertDate($donneesAffectation->date_debut); 
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Date d&eacute;but:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$dateAffectDeb."</div></td>";
		} 
		
		if($dateAffectFin){ 
			$dateAffectFin = $this->dateHelper->convertDate($donneesAffectation->date_fin); 
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Date fin:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$dateAffectFin."</div></td>";
		} 


		if($donneesAffectation->numero_os){
			$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Num&eacute;ro OS:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesAffectation->numero_os."</div></td>";
		}

		$html .="</tr>";
		$html .="</table>";
		}
		
/*
		//APPLIQUER UNIQUEMENT SUR L'INTERFACE DE VISUALISATION SUR LA LISTE DES AGENTS TRANSFERES
		
		if($identif == 1){
			
			$data = array(
					'id_personne' => $id_personne,
					'id_service_origine' => $donneesAffectation->service_accueil
			);
			
			$transfert = $this->getTransfertTable()->getTransfert($data);
			
			if($transfert) {
				$html .="<div id='titre_info_deces' style='margin-top: 25px;' >Transfert ( ".$transfert->type_transfert." ) </div>";
				$html .="<div id='barre'></div>";
			
				if($transfert->type_transfert == "Interne") {
				
					$leService = $this->getServiceTable()->getServiceAffectation($transfert->service_accueil);
					$html .="<table style='margin-top:10px; margin-left:185px; margin-bottom: 30px;'>";
					$html .="<tr>";
					$html .="<td style='width:310px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Service:</a><div id='inform' style='float:left; font-weight:bold; font-size:15px;'>".$leService->nom."</div></td>";
					$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Date :</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$this->dateHelper->convertDate($transfert->date_debut)."</div></td>";
					$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Date fin:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>  </div></td>";
					$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Num&eacute;ro OS:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesAffectation->numero_os."</div></td>";
					$html .="</tr>";
					$html .="</table>";
				
					$html .="<table style='margin-top:10px; margin-left:185px;'>";
					$html .="<tr>";
					$html .="<td style='padding-top: 10px; padding-bottom: 0px; padding-right: 30px'><a style='text-decoration:underline; font-size:13px;'>Motif du transfert:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'> ".$transfert->motif_transfert." </p></td>";
					$html .="<td style='padding-top: 10px; padding-bottom: 0px; padding-right: 20px'><a style='text-decoration:underline; font-size:13px;'>Note:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'> $transfert->note </p></td>";
					$html .="</tr>";
					$html .="</table>";
				}
				else {
					
					$leService = $this->getServiceTable()->getServiceAffectation($transfert->service_accueil_externe);
					$html .="<table style='margin-top:10px; margin-left:185px; margin-bottom: 30px;'>";
					$html .="<tr>";
					$html .="<td style='width:310px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Service:</a><div id='inform' style='float:left; font-weight:bold; font-size:15px;'>".$leService->nom."</div></td>";
					$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Date :</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$this->dateHelper->convertDate($transfert->date_debut)."</div></td>";
					$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Date fin:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'> </div></td>";
					$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Num&eacute;ro OS:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesAffectation->numero_os."</div></td>";
					$html .="</tr>";
					$html .="</table>";
					
					$html .="<table style='margin-top:10px; margin-left:185px;'>";
					$html .="<tr>";
					$html .="<td style='padding-top: 10px; padding-bottom: 0px; padding-right: 30px'><a style='text-decoration:underline; font-size:13px;'>Motif du transfert:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'> ".(String)$transfert->motif_transfert_externe." </p></td>";
					$html .="<td style='padding-top: 10px; padding-bottom: 0px; padding-right: 20px'><a style='text-decoration:underline; font-size:13px;'>Note:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'>  </p></td>";
					$html .="</tr>";
					$html .="</table>";
				}
			}
		}*/
		
	    $html .="<div style='width: 100%; height: 100px;'>
	    		 <div style='color: white; opacity: 1; width:95px; height:40px; padding-right:15px; float:right;'>
                    <img  src='".$this->baseUrl()."public/images_icons/fleur1.jpg' />
                 </div>
                
			     <div class='block' id='thoughtbot' style='vertical-align: bottom; padding-left:60%; margin-bottom: 40px; padding-top: 35px; font-size: 18px; font-weight: bold;'><button type='submit' id='terminer'>Terminer</button></div>
                 </div>";

		$html .="</div>";
		        
	    $html .="<script>listepatient();</script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function modifierDossierAction() {
		$this->layout()->setTemplate('layout/personnel');
		$this->getDateHelper();
		
		$user = $this->layout()->user;
		$id_employe = $user['id_personne'];
		
		$id_personne = (int) $this->params()->fromRoute('val', 0);
		
		if (!$id_personne) {
			return $this->redirect()->toRoute('personnel', array(
					'action' => 'dossier-personnel'
			));
		}

		/****************************************************************
		 * ============== INITIALISATION DU FORMULAIRE ==================
		 * **************************************************************
		 * **************************************************************/
		
		$this->getFormPersonnel();
		$formPersonnel = $this->formPersonnel;
		$formPersonnel->get('nationalite')->setvalueOptions($this->getPatientTable()->listeDeTousLesPays());
		$formPersonnel->get('type_personnel')->setValueOptions($this->getPatientTable()->getTypePersonnel());
		$formPersonnel->get('service_accueil')->setValueOptions($this->getPatientTable()->listeServices());
		
		/****************************************************************
		 * ============= ENREGISTREMENT DES MODIFICATIONS ===============
		 * **************************************************************
		 * **************************************************************/
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			$personnel =  new Personnel();
			$formPersonnel->setInputFilter($personnel->getInputFilter());
			
			$formPersonnel->setData($request->getPost());
			
			if ($formPersonnel->isValid()) {
				$personnel->exchangeArray($formPersonnel->getData());

				/*************************************************************
				 ============ ENREGISTREMENT DE L'ETAT CIVIL =================
				 *************************************************************
				 *************************************************************/
				$today = new \DateTime ( 'now' );
				$nomPhoto = $today->format ( 'dmy_His' );
				$fileBase64 = $this->params ()->fromPost ('fichier_tmp');
				$fileBase64 = substr($fileBase64, 23);
				
				if($fileBase64){
					$img = imagecreatefromstring(base64_decode($fileBase64));
				} else {
					$img = false;
				}
				$anciennePhoto = $this->getPersonnelTable()->getPersonne($id_personne)['PHOTO'];
				
				if ($img != false) {
					if($anciennePhoto){ //SI LA PHOTO EXISTE BIEN ELLE EST SUPPRIMER DU DOSSIER POUR ETRE REMPLACER PAR LA NOUVELLE
						unlink ( $this->baseUrlRacine().'public/images/photos_personnel/' . $anciennePhoto . '.jpg' );
					}
					imagejpeg ( $img, $this->baseUrlRacine().'public/images/photos_personnel/' . $nomPhoto . '.jpg' );
					
					//ON ENREGISTRE AVEC LA NOUVELLE PHOTO
					$id_personnel = $this->getPersonnelTable()->savePersonnel($personnel,$nomPhoto);
				} else {
					//PAS DE NOUVELLE PHOTO
					$id_personnel = $this->getPersonnelTable()->savePersonnel($personnel,$anciennePhoto);
				}
				 
				/***************************************************************
				 ============ ENREGISTREMENT DES DONNEES DES COMPLEMENTS =======
				 ***************************************************************
				 ***************************************************************/
				if($personnel->type_personnel == 1) { 
					$this->getMedicoTechniqueTable()->deleteMedicoTechnique($id_personne);
					$this->getLogistiqueTable()->deleteLogistique($id_personne);
					$this->getMedecinTable()->saveMedecin($personnel, $id_personne, $id_employe);
				}else
				if($personnel->type_personnel == 2){
					$this->getMedecinTable()->deleteMedecin($id_personne);
					$this->getLogistiqueTable()->deleteLogistique($id_personne);
					$this->getMedicoTechniqueTable()->saveMedicoTechnique($personnel, $id_personne, $id_employe);
				}else
				if($personnel->type_personnel == 3){
					$this->getMedecinTable()->deleteMedecin($id_personne);
					$this->getMedicoTechniqueTable()->deleteMedicoTechnique($id_personne);
					$this->getLogistiqueTable()->saveLogistique($personnel, $id_personne, $id_employe);
				}
				
				/***************************************************************
				 ============ ENREGISTREMENT DES DONNEES SUR L'AFFECTATION =====
				 ***************************************************************
				 ***************************************************************/
				 
				$this->getAffectationTable()->saveAffectation($personnel, $id_personne, $id_employe);
				
				// Redirection a la liste du personnel
				return $this->redirect()->toRoute('personnel', array('action' =>'liste-personnel') );
			}
		}
		
		/****************************************************************
		 * ====== AFFICHAGE DES DONNEES POUR LES MODIFICATIONS ==========
		 * **************************************************************
		 * **************************************************************/
		try {
			$laPersonne = $this->getPersonnelTable()->getPersonne($id_personne);
			$donneesPersonnel = new Personnel();

			if($laPersonne){
				$donneesPersonnel ->sexe = $laPersonne['SEXE'];
				$donneesPersonnel ->prenom = $laPersonne['PRENOM'];
				$donneesPersonnel ->nom = $laPersonne['NOM'];
				$donneesPersonnel ->date_naissance = $laPersonne['DATE_NAISSANCE'];
				$donneesPersonnel ->lieu_naissance = $laPersonne['LIEU_NAISSANCE'];
				$donneesPersonnel ->nationalite = $laPersonne['NATIONALITE_ACTUELLE'];
				$donneesPersonnel ->situation_matrimoniale = $laPersonne['SITUATION_MATRIMONIALE'];
				$donneesPersonnel ->adresse = $laPersonne['ADRESSE'];
				$donneesPersonnel ->telephone = $laPersonne['TELEPHONE'];
				$donneesPersonnel ->email = $laPersonne['EMAIL'];
				$donneesPersonnel ->profession = $laPersonne['PROFESSION'];
				$donneesPersonnel ->id_personne = $laPersonne['ID_PERSONNE'];
			}
			$type_personnel = $laPersonne['id_type_employe'];
			$donneesComplement= "";
			
			if($type_personnel == 1){
				$donneesComplement = $this->getMedecinTable()->getMedecin($id_personne);
			}
			else
			if($type_personnel == 2){
				$donneesComplement = $this->getMedicoTechniqueTable()->getMedicoTechnique($id_personne);
			}
			else
			if($type_personnel == 3){
				$donneesComplement = $this->getLogistiqueTable()->getLogistique($id_personne);
			}
			
			$donneesAffectation = $this->getAffectationTable()->getAffectation($id_personne);
			
		}
		catch (\Exception $ex) {
			return $this->redirect()->toRoute('personnel', array(
					'action' => 'liste-personnel'
			));
		}
		
		$data = array();
		if($laPersonne){
			if($laPersonne['DATE_NAISSANCE']){ $data['date_naissance'] = $this->dateHelper->convertDate($laPersonne['DATE_NAISSANCE']); } else { $data['date_naissance'] = ""; }
		}
		if($donneesAffectation){
			if($donneesAffectation->date_debut){ $data['date_debut'] = $this->dateHelper->convertDate($donneesAffectation->date_debut); } else { $data['date_debut'] = ""; }
			if($donneesAffectation->date_fin){ $data['date_fin'] = $this->dateHelper->convertDate($donneesAffectation->date_fin); } else { $data['date_fin'] = ""; }
		}
		
 		$laPhoto = $laPersonne['PHOTO'];
 		if(!$laPhoto){ $laPhoto = 'identite'; }
 		
 		if($donneesPersonnel){ $formPersonnel->bind($donneesPersonnel); }
 		if($donneesComplement){ $formPersonnel->bind($donneesComplement); }
 		if($donneesAffectation){ $formPersonnel->bind($donneesAffectation); }
 		
 		$formPersonnel->populateValues($data);
 		
 		//var_dump($id_personne); exit();

		return array (
				'photo' => $laPhoto,
				'type_personnel' => $type_personnel,
				'id_personne' => $id_personne,
				'form' => $formPersonnel,
		);
	}
	
	public function listePersonnelTransfertAjaxAction() {
		$personnel = $this->getPersonnelTable();
		$output = $personnel->getListeRechercheTransfertPersonnel();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	/**
	 * Pour avoir une vue sur l'agent
	 */
	public function vueAgentPersonnelAction(){
		
		$id_personne = ( int ) $this->params ()->fromPost ( 'id', 0 );
		
		$unAgent = $this->getPersonnelTable()->getPersonne($id_personne);
 		$photo = $this->getPersonnelTable()->getPhoto($id_personne);
		

 		$affectation = $this->getAffectationTable()->getServiceAgentAffecter($id_personne);
 		$service = $this->getServiceTable()->getServiceAffectation($affectation);
 		if($service){ $nomService = $service->nom;} else {$nomService = null;}
 		
		$this->getDateHelper();
		$date = $this->dateHelper->convertDate( $unAgent->date_naissance );
		
		$html  = "<div style='width:100%;'>";
			
		$html .= "<div style='width: 18%; height: 180px; float:left;'>";
		$html .= "<div id='photo' style='float:left; margin-left:40px; margin-top:10px; margin-right:30px;'> <img style='width:105px; height:105px;' src='".$this->baseUrl()."public/images/photos_personnel/" . $photo . "' ></div>";
		$html .= "</div>";
			
		$html .= "<div style='width: 65%; height: 180px; float:left;'>";
		$html .= "<table style='margin-top:10px; float:left; width: 100%;'>";
		$html .= "<tr style='width: 100%;'>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unAgent->nom . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Lieu de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unAgent->lieu_naissance . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; d'origine:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unAgent->nationalite . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'></td>";
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unAgent->prenom . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unAgent->telephone . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Sit. matrimoniale:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unAgent->situation_matrimoniale . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Email:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unAgent->email . "</p></td>";
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $date . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unAgent->adresse . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Profession:</a><br><p style=' font-weight:bold; font-size:17px;'>" .  $unAgent->profession . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'></td>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .="</div>";
			
		$html .= "<div style='width: 17%; height: 180px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:20px; margin-left:25px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->baseUrl()."public/images/photos_personnel/" . $photo . "'></div>";
		$html .= "</div>";
			
		$html .= "</div>";
		
		//UNIQUEMENT POUR L'INTERFACE DE MODIFICATION
		$identif = ( int ) $this->params ()->fromPost ( 'identif', 0 );
		if($identif == 1){
			$html .= $this->modificationTransfert($id_personne, $affectation);
		}
		//SCRIPT UTILISER UNIQUEMENT DANS L'INTERFACE TRANSFERT ET INTERVENTION D'UN AGENT
		$html .="<script> 
				  //TRANSFERT INTERNE
				    $('#service_origine').val('".$nomService."');
				    $('#service_origine').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'17px'});
					$('#service_origine').attr('readonly',true);

				    $('#service_accueil, #id_service').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				    $('#motif_transfert, #motif_intervention').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'15px'});
				    $('#note, #note_externe').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'15px'});
				    
				  //TRANSFERT EXTERNE
				    $('#service_origine_externe').val('".$nomService."');
				    $('#service_origine_externe').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'17px'});
					$('#service_origine_externe').attr('readonly',true);

				    $('#hopital_accueil').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				    $('#service_accueil_externe, #id_service_externe').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				    $('#motif_transfert_externe, #motif_intervention_externe').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				 
				    $('#date_debut, #date_fin, #date_debut_externe, #date_fin_externe').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				 </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	/**
	 * Pour la visualisation de quelques d�tails sur l'agent
	 */
	public function popupAgentPersonnelAction() {
		
			$id_personne = (int)$this->params()->fromPost('id');
			$unAgent = $this->getPersonnelTable()->getPersonne($id_personne);
			$photo = $this->getPersonnelTable()->getPhoto($id_personne);
		
			$this->getDateHelper();
			$date = $this->dateHelper->convertDate($unAgent->date_naissance);
		
			$html ="<div id='photo' style='float:left; margin-right:20px;' > <img  style='width:105px; height:105px;' src='../images/photos_personnel/".$photo."'></div>";
		
			$html .="<table>";
		
			$html .="<tr>";
			$html .="<td><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>".$unAgent->nom."</p></td>";
			$html .="</tr><tr>";
			$html .="<td><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>".$unAgent->prenom."</p></td>";
			$html .="</tr><tr>";
			$html .="<td><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>".$date."</p></td>";
			$html .="</tr>";
		
			$html .="<tr>";
			$html .="<td><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>".$unAgent->adresse."</p></td>";
			$html .="</tr><tr>";
			$html .="<td><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>".$unAgent->telephone."</p></td>";
			$html .= "</tr>";
		
			$html .="</table>";
		
			$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		    return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		    
	}
	
	//******* Recuperer les services correspondants en cliquant sur un hopital
	public function servicesAction()
	{
		$id=(int)$this->params()->fromPost ('id');
	
		if ($this->getRequest()->isPost()){
			$liste_select = "";
			$services= $this->getServiceTable();
			foreach($services->getServiceHopital($id) as $listeServices){
				$liste_select.= "<option value=".$listeServices['Id_service'].">".$listeServices['Nom_service']."</option>";
			}
				
			$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
			return $this->getResponse ()->setContent(Json::encode ( $liste_select));
		}
	
	}
}