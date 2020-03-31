<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Predicate\In;
use Consultation\View\Helper\DateHelper;

class ConsultationTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getConsultation($idcons){
		$rowset = $this->tableGateway->select ( array (
				'idcons' => $idcons
		) );
		$row =  $rowset->current ();
 		if (! $row) {
 			throw new \Exception ( "Could not find row $idcons" );
 		}
		return $row;
	}
	
	
	/**
	 * R�cup�rer la premiere consultation g�n�rale du patient diff�rente des consultations de suivi
	 */
	public function getConsultationPatientDifferentDeSuivi($idpatient){
	    $sql = new Sql ($this->tableGateway->getAdapter());
	    
	    $subselect2 = $sql->select ()->from ( array ( 'c' => 'consultation' ));
	    $subselect2->where(array('idpatient' => $idpatient));
	    return $sql->prepareStatementForSqlObject($subselect2)->execute()->current();
	}
	
	/**
	 * V�rifier si le patient a d�j� une consultation de suivi
	 */
	public function getConsultationDeSuivi($idpatient){
	    $sql = new Sql ($this->tableGateway->getAdapter());
	    $subselect1 = $sql->select()->from(array('sp'=>'suivi_patient'))->columns(array('*'));
	    $subselect1->where(array('sp.idpatient' => $idpatient));
	    return $sql->prepareStatementForSqlObject($subselect1)->execute()->current();
	}
	
	public function getConsultationDeSuiviDuPatient($idsuiv, $idpatient){
	    $sql = new Sql ($this->tableGateway->getAdapter());
	    $subselect1 = $sql->select()->from(array('sp'=>'suivi_patient'))->columns(array('*'));
	    $subselect1->where(array('sp.idpatient' => $idpatient, 'sp.idsuiv' => $idsuiv));
	    return $sql->prepareStatementForSqlObject($subselect1)->execute()->current();
	}
	
	
	public function addConsultation($values, $idemploye){
		$date = (new \DateTime())->format('Y-m-d');
		$heure = (new \DateTime())->format('H:i:s');
	
		$dataconsultation = array(
		    'idcons'=> $values["idcons"],
		    'idpatient'=> $values["idpatient"],
		    'idadmission'=> $values["idadmission"],
		    'date'=> $date,
		    'heure' => $heure,
		    'idemploye' => $idemploye
		);
		
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->insert()->into('consultation')->values($dataconsultation);
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function addConsultationSuiv($values, $idemploye){
	    $date = (new \DateTime())->format('Y-m-d');
	    $heure = (new \DateTime())->format('H:i:s');
	
	    $dataconsultationsuiv = array(
	        'idsuiv'=> $values["idsuiv"],
	        'idcons'=> $values["idcons"],
	        'idpatient'=> $values["idpatient"],
	        'idadmission'=> $values["idadmissionsuiv"],
	        'date'=> $date,
	        'heure' => $heure,
	        'idemploye' => $idemploye
	    );
	    
// 	    echo "<pre>";
// 	    var_dump($dataconsultationsuiv); exit();
// 	    echo "</pre>";
	    
	    $sql = new Sql($this->tableGateway->getAdapter());
	    $sQuery = $sql->insert()->into('suivi_patient')->values($dataconsultationsuiv);
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
	public function updateConsultations($values, $idemploye){
		$this->tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();
	
		try {
	
			$idcons = $values["idcons"];
			
			$dataconsultation = array(
			    'idemploye' => $idemploye
			);
	
			$this->tableGateway->update($dataconsultation, array('idcons' => $idcons));
	
			$this->tableGateway->getAdapter()->getDriver()->getConnection()->commit();
		} catch (\Exception $e) {
			$this->tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
		}
		
	}
	
	
	
	
	//********** RECUPERER LA LISTE DES CONSULTATIONS DU MEDECIN *********
	//********** RECUPERER LA LISTE DES CONSULTATIONS DU MEDECIN *********
	public function getListeConsultations(){
	
		$db = $this->tableGateway->getAdapter();
		$date = (new \DateTime())->format('Y-m-d');
		
		$aColumns = array('numero_dossier', 'Nom','Prenom','Datenaissance','Sexe', 'Adresse', 'id', 'id2');
		
		//Liste des pateints admis aujourdhui et d�j� consult�s 
		$sql2 = new Sql ($db );
		$subselect2 = $sql2->select()->from(array('cons'=>'consultation'))->columns(array('idpatient'))->where(array('date' => $date));
		
		//Liste des pateints admis aujourdhui et d�j� suivi
		$sql3 = new Sql ($db );
		$subselect3 = $sql3->select()->from(array('supa'=>'suivi_patient'))->columns(array('idpatient'))->where(array('date' => $date));
		
		//Liste des patients admis aujourdhui et non encore consult�s 
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat'   => 'patient'))->columns(array('*'))
		->join(array('pers'  => 'personne'), 'pat.idpersonne = pers.ID_PERSONNE' , array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE', 'Age'=> 'AGE', 'Nationalite'=>'NATIONALITE_ACTUELLE','id'=>'ID_PERSONNE', 'id2'=>'ID_PERSONNE'))
		->join(array('admis' => 'admission'), 'admis.idpatient = pers.ID_PERSONNE' , array('dateadmission','idadmission'))
		//->where(array('dateadmission' => $date, new NotIn ( 'pat.idpersonne', $subselect2 ),  new NotIn ( 'pat.idpersonne', $subselect3 )))
		->group('pat.idpersonne')
		->order('admis.idadmission ASC');
		
		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		$iFilteredTotal = count($rResultFt);
		
		$rResult = $rResultFt;
		
		$output = array(
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
		);
		
		/*
		 * $Control pour convertir la date en fran�ais
		*/
		$Control = new DateHelper();
			
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
			
		/*
		 * Pr�parer la liste
		*/
 		foreach ( $rResult as $aRow )
 		{
 			$row = array();
 			for ( $i=0 ; $i<count($aColumns) ; $i++ )
 			{
				if ( $aColumns[$i] != ' ' )
				{
					/* General output */
					if ($aColumns[$i] == 'Nom'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
					}
		
					else if ($aColumns[$i] == 'Prenom'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
					}
		
					else if ($aColumns[$i] == 'Datenaissance') {
							
						//$date_naissance = $aRow[ $aColumns[$i] ];
						//if($date_naissance){ $row[] = $Control->convertDate($aRow[ $aColumns[$i] ]); }else{ $row[] = null;}

					    $row[] = $aRow[ 'Age' ];
					}
		
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div>".$aRow[ $aColumns[$i] ]."</div>";
					}
		
					else if ($aColumns[$i] == 'id') {
						$html  ="<infoBulleVue> <a href='".$tabURI[0]."public/consultation/consulter?idadmission=".$aRow[ 'idadmission' ]."&idpatient=".$aRow[ 'id' ]."'>";
						$html .="<img style='display: inline; margin-right: 17%;' src='".$tabURI[0]."public/images_icons/doctor_16.png' title='Consulter'></a></infoBulleVue>";
							
						$html .="<infoBulleVue>";
						$html .="<img style='opacity:0.2; margin-right: 17%;' src='".$tabURI[0]."public/images_icons/11modif.png' ></a></infoBulleVue>";
						
						$row[] = $html;
					}
		
					else {
						$row[] = $aRow[ $aColumns[$i] ];
					}
		
				}
 			}
 			$output['aaData'][] = $row;
 		}
		
		
		/*
		 * SQL queries
		* Liste des patients admis d�j� consult�s aujourd'hui (POUR LA CONSULTATION GLOBALE)
		*/
		$sql2 = new Sql($db);
		$sQuery2 = $sql2->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers'  => 'personne'), 'pat.idpersonne = pers.ID_PERSONNE' , array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE','Nationalite'=>'NATIONALITE_ACTUELLE', 'id'=>'ID_PERSONNE', 'id2'=>'ID_PERSONNE'))
		->join(array('cons' => 'consultation'), 'cons.idpatient = pat.idpersonne', array('Idcons' => 'idcons', 'Date' => 'date') )
		->where(array('cons.date' => $date) )
		->order('cons.idcons DESC');
			
		/* Data set length after filtering */
		$rResult = $sql2->prepareStatementForSqlObject($sQuery2)->execute();

		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
			
		/*
		 * Pr�parer la liste
		*/
		foreach ( $rResult as $aRow )
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $aColumns[$i] != ' ' )
				{
					/* General output */
					if ($aColumns[$i] == 'Nom'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
					}
	
					else if ($aColumns[$i] == 'Prenom'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
					}
	
					else if ($aColumns[$i] == 'Datenaissance') {
							
						$date_naissance = $aRow[ $aColumns[$i] ];
						if($date_naissance){ $row[] = $Control->convertDate($aRow[ $aColumns[$i] ]); }else{ $row[] = null;}
	
					}
	
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div>".$aRow[ $aColumns[$i] ]."</div>";
					}
	
					else if ($aColumns[$i] == 'Date') {
						$row[] = $Control->convertDateTimeHm($aRow[ 'date_enregistrement' ]);
					}
					
					else if ($aColumns[$i] == 'id') {
						$html  ="<infoBulleVue>";
						$html .="<img style='opacity: 0.2; margin-right: 12%;' src='".$tabURI[0]."public/images_icons/doctor_16.png'></infoBulleVue>";
							
						$html .="<infoBulleVue> <a href='".$tabURI[0]."public/consultation/modifier-consultation?idpatient=".$aRow[ 'id' ]."&idcons=".$aRow[ 'Idcons' ]."'>";
						$html .="<img style='display: inline; margin-right: 17%;' src='".$tabURI[0]."public/images_icons/11modif.png' title='Modifier'></a></infoBulleVue>";
						
 						$html .="<infoBulleVue>";
 						$html .="<img style='display: inline; margin-left: 5%; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/tick_16.png' ></infoBulleVue>";
							
						$row[] = $html;
					}
	
					else {
						$row[] = $aRow[ $aColumns[$i] ];
					}
	
				}
			}
			$output['aaData'][] = $row;
		}
		
		

		/*
		 * SQL queries
		 * Liste des patients admis d�j� consult�s aujourd'hui (POUR LA CONSULTATION DE SUIVI)
		 */
		$sql4 = new Sql($db);
		$sQuery4 = $sql4->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers'  => 'personne'), 'pat.idpersonne = pers.ID_PERSONNE' , array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE','Nationalite'=>'NATIONALITE_ACTUELLE', 'id'=>'ID_PERSONNE', 'id2'=>'ID_PERSONNE'))
		->join(array('supa' => 'suivi_patient'), 'supa.idpatient = pat.idpersonne', array('Idsuiv' => 'idsuiv', 'Date' => 'date') )
		->where(array('supa.date' => $date) )
		->order('supa.idsuiv DESC');
			
		/* Data set length after filtering */
		$rResult = $sql4->prepareStatementForSqlObject($sQuery4)->execute();
		//var_dump($rResult->current()); exit();
		
		/*
		 * ADRESSE URL RELATIF
		 */
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
			
		/*
		 * Pr�parer la liste
		 */
		foreach ( $rResult as $aRow )
		{
		    $row = array();
		    for ( $i=0 ; $i<count($aColumns) ; $i++ )
		    {
		        if ( $aColumns[$i] != ' ' )
		        {
		            /* General output */
		            if ($aColumns[$i] == 'Nom'){
		                $row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
		            }
		
		            else if ($aColumns[$i] == 'Prenom'){
		                $row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
		            }
		
		            else if ($aColumns[$i] == 'Datenaissance') {
		                 
		                $date_naissance = $aRow[ $aColumns[$i] ];
		                if($date_naissance){ $row[] = $Control->convertDate($aRow[ $aColumns[$i] ]); }else{ $row[] = null;}
		
		            }
		
		            else if ($aColumns[$i] == 'Adresse') {
		                $row[] = "<div>".$aRow[ $aColumns[$i] ]."</div>";
		            }
		
		            else if ($aColumns[$i] == 'Date') {
		                $row[] = $Control->convertDateTimeHm($aRow[ 'date_enregistrement' ]);
		            }
		             
		            else if ($aColumns[$i] == 'id') {
		                $html  ="<infoBulleVue>";
		                $html .="<img style='opacity: 0.2; margin-right: 12%;' src='".$tabURI[0]."public/images_icons/doctor_16.png'></infoBulleVue>";
		                 
		                $html .="<infoBulleVue> <a href='".$tabURI[0]."public/consultation/modifier-consultation-suivi?idpatient=".$aRow[ 'id' ]."&idsuiv=".$aRow[ 'Idsuiv' ]."'>";
		                $html .="<img style='display: inline; margin-right: 17%;' src='".$tabURI[0]."public/images_icons/11modif.png' title='Modifier le suivi'></a></infoBulleVue>";
		
		                $html .="<infoBulleVue>";
		                $html .="<img style='display: inline; margin-left: 5%; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/tick_16.png' ></infoBulleVue>";
		                 
		                $row[] = $html;
		            }
		
		            else {
		                $row[] = $aRow[ $aColumns[$i] ];
		            }
		
		        }
		    }
		    $output['aaData'][] = $row;
		}
		
		
		return $output;
	}
	
	public function getListeSigne()
	{
	    $sql = new Sql($this->tableGateway->getAdapter());
	    $select = $sql->select('liste_signe')->order('libelle ASC');
	    $result = $sql->prepareStatementForSqlObject($select)->execute();
	
	    $options = array(0 => '');
	    foreach ($result as $data) {
	        $options[$data['id']] = $data['libelle'];
	    }
	    return $options;
	}
	
	
	//********** RECUPERER LA LISTE DES HISTORIQUES CONSULTATIONS DU MEDECIN *********
	//********** RECUPERER LA LISTE DES HISTORIQUES CONSULTATIONS DU MEDECIN *********
	public function getListeHistoriquesConsultations(){
	
	    $db = $this->tableGateway->getAdapter();
	    $date = (new \DateTime())->format('Y-m-d');
	
	    $aColumns = array('numero_dossier', 'Nom','Prenom','Datenaissance','Sexe', 'Adresse', 'id', 'id2');
	
	    //Liste des pateints admis aujourdhui et d�j� consult�s
	    $sql2 = new Sql ($db );
	    $subselect2 = $sql2->select()->from(array('cons'=>'consultation'))->columns(array('idpatient'))->where(array('date' => $date));
	
	    //Liste des pateints admis aujourdhui et d�j� suivi
	    $sql3 = new Sql ($db );
	    $subselect3 = $sql3->select()->from(array('supa'=>'suivi_patient'))->columns(array('idpatient'))->where(array('date' => $date));
	
	    //Liste des patients admis aujourdhui et non encore consult�s
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('pat'   => 'patient'))->columns(array('*'))
	    ->join(array('pers'  => 'personne'), 'pat.idpersonne = pers.ID_PERSONNE' , array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE','Nationalite'=>'NATIONALITE_ACTUELLE','id'=>'ID_PERSONNE', 'id2'=>'ID_PERSONNE'))
	    ->join(array('admis' => 'admission'), 'admis.idpatient = pers.ID_PERSONNE' , array('dateadmission','idadmission'))
	    ->join(array('cons' => 'consultation'), 'cons.idadmission = admis.idadmission', array('Idcons' => 'idcons', 'Date' => 'date') )
	    ->where(array('dateadmission < ?' => $date, new NotIn ( 'pat.idpersonne', $subselect2 ),  new NotIn ( 'pat.idpersonne', $subselect3 )))
	    ->order('admis.idadmission ASC')
	    ->group('pat.idpersonne');
	
	    /* Data set length after filtering */
	    $stat = $sql->prepareStatementForSqlObject($sQuery);
	    $rResultFt = $stat->execute();
	    $iFilteredTotal = count($rResultFt);
	
	    $rResult = $rResultFt;
	
	    $output = array(
	        "iTotalDisplayRecords" => $iFilteredTotal,
	        "aaData" => array()
	    );
	
	    /*
	     * $Control pour convertir la date en fran�ais
	     */
	    $Control = new DateHelper();
	    	
	    /*
	     * ADRESSE URL RELATIF
	     */
	    $baseUrl = $_SERVER['REQUEST_URI'];
	    $tabURI  = explode('public', $baseUrl);
	    	
	    /*
	     * Pr�parer la liste
	     */
	    foreach ( $rResult as $aRow )
	    {
	        $row = array();
	        for ( $i=0 ; $i<count($aColumns) ; $i++ )
	        {
	            if ( $aColumns[$i] != ' ' )
	            {
	                /* General output */
	                if ($aColumns[$i] == 'Nom'){
	                    $row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
	                }
	
	                else if ($aColumns[$i] == 'Prenom'){
	                    $row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
	                }
	
	                else if ($aColumns[$i] == 'Datenaissance') {
	                    	
	                    $date_naissance = $aRow[ $aColumns[$i] ];
	                    if($date_naissance){ $row[] = $Control->convertDate($aRow[ $aColumns[$i] ]); }else{ $row[] = null;}
	
	                }
	
	                else if ($aColumns[$i] == 'Adresse') {
	                    $row[] = "<div>".$aRow[ $aColumns[$i] ]."</div>";
	                }
	
	                else if ($aColumns[$i] == 'id') {
	                    $html  ="<infoBulleVue> <a href='".$tabURI[0]."public/consultation/visualiser-historique-consultations?idcons=".$aRow[ 'Idcons' ]."&idpatient=".$aRow[ 'id' ]."'>";
	                    $html .="<img style='display: inline; margin-right: 17%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='Visualiser'></a></infoBulleVue>";
	                    	
	                    $row[] = $html;
	                }
	
	                else {
	                    $row[] = $aRow[ $aColumns[$i] ];
	                }
	
	            }
	        }
	        $output['aaData'][] = $row;
	    }
	
	    return $output;
	}
	
	
	//********** RECUPERER LA LISTE DES CONSULTATIONS DU MEDECIN *********
	//********** RECUPERER LA LISTE DES CONSULTATIONS DU MEDECIN *********
	public function getListeHistoriquesConsultationsSuivis($idpatient){
	
	    $db = $this->tableGateway->getAdapter();
	    $date = (new \DateTime())->format('Y-m-d');
	
	    $aColumns = array('Date', 'Prenom', 'Nom', 'id');
	
	    /*
	     * SQL queries
	     * Liste des patients admis d�j� consult�s aujourd'hui (POUR LA CONSULTATION DE SUIVI)
	     */
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('pat' => 'patient'))->columns(array('*'))
	    ->join(array('pers' => 'personne'), 'pat.idpersonne = pers.ID_PERSONNE' , array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE','Nationalite'=>'NATIONALITE_ACTUELLE', 'id'=>'ID_PERSONNE'))
	    ->join(array('supa' => 'suivi_patient'), 'supa.idpatient = pat.idpersonne', array('Idsuiv' => 'idsuiv', 'Date' => 'date', 'Heure' => 'heure') )
	    ->join(array('pers2' => 'personne'), 'supa.idemploye = pers2.ID_PERSONNE' , array('NomMed'=>'NOM','PrenomMed'=>'PRENOM'))
	    ->join(array('ps' => 'plainte_suivi'), 'ps.idsuiv = supa.idsuiv' , array('idplainte'))
	    ->where(array('supa.date != ?' => $date, 'supa.idpatient' => $idpatient) )
	    
	    ->order('supa.date DESC')
	    ->group('supa.idsuiv');
	    	
	    /* Data set length after filtering */
	    $stat = $sql->prepareStatementForSqlObject($sQuery);
	    $rResultFt = $stat->execute();
	    $iFilteredTotal = count($rResultFt);
	    
	    $rResult = $rResultFt;
	    	
	    $Control = new DateHelper();
	    
	    $output = array(
	        "iTotalDisplayRecords" => $iFilteredTotal,
	        "aaData" => array()
	    );
	    
	    /*
	     * ADRESSE URL RELATIF
	     */
	    $baseUrl = $_SERVER['REQUEST_URI'];
	    $tabURI  = explode('public', $baseUrl);
	    	
	    
	    //return $rResultFt;
	    
	    /*
	     * Pr�parer la liste
	     */
	    foreach ( $rResult as $aRow )
	    {
	        $row = array();
	        for ( $i=0 ; $i<count($aColumns) ; $i++ )
	        {
	            if ( $aColumns[$i] != ' ' )
	            {
	                /* General output */
	                if ($aColumns[$i] == 'Nom'){
	                    $row[] = $aRow['Prenom'].' '.$aRow[$aColumns[$i]];
	                }
	    
	                else if ($aColumns[$i] == 'Prenom'){
	                    $listeSignes = $this->getListeSigne();
	                    $row[] = "<div>".$listeSignes[$aRow['idplainte']]."</div>";
	                }
	    
	                else if ($aColumns[$i] == 'Date') {
	                    $row[] = $Control->convertDate($aRow['Date']).' - '.$Control->decouperTimeHm($aRow['Heure']);
	                }
	                
	                else if ($aColumns[$i] == 'id') {
	                    $html  ="<a href='".$tabURI[0]."public/consultation/visualiser-consultation-suivi?idpatient=".$aRow[ 'id' ]."&idsuiv=".$aRow[ 'Idsuiv' ]."'  target='_blank'>";
	                    $html .="<img style='display: inline; margin-right: 17%;' src='".$tabURI[0]."public/images_icons/transfert_droite.png' title='Visualiser le suivi'></a>";
	                    
	                    $row[] = $html;
	                }
	    
	                else {
	                    $row[] = $aRow[ $aColumns[$i] ];
	                }
	    
	            }
	        }
	        $output['aaData'][] = $row;
	    }
	    
	    return $output;
	}
	
	
	public function addImagesIconographie($nomimage, $idadmission, $position, $idemploye){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->insert()->into('iconographie_image')
		->values(array('nomimage' => $nomimage, 'idadmission' => $idadmission, 'position' => $position, 'idemploye' => $idemploye));
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function getImagesIconographie($idadmission, $position) {
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select('iconographie_image')->order('idimage DESC')
		->where(array('idadmission' => $idadmission, 'position' => $position));
		 
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function getImageIconographie($id, $idadmission, $position) {
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select('iconographie_image')->order('idimage DESC')
		->where(array('position' => $position, 'idadmission' => $idadmission));
			
		$Result = $sql->prepareStatementForSqlObject($sQuery)->execute();
		
		$i = 1;
		$tabIdImage  = array( $id => 0 );
		$tabNomImage = array( $id => 0 );
		 
		foreach ($Result as $resultat){
			$tabIdImage[$i] = $resultat['idimage'];
			$tabNomImage[$i++] = $resultat['nomimage'];
		}
		
		return  array('idimage' => $tabIdImage[$id], 'nomimage'=> $tabNomImage[$id]);
	}
	
	public function deleteImagesIconographie($idimage, $idadmission){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->delete()->from('iconographie_image')->where(array('idimage' => $idimage, 'idadmission' => $idadmission));
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}	
	
	
	
	
	public function addImagesExamens($nomimage, $idadmission, $examen, $idemploye){
		$table = 'image';
		if($examen == 'Nfs'){$table = $table.'_nfs'; }
		else if($examen == 'Ecg'){$table = $table.'_ecg'; }
		else if($examen == 'Rsd'){$table = $table.'_rsd'; }
		else if($examen == 'Sca'){$table = $table.'_sca'; }
		else if($examen == 'Eco'){$table = $table.'_eco'; }
		
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->insert()->into($table)
		->values(array('nomimage' => $nomimage, 'idadmission' => $idadmission, 'idemploye' => $idemploye));
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function getImagesExamens($idadmission, $examen) {
		$table = 'image';
		if($examen == 'Nfs'){$table = $table.'_nfs'; }
		else if($examen == 'Ecg'){$table = $table.'_ecg'; }
		else if($examen == 'Rsd'){$table = $table.'_rsd'; }
		else if($examen == 'Sca'){$table = $table.'_sca'; }
		else if($examen == 'Eco'){$table = $table.'_eco'; }
		
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select($table)->order('idimage DESC')
		->where(array('idadmission' => $idadmission));
			
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
	
	public function getImageExamen($id, $idadmission, $examen) {
		$table = 'image';
		if($examen == 'Nfs'){ $table = $table.'_nfs'; } 
		else if($examen == 'Ecg'){$table = $table.'_ecg'; }
		else if($examen == 'Rsd'){$table = $table.'_rsd'; }
		else if($examen == 'Sca'){$table = $table.'_sca'; }
		else if($examen == 'Eco'){$table = $table.'_eco'; }
		
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select($table)->order('idimage DESC')->where(array('idadmission' => $idadmission));
			
		$Result = $sql->prepareStatementForSqlObject($sQuery)->execute();
	
		$i = 1;
		$tabIdImage  = array( $id => 0 );
		$tabNomImage = array( $id => 0 );
			
		foreach ($Result as $resultat){
			$tabIdImage[$i] = $resultat['idimage'];
			$tabNomImage[$i++] = $resultat['nomimage'];
		}
	
		return  array('idimage' => $tabIdImage[$id], 'nomimage'=> $tabNomImage[$id]);
	}
	
	public function deleteImageExamen($idimage, $idadmission, $examen){
		$table = 'image';
		if($examen == 'Nfs'){ $table = $table.'_nfs'; }
		else if($examen == 'Ecg'){$table = $table.'_ecg'; }
		else if($examen == 'Rsd'){$table = $table.'_rsd'; }
		else if($examen == 'Sca'){$table = $table.'_sca'; }
		else if($examen == 'Eco'){$table = $table.'_eco'; }
		
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->delete()->from($table)->where(array('idimage' => $idimage, 'idadmission' => $idadmission));
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
	public function deleteMotifAdmission($idcons){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->delete();
	    $select->from('motif_admission');
	    $select->where(array('idcons' => $idcons));
	    $sql->prepareStatementForSqlObject ( $select )->execute();
	}
	
	
	public function updateMotifAdmission($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	     
	    $this->deleteMotifAdmission($values['idcons']);
	    $this->addMotifAdmission($values, $idemploye);
	}
	
	
	public function addMotifAdmission($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	    
	    for($i=1 ; $i<=5 ; $i++){
	
	        if($values['motif_admission'.$i]){
	
	            $idMotif = $values['motif_admission'.$i];
	
	            if($idMotif){
	                $datamotifadmission	= array(
	                    'idcons' => $values['idcons'],
	                    'idlistemotif' => $idMotif,
	                    'idemploye' => $idemploye
	                );
	                
	                $sQuery = $sql->insert()->into('motif_admission')->values($datamotifadmission);
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	            }
	
	        }
	
	    }
	}
	
	public function getMotifAdmission($id){
	    $adapter = $this->tableGateway->getAdapter();
	    $sql = new Sql($adapter);
	    $select = $sql->select();
	    $select->from('motif_admission');
	    $select->columns(array('*'));
	    $select->where(array('idcons'=>$id));
	    $select->order('idmotif ASC');
	    $result = $sql->prepareStatementForSqlObject($select)->execute();
	    return $result;
	}
	
	public function nbMotifs($id){
	    $adapter = $this->tableGateway->getAdapter();
	    $sql = new Sql($adapter);
	    $select = $sql->select();
	    $select->from('motif_admission');
	    $select->columns(array('idmotif'));
	    $select->where(array('idcons'=>$id));
	    $stat = $sql->prepareStatementForSqlObject($select);
	    $result = $stat->execute()->count();
	    return $result;
	}
	
	public function addSignes($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	    
	    if($values['duree_des_signes'] || $values['gravite_des_signes']){
	        $donneeSignes = array(
	            'idcons' => $values['idcons'],
	            'duree' => $values['duree_des_signes'],
	            'gravite' => $values['gravite_des_signes']
	        );
	         
	        $sQuery = $sql->insert()->into('infos_signes')->values($donneeSignes);
	        $sql->prepareStatementForSqlObject($sQuery)->execute();
	    }
	    
	}
	
	public function getSignes($idcons){
	    $adapter = $this->tableGateway->getAdapter();
	    $sql = new Sql($adapter);
	    $select = $sql->select()->from('infos_signes')->columns(array('*'));
	    $select->where(array('idcons'=>$idcons));
	    return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	public function updateSignes($tabDonnees, $idemploye){
	    $idcons = $tabDonnees['idcons'];
	    
	    if($this->getSignes($idcons)){
	        $donneeSignes = array(
	            'duree' => $tabDonnees['duree_des_signes'],
	            'gravite' => $tabDonnees['gravite_des_signes']
	        );
	        
	        $sql = new Sql($this->tableGateway->getAdapter());
	        $sQuery = $sql->update()->table('infos_signes')->set($donneeSignes)
	        ->where(array('idcons' => $idcons));
	        $sql->prepareStatementForSqlObject($sQuery)->execute();
	    }else {
	        $this->addSignes($tabDonnees, $idemploye);
	    }
	}
	
	
	public function addResumeHistoireMaladie($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	     
	    if($values['resume_histoire_maladie']){
	        $donnees = array(
	            'idcons' => $values['idcons'],
	            'resume' => $values['resume_histoire_maladie'],
	        );
	        
	        $sQuery = $sql->insert()->into('histoire_maladie')->values($donnees);
	        $sql->prepareStatementForSqlObject($sQuery)->execute();
	    }
	    
	}
	
	public function getResumeHistoireMaladie($idcons){
	    $adapter = $this->tableGateway->getAdapter();
	    $sql = new Sql($adapter);
	    $select = $sql->select()->from('histoire_maladie')->columns(array('*'));
	    $select->where(array('idcons'=>$idcons));
	    return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	public function updateResumeHistoireMaladie($tabDonnees, $idemploye){
	    $idcons = $tabDonnees['idcons'];
	     
	    if($this->getResumeHistoireMaladie($idcons)){
	        $donnees = array(
	            'resume' => $tabDonnees['resume_histoire_maladie'],
	        );
	         
	        $sql = new Sql($this->tableGateway->getAdapter());
	        $sQuery = $sql->update()->table('histoire_maladie')->set($donnees)
	        ->where(array('idcons' => $idcons));
	        $sql->prepareStatementForSqlObject($sQuery)->execute();
	    }else {
	        $this->addResumeHistoireMaladie($tabDonnees, $idemploye);
	    }
	}
	
	
	
	public function getInfosDiabetique($idpatient){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('atc' => 'infos_diabetique' ));
	    $select->where(array('idpatient' => $idpatient));
	    return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
	}
	
	public function updateInfosDiabetique($idpatient, $donnees){
	    $sql = new Sql($this->tableGateway->getAdapter());
	    $sQuery = $sql->update()->table('infos_diabetique')->set($donnees)
	    ->where(array('idpatient' => $idpatient));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function addInfosDiabetique($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	
	    $exist = $this->getInfosDiabetique($values['idpatient']);
	    
        $donnees = array(
            'idpatient' => $values['idpatient'],
            'diabetique_connu' => $values['diabetique_connu'],
            'type_diabetique' => $values['type_diabetique'],
            'annee_decouverte' => $values['annee_decouverte'],
            'antidiabetique_oraux' => $values['antidiabetique_oraux'],
            'insulinotherapie' => $values['insulinotherapie'],
            'regime_alimentaire' => $values['regime_alimentaire'],
            'antecedent_diabete_dans_famille' => $values['antecedent_diabete_dans_famille'],
            'idemploye' => $idemploye
        );
	    
	    if($exist){
	        if($donnees['diabetique_connu'] || $donnees['antecedent_diabete_dans_famille']){
	            $this->updateInfosDiabetique($values['idpatient'], $donnees);
	        }
	    }else{
	        
	        if($donnees['diabetique_connu'] || $donnees['antecedent_diabete_dans_famille']){
	            $sQuery = $sql->insert()->into('infos_diabetique')->values($donnees);
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	        
	    }
	    
	}
	
	
	public function getAutreTerrainConnu($idpatient){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('atc' => 'autre_terrain_connu' ));
	    $select->where(array('idpatient' => $idpatient));
	    return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
	}
	
	public function updateAutreTerrainConnu($idpatient, $donnees){
	    $sql = new Sql($this->tableGateway->getAdapter());
	    $sQuery = $sql->update()->table('autre_terrain_connu')->set($donnees)
	    ->where(array('idpatient' => $idpatient));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function addAutreTerrainConnu($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	
	    $exist = $this->getAutreTerrainConnu($values['idpatient']);
	    
        $donnees = array(
            'idpatient' => $values['idpatient'],
            'autre_terrain_connu' => $values['autre_terrain_connu'],
            'autres_terrain_connu_hta' => $values['autres_terrain_connu_hta'],
            'hta_traitement' => $values['hta_traitement'],
            'dyslipidemie' => $values['dyslipidemie'],
            'dyslipidemie_traitement' => $values['dyslipidemie_traitement'],
            'obesite' => $values['obesite'],
            'tabagisme' => $values['tabagisme'],
            'tabagisme_nb_paquet' => $values['tabagisme_nb_paquet'],
            'alcool' => $values['alcool'],
            'idemploye' => $idemploye
        );
	        
	    if($exist){
	        if($donnees['autre_terrain_connu']){
	            $this->updateAutreTerrainConnu($values['idpatient'], $donnees);
	        }
	    }else{
	        if($donnees['autre_terrain_connu']){
	            $sQuery = $sql->insert()->into('autre_terrain_connu')->values($donnees);
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }
	
	}
	
	public function getAntMedicaux($idpatient){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('am' => 'ant_medicaux' ));
	    $select->where(array('idpatient' => $idpatient));
	    return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
	}
	
	public function updateAntMedicaux($idpatient, $donnees){
	    $sql = new Sql($this->tableGateway->getAdapter());
	    $sQuery = $sql->update()->table('ant_medicaux')->set($donnees)
	    ->where(array('idpatient' => $idpatient));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function addAntMedicaux($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	    
	    $exist = $this->getAntMedicaux($values['idpatient']);
	    
	    $donnees = array(
	        'antecedents_medicaux_notable' => $values['antecedents_medicaux_notable'],
	        'anciennes_hospitalisations' => $values['anciennes_hospitalisations'],
	        'traitements_en_cours' => $values['traitements_en_cours'],
	    );
	    
	    if($exist){
	        if(!$this->array_empty($donnees)){
	            $donnees['idemploye'] = $idemploye;
	            $this->updateAntMedicaux($values['idpatient'], $donnees);
	        }
	    }else{
	        if(!$this->array_empty($donnees)){
	            $donnees['idpatient'] = $values['idpatient'];
	            $donnees['idemploye'] = $idemploye;
	            
	            $sQuery = $sql->insert()->into('ant_medicaux')->values($donnees);
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }
	    
	
	}
	
	
	
	public function getAntChirurgicaux($idpatient){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('ac' => 'ant_chirurgicaux' ));
	    $select->where(array('idpatient' => $idpatient));
	    return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
	}
	
	public function updateAntChirurgicaux($idpatient, $donnees){
	    $sql = new Sql($this->tableGateway->getAdapter());
	    $sQuery = $sql->update()->table('ant_chirurgicaux')->set($donnees)
	    ->where(array('idpatient' => $idpatient));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function addAntChirurgicaux($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	     
	    $exist = $this->getAntChirurgicaux($values['idpatient']);
	    
	    $donnees = array(
	        'antecedents_chirurgicaux' => $values['antecedents_chirurgicaux'],
	        'chirurgie_rapport_avec_diabete' => $values['chirurgie_rapport_avec_diabete'],
	        'liste_chirurgie_des_membres' => $values['liste_chirurgie_des_membres'],
	        'chirurgie_sans_rapport_avec_diabete' => $values['chirurgie_sans_rapport_avec_diabete'],
	    );
	   
	    if($exist){
	        if(!$this->array_empty($donnees)){
	            $donnees['idemploye'] = $idemploye;
	            $this->updateAntChirurgicaux($values['idpatient'], $donnees);
	        }
	    }else{
	        if(!$this->array_empty($donnees)){
	            $donnees['idpatient'] = $values['idpatient'];
	            $donnees['idemploye'] = $idemploye;
	            
	            $sQuery = $sql->insert()->into('ant_chirurgicaux')->values($donnees);
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }
	}
	
	public function getEtatGeneral($idcons){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('eg' => 'etat_general' ));
	    $select->where(array('idcons' => $idcons));
	    return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
	}
	
	public function addEtatGeneral($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	    
	    $donnees = array(
	        'etat_general' => $values['etat_general']?$values['etat_general']:null,
	        'muqueuses' => $values['muqueuses']?$values['muqueuses']:null,
	        'deshydratation' => $values['deshydratation']?$values['deshydratation']:null,
	        'oedeme_membres_inferieurs' => $values['oedeme_membres_inferieurs']?$values['oedeme_membres_inferieurs']:null,
	        'tension_arterielle_max' => $values['tension_arterielle_max']?$values['tension_arterielle_max']:null,
	        'tension_arterielle_min' => $values['tension_arterielle_min']?$values['tension_arterielle_min']:null,
	        'coucheTA' => $values['coucheTA']?$values['coucheTA']:null,
	        'deboutTA' => $values['deboutTA']?$values['deboutTA']:null,
	        'ipsTA' => $values['ipsTA']?$values['ipsTA']:null,
	        'piedgaucheTA' => $values['piedgaucheTA']?$values['piedgaucheTA']:null,
	        'pieddroitTA' => $values['pieddroitTA']?$values['pieddroitTA']:null,
	        'poulsBatMin' => $values['poulsBatMin']?$values['poulsBatMin']:null,
	        'frequence_respiratoire_cycles_min' => $values['frequence_respiratoire_cycles_min']?$values['frequence_respiratoire_cycles_min']:null,
	        'diurese_horaire' => $values['diurese_horaire']?$values['diurese_horaire']:null,
	        'poids' => $values['poids']?$values['poids']:null,
	        'taille' => $values['taille']?$values['taille']:null,
	        'tour_taille' => $values['tour_taille']?$values['tour_taille']:null,
	        'imc_constante' => $values['imc_constante']?$values['imc_constante']:null,
	        'glycemie_capillaire' => $values['glycemie_capillaire']?$values['glycemie_capillaire']:null,
	        'sucre' => $values['sucre']?$values['sucre']:null,
	        'corps_cetonique' => $values['corps_cetonique']?$values['corps_cetonique']:null,
	        'proteines' => $values['proteines']?$values['proteines']:null,
	        'hematies' => $values['hematies']?$values['hematies']:null,
	    );
	    
	    if(!$this->array_empty($donnees)){
	        $donnees['idcons'] = $values['idcons'];
	        $donnees['idemploye'] = $idemploye;
	    
	        $sQuery = $sql->insert()->into('etat_general')->values($donnees);
	        $sql->prepareStatementForSqlObject($sQuery)->execute();
	    }
	    
	}
	
	
	public function updateEtatGeneral($tabDonnees, $idemploye){
	    $idcons = $tabDonnees['idcons'];
	
	    if($this->getEtatGeneral($idcons)){
	        $donnees = array(
	            
    	        'etat_general' => $tabDonnees['etat_general']?$tabDonnees['etat_general']:null,
    	        'muqueuses' => $tabDonnees['muqueuses']?$tabDonnees['muqueuses']:null,
    	        'deshydratation' => $tabDonnees['deshydratation']?$tabDonnees['deshydratation']:null,
    	        'oedeme_membres_inferieurs' => $tabDonnees['oedeme_membres_inferieurs']?$tabDonnees['oedeme_membres_inferieurs']:null,
    	        'tension_arterielle_max' => $tabDonnees['tension_arterielle_max']?$tabDonnees['tension_arterielle_max']:null,
    	        'tension_arterielle_min' => $tabDonnees['tension_arterielle_min']?$tabDonnees['tension_arterielle_min']:null,
    	        'coucheTA' => $tabDonnees['coucheTA']?$tabDonnees['coucheTA']:null,
    	        'deboutTA' => $tabDonnees['deboutTA']?$tabDonnees['deboutTA']:null,
    	        'ipsTA' => $tabDonnees['ipsTA']?$tabDonnees['ipsTA']:null,
    	        'piedgaucheTA' => $tabDonnees['piedgaucheTA']?$tabDonnees['piedgaucheTA']:null,
    	        'pieddroitTA' => $tabDonnees['pieddroitTA']?$tabDonnees['pieddroitTA']:null,
    	        'poulsBatMin' => $tabDonnees['poulsBatMin']?$tabDonnees['poulsBatMin']:null,
    	        'frequence_respiratoire_cycles_min' => $tabDonnees['frequence_respiratoire_cycles_min']?$tabDonnees['frequence_respiratoire_cycles_min']:null,
    	        'diurese_horaire' => $tabDonnees['diurese_horaire']?$tabDonnees['diurese_horaire']:null,
    	        'poids' => $tabDonnees['poids']?$tabDonnees['poids']:null,
    	        'taille' => $tabDonnees['taille']?$tabDonnees['taille']:null,
    	        'tour_taille' => $tabDonnees['tour_taille']?$tabDonnees['tour_taille']:null,
    	        'imc_constante' => $tabDonnees['imc_constante']?$tabDonnees['imc_constante']:null,
    	        'glycemie_capillaire' => $tabDonnees['glycemie_capillaire']?$tabDonnees['glycemie_capillaire']:null,
    	        'sucre' => $tabDonnees['sucre']?$tabDonnees['sucre']:null,
    	        'corps_cetonique' => $tabDonnees['corps_cetonique']?$tabDonnees['corps_cetonique']:null,
    	        'proteines' => $tabDonnees['proteines']?$tabDonnees['proteines']:null,
    	        'hematies' => $tabDonnees['hematies']?$tabDonnees['hematies']:null,
	            
	        );
	
	        $sql = new Sql($this->tableGateway->getAdapter());
	        $sQuery = $sql->update()->table('etat_general')->set($donnees)
	        ->where(array('idcons' => $idcons));
	        $sql->prepareStatementForSqlObject($sQuery)->execute();
	    }else {
	        $this->addEtatGeneral($tabDonnees, $idemploye);
	    }
	}
	
	
	public function getEtatGeneralSuiv($idsuiv){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('eg' => 'etat_general_suiv' ));
	    $select->where(array('idsuiv' => $idsuiv));
	    return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
	}
	
	public function addEtatGeneralSuiv($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	     
	    $donnees = array(
	        'etat_general_suiv' => $values['etat_general_suiv']?$values['etat_general_suiv']:null,
	        'muqueuses_suiv' => $values['muqueuses_suiv']?$values['muqueuses_suiv']:null,
	        'deshydratation_suiv' => $values['deshydratation_suiv']?$values['deshydratation_suiv']:null,
	        'oedeme_membres_inferieurs_suiv' => $values['oedeme_membres_inferieurs_suiv']?$values['oedeme_membres_inferieurs_suiv']:null,
	        'tension_arterielle_max_suiv' => $values['tension_arterielle_max_suiv']?$values['tension_arterielle_max_suiv']:null,
	        'tension_arterielle_min_suiv' => $values['tension_arterielle_min_suiv']?$values['tension_arterielle_min_suiv']:null,
	        'coucheTA_suiv' => $values['coucheTA_suiv']?$values['coucheTA_suiv']:null,
	        'deboutTA_suiv' => $values['deboutTA_suiv']?$values['deboutTA_suiv']:null,
	        'ipsTA_suiv' => $values['ipsTA_suiv']?$values['ipsTA_suiv']:null,
	        'piedgaucheTA_suiv' => $values['piedgaucheTA_suiv']?$values['piedgaucheTA_suiv']:null,
	        'pieddroitTA_suiv' => $values['pieddroitTA_suiv']?$values['pieddroitTA_suiv']:null,
	        'poulsBatMin_suiv' => $values['poulsBatMin_suiv']?$values['poulsBatMin_suiv']:null,
	        'frequence_respiratoire_cycles_min_suiv' => $values['frequence_respiratoire_cycles_min_suiv']?$values['frequence_respiratoire_cycles_min_suiv']:null,
	        'diurese_horaire_suiv' => $values['diurese_horaire_suiv']?$values['diurese_horaire_suiv']:null,
	        'poids_suiv' => $values['poids_suiv']?$values['poids_suiv']:null,
	        'taille_suiv' => $values['taille_suiv']?$values['taille_suiv']:null,
	        'tour_taille_suiv' => $values['tour_taille_suiv']?$values['tour_taille_suiv']:null,
	        'imc_constante_suiv' => $values['imc_constante_suiv']?$values['imc_constante_suiv']:null,
	        'glycemie_capillaire_suiv' => $values['glycemie_capillaire_suiv']?$values['glycemie_capillaire_suiv']:null,
	        'sucre_suiv' => $values['sucre_suiv']?$values['sucre_suiv']:null,
	        'corps_cetonique_suiv' => $values['corps_cetonique_suiv']?$values['corps_cetonique_suiv']:null,
	        'proteines_suiv' => $values['proteines_suiv']?$values['proteines_suiv']:null,
	        'hematies_suiv' => $values['hematies_suiv']?$values['hematies_suiv']:null,
	    );
	     
	    if(!$this->array_empty($donnees)){
	        $donnees['idsuiv'] = $values['idsuiv'];
	        $donnees['idemploye'] = $idemploye;
	         
	        $sQuery = $sql->insert()->into('etat_general_suiv')->values($donnees);
	        $sql->prepareStatementForSqlObject($sQuery)->execute();
	    }
	     
	}
	
	
	public function updateEtatGeneralSuiv($values, $idemploye){
	    $idsuiv = $values['idsuiv'];
	
	    if($this->getEtatGeneralSuiv($idsuiv)){
	        $donnees = array(
	            'etat_general_suiv' => $values['etat_general_suiv']?$values['etat_general_suiv']:null,
	            'muqueuses_suiv' => $values['muqueuses_suiv']?$values['muqueuses_suiv']:null,
	            'deshydratation_suiv' => $values['deshydratation_suiv']?$values['deshydratation_suiv']:null,
	            'oedeme_membres_inferieurs_suiv' => $values['oedeme_membres_inferieurs_suiv']?$values['oedeme_membres_inferieurs_suiv']:null,
	            'tension_arterielle_max_suiv' => $values['tension_arterielle_max_suiv']?$values['tension_arterielle_max_suiv']:null,
	            'tension_arterielle_min_suiv' => $values['tension_arterielle_min_suiv']?$values['tension_arterielle_min_suiv']:null,
	            'coucheTA_suiv' => $values['coucheTA_suiv']?$values['coucheTA_suiv']:null,
	            'deboutTA_suiv' => $values['deboutTA_suiv']?$values['deboutTA_suiv']:null,
	            'ipsTA_suiv' => $values['ipsTA_suiv']?$values['ipsTA_suiv']:null,
	            'piedgaucheTA_suiv' => $values['piedgaucheTA_suiv']?$values['piedgaucheTA_suiv']:null,
	            'pieddroitTA_suiv' => $values['pieddroitTA_suiv']?$values['pieddroitTA_suiv']:null,
	            'poulsBatMin_suiv' => $values['poulsBatMin_suiv']?$values['poulsBatMin_suiv']:null,
	            'frequence_respiratoire_cycles_min_suiv' => $values['frequence_respiratoire_cycles_min_suiv']?$values['frequence_respiratoire_cycles_min_suiv']:null,
	            'diurese_horaire_suiv' => $values['diurese_horaire_suiv']?$values['diurese_horaire_suiv']:null,
	            'poids_suiv' => $values['poids_suiv']?$values['poids_suiv']:null,
	            'taille_suiv' => $values['taille_suiv']?$values['taille_suiv']:null,
	            'tour_taille_suiv' => $values['tour_taille_suiv']?$values['tour_taille_suiv']:null,
	            'imc_constante_suiv' => $values['imc_constante_suiv']?$values['imc_constante_suiv']:null,
	            'glycemie_capillaire_suiv' => $values['glycemie_capillaire_suiv']?$values['glycemie_capillaire_suiv']:null,
	            'sucre_suiv' => $values['sucre_suiv']?$values['sucre_suiv']:null,
	            'corps_cetonique_suiv' => $values['corps_cetonique_suiv']?$values['corps_cetonique_suiv']:null,
	            'proteines_suiv' => $values['proteines_suiv']?$values['proteines_suiv']:null,
	            'hematies_suiv' => $values['hematies_suiv']?$values['hematies_suiv']:null,
	        );
	
	        $sql = new Sql($this->tableGateway->getAdapter());
	        $sQuery = $sql->update()->table('etat_general_suiv')->set($donnees)
	        ->where(array('idsuiv' => $idsuiv));
	        $sql->prepareStatementForSqlObject($sQuery)->execute();
	    }else {
	        $this->addEtatGeneralSuiv($values, $idemploye);
	    }
	}
	
	
	public function deleteExamenPhysique($idcons){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->delete();
	    $select->from('examen_physique_cons');
	    $select->where(array('idcons' => $idcons));
	    $sql->prepareStatementForSqlObject ( $select )->execute();
	}
	
	public function getExamenPhysique($idcons){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('epc' => 'examen_physique_cons' ));
	    $select->where(array('idcons' => $idcons));
	    return $sql->prepareStatementForSqlObject ( $select )->execute();
	}
	
	public function addExamenPhysique($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	     
	    $this->deleteExamenPhysique($values['idcons']);
	    
	    $nbChamp = $values['nbChampsInputAppSysSelect'];
	    
	    for($i=1 ; $i<=$nbChamp ; $i++){
	        $donnees = array();
	        
	        $donnees['idexamenphysique'] = $values['valueChampInputAppSysSelect_'.$i];
	        $donnees['inspection'] = $values['inspection_'.$i];
	        $donnees['palpitation'] = $values['palpitation_'.$i];
	        $donnees['percussion'] = $values['percussion_'.$i];
	        $donnees['auscultation'] = $values['auscultation_'.$i];
	        
	        if(!$this->array_empty($donnees)){
	            $donnees['idcons'] = $values['idcons'];
	            $donnees['idemploye'] = $idemploye;
	        
	            $sQuery = $sql->insert()->into('examen_physique_cons')->values($donnees);
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }
	    
	}
	
	
	public function deleteExamenPhysiqueSuiv($idsuiv){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->delete();
	    $select->from('examen_physique_suiv');
	    $select->where(array('idsuiv' => $idsuiv));
	    $sql->prepareStatementForSqlObject ( $select )->execute();
	}
	
	public function getExamenPhysiqueSuiv($idsuiv){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('epc' => 'examen_physique_suiv' ));
	    $select->where(array('idsuiv' => $idsuiv));
	    return $sql->prepareStatementForSqlObject ( $select )->execute();
	}
	
	public function addExamenPhysiqueSuiv($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	
	    $this->deleteExamenPhysiqueSuiv($values['idsuiv']);
	     
	    $nbChamp = $values['nbChampsInputAppSysSelectSuiv'];
	     
	    for($i=1 ; $i<=$nbChamp ; $i++){
	        $donnees = array();
	        
	        $donnees['idexamenphysique'] = $values['valueChampInputAppSysSelectSuiv_'.$i];
	        $donnees['inspection'] = $values['inspectionSuiv_'.$i];
	        $donnees['palpitation'] = $values['palpitationSuiv_'.$i];
	        $donnees['percussion'] = $values['percussionSuiv_'.$i];
	        $donnees['auscultation'] = $values['auscultationSuiv_'.$i];
	         
	        if(!$this->array_empty($donnees)){
	            $donnees['idsuiv'] = $values['idsuiv'];
	            $donnees['idemploye'] = $idemploye;
	             
	            $sQuery = $sql->insert()->into('examen_physique_suiv')->values($donnees);
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }
	     
	}
	
	
	
	public function getGlycemieAJeun($idcons){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('gj' => 'glycemie_a_jeun' ));
	    $select->where(array('idcons' => $idcons));
	    return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
	}
	
	public function updateGlycemieAJeun($idcons, $donnees){
	    $sql = new Sql($this->tableGateway->getAdapter());
	    $sQuery = $sql->update()->table('glycemie_a_jeun')->set($donnees)
	    ->where(array('idcons' => $idcons));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function addGlycemieAJeun($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	    
	    $exist = $this->getGlycemieAJeun($values['idcons']);
	     
	    $donnees = array(
	        'glycemie_jeun' => $values['glycemie_jeun'],
	    );
	    
	    if($exist){
	        if(!$this->array_empty($donnees)){
	            $donnees['idemploye'] = $idemploye;
	            $this->updateGlycemieAJeun($values['idcons'], $donnees);
	        }else{
	            $sQuery = $sql->delete()->from('glycemie_a_jeun')->where(array('idcons' => $values['idcons']));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }else{
	        if(!$this->array_empty($donnees)){
	            $donnees['idcons'] = $values['idcons'];
	            $donnees['idemploye'] = $idemploye;
	             
	            $sQuery = $sql->insert()->into('glycemie_a_jeun')->values($donnees);
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }
	    
	}
	

	public function getHemoglobineGlyquee($idcons){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('hg' => 'hemoglobine_glyquee' ));
	    $select->where(array('idcons' => $idcons));
	    return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
	}
	
	public function updateHemoglobineGlyquee($idcons, $donnees){
	    $sql = new Sql($this->tableGateway->getAdapter());
	    $sQuery = $sql->update()->table('hemoglobine_glyquee')->set($donnees)
	    ->where(array('idcons' => $idcons));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function addHemoglobineGlyquee($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	     
	    $exist = $this->getHemoglobineGlyquee($values['idcons']);
	
	    $donnees = array(
	        'hemoglobine_glyquee' => $values['hemoglobine_glyquee'],
	    );
	     
	    if($exist){
	        if(!$this->array_empty($donnees)){
	            $donnees['idemploye'] = $idemploye;
	            $this->updateHemoglobineGlyquee($values['idcons'], $donnees);
	        }else{
	            $sQuery = $sql->delete()->from('hemoglobine_glyquee')->where(array('idcons' => $values['idcons']));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }else{
	        if(!$this->array_empty($donnees)){
	            $donnees['idcons'] = $values['idcons'];
	            $donnees['idemploye'] = $idemploye;
	
	            $sQuery = $sql->insert()->into('hemoglobine_glyquee')->values($donnees);
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }
	     
	}
	
	/**
	 * UTILISATION MULTIPLE --- UTILISATION MULTIPLE --- UTILISATION MULTIPLE
	 * UTILISATION MULTIPLE --- UTILISATION MULTIPLE --- UTILISATION MULTIPLE
	 */
	
	public function getInfosAnalyse($table, $idcons){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('tab' => $table ));
	    $select->where(array('idcons' => $idcons));
	    return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
	}
	
	public function updateInfosAnalyse($table, $idcons, $donnees){
	    $sql = new Sql($this->tableGateway->getAdapter());
	    $sQuery = $sql->update()->table($table)->set($donnees)
	    ->where(array('idcons' => $idcons));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function addInfosAnalyse($table, $donnees, $idcons, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	
	    $exist = $this->getInfosAnalyse($table, $idcons);
	    if($exist){
	        if(!$this->array_empty($donnees)){
	            $donnees['idemploye'] = $idemploye;
	            $this->updateInfosAnalyse($table, $idcons, $donnees);
	        }else{
	            $sQuery = $sql->delete()->from($table)->where(array('idcons' => $idcons));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }else{
	        if(!$this->array_empty($donnees)){
	            $donnees['idcons'] = $idcons;
	            $donnees['idemploye'] = $idemploye;
	
	            $sQuery = $sql->insert()->into($table)->values($donnees);
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }
	
	}
	
	/**
	 * -------------------------------------------------------------
	 * FIN FIN UTILISATION MULTIPLE --- FIN FIN UTILISATION MULTIPLE
	 * _____________________________________________________________
	 */
	
	
	/**
	 * UTILISATION MULTIPLE --- UTILISATION MULTIPLE --- UTILISATION MULTIPLE
	 * UTILISATION MULTIPLE --- UTILISATION MULTIPLE --- UTILISATION MULTIPLE
	 */
	
	public function getInfosAnalyseSuiv($table, $idsuiv){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('tab' => $table ));
	    $select->where(array('idsuiv' => $idsuiv));
	    return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
	}
	
	public function updateInfosAnalyseSuiv($table, $idsuiv, $donnees){
	    $sql = new Sql($this->tableGateway->getAdapter());
	    $sQuery = $sql->update()->table($table)->set($donnees)
	    ->where(array('idsuiv' => $idsuiv));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	
	    $exist = $this->getInfosAnalyseSuiv($table, $idsuiv);
	    
	    if($exist){
	        if(!$this->array_empty($donnees)){
	            $donnees['idemploye'] = $idemploye;
	            $this->updateInfosAnalyseSuiv($table, $idsuiv, $donnees);
	        }else{
	            $sQuery = $sql->delete()->from($table)->where(array('idsuiv' => $idsuiv));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }else{
	        if(!$this->array_empty($donnees)){
	            $donnees['idsuiv'] = $idsuiv;
	            $donnees['idemploye'] = $idemploye;
	
	            $sQuery = $sql->insert()->into($table)->values($donnees);
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }
	
	}
	
	/**
	 * -------------------------------------------------------------
	 * FIN FIN UTILISATION MULTIPLE --- FIN FIN UTILISATION MULTIPLE
	 * _____________________________________________________________
	 */
	
	public function addGlycemieAJeunSuiv($values, $idemploye){
	    $table = "glycemie_a_jeun";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'glycemie_jeun' => $values['glycemie_jeun_suiv'],
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	public function addHemoglobineGlyqueeSuiv($values, $idemploye){
	    $table = "hemoglobine_glyquee";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'hemoglobine_glyquee' => $values['hemoglobine_glyquee_suiv'],
	    );
	    
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	public function addCreatininemie($values, $idemploye){
	    $table = "creatininemie";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'creatininemie' => $values['creatininemie'],
	    );

	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addCreatininemieSuiv($values, $idemploye){
	    $table = "creatininemie";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'creatininemie' => $values['creatininemie_suiv'],
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	
	public function addGroupageRhesus($values, $idemploye){
	    $table = "groupage_rhesus";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'grsh' => $values['grsh'],
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addGroupageRhesusSuiv($values, $idemploye){
	    $table = "groupage_rhesus";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'grsh' => $values['grsh_suiv'],
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	public function addTauxProthrombine($values, $idemploye){
	    $table = "taux_prothrombine";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'tp' => $values['tp'],
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addTauxProthrombineSuiv($values, $idemploye){
	    $table = "taux_prothrombine";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'tp' => $values['tp_suiv'],
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	public function addTempsCephalineActive($values, $idemploye){
	    $table = "temps_cephaline_active";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'tca' => $values['tca'],
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addTempsCephalineActiveSuiv($values, $idemploye){
	    $table = "temps_cephaline_active";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'tca' => $values['tca_suiv'],
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	public function addCholesterolHdl($values, $idemploye){
	    $table = "cholesterol_hdl";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'hdl_c' => $values['hdl_c'],
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addCholesterolHdlSuiv($values, $idemploye){
	    $table = "cholesterol_hdl";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'hdl_c' => $values['hdl_c_suiv'],
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	public function addCholesterolLdl($values, $idemploye){
	    $table = "cholesterol_ldl";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'ldl_c' => $values['ldl_c'],
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addCholesterolLdlSuiv($values, $idemploye){
	    $table = "cholesterol_ldl";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'ldl_c' => $values['ldl_c_suiv'],
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	
	public function addUricemie($values, $idemploye){
	    $table = "uricemie";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'uricemie' => $values['uricemie'],
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addUricemieSuiv($values, $idemploye){
	    $table = "uricemie";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'uricemie' => $values['uricemie_suiv'],
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	
	public function addTriglycerides($values, $idemploye){
	    $table = "triglycerides";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'triglycerides' => $values['triglycerides'],
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addTriglyceridesSuiv($values, $idemploye){
	    $table = "triglycerides";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'triglycerides' => $values['triglycerides_suiv'],
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	
	public function addMicroalbuminurie($values, $idemploye){
	    $table = "microalbuminurie";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'microalbuminurie_pu24h' => $values['microalbuminurie_pu24h'],
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addMicroalbuminurieSuiv($values, $idemploye){
	    $table = "microalbuminurie";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'microalbuminurie_pu24h' => $values['microalbuminurie_pu24h_suiv'],
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function addECG($values, $idemploye){
	    $table = "ecg";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'ecg' => $values['ecg'],
	        'indice_pression_systolique' => $values['indice_pression_systolique']
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addRadio($values, $idemploye){
	    $table = "radio";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'rsd1' => $values['rsd1'],
	        'rsd2' => $values['rsd2']
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addRadioSuiv($values, $idemploye){
	    $table = "radio";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'rsd1' => $values['rsd1_suiv'],
	        'rsd2' => $values['rsd2_suiv']
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	public function addScanner($values, $idemploye){
	    $table = "scanner";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'scanner' => $values['scanner'],
	        'echodoppler' => $values['echodoppler']
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addScannerSuiv($values, $idemploye){
	    $table = "scanner";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'scanner' => $values['scanner_suiv'],
	        'echodoppler' => $values['echodoppler_suiv']
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	public function addEchographie($values, $idemploye){
	    $table = "echographie";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'echographie1' => $values['echographie1'],
	        'echographie2' => $values['echographie2']
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addEchographieSuiv($values, $idemploye){
	    $table = "echographie";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'echographie1' => $values['echographie1_suiv'],
	        'echographie2' => $values['echographie2_suiv'],
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	
	
	
	public function addTypeDiabete($values, $idemploye){
	    $table = "type_diabete";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'type_diabete' => $values['type_diabete'],
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addHospitalisation($values, $idemploye){
	    $table = "hospitalisation";
	    $idcons = $values['idcons'];
	    $donnees = $values['hospitalisation'] == 1 ? array('hospitalisation'=>$values['hospitalisation'], 'evolution'=>$values['evolution'], 'exeat'=>$values['exeat'], 'deces'=>$values['deces']) : array('hospitalisation' => $values['hospitalisation'],'evolution'=>null, 'exeat'=>null, 'deces'=>null);
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	public function addTraitement($values, $idemploye){
	    $table = "traitement";
	    $idcons = $values['idcons'];
	    $donnees = array(
	        'traitement_medical' => $values['traitement_medical'],
	        'traitement_chirurgical' => $values['traitement_chirurgical'],
	    );
	
	    $this->addInfosAnalyse($table, $donnees, $idcons, $idemploye);
	}
	
	
	public function getConduiteASuivre($idsuiv){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('hg' => 'conduite_a_suivre' ));
	    $select->where(array('idsuiv' => $idsuiv));
	    return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
	}
	
	
	public function addConduiteASuivre($values, $idemploye){
	    $table = "conduite_a_suivre";
	    $idsuiv = $values['idsuiv'];
	    $donnees = array(
	        'conduite_a_suivre' => $values['conduite_a_suivre_suivi'],
	    );
	
	    $this->addInfosAnalyseSuiv($table, $donnees, $idsuiv, $idemploye);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function deleteComplicationDiagEntree($idcons){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->delete();
	    $select->from('complication_cons');
	    $select->where(array('idcons' => $idcons));
	    $sql->prepareStatementForSqlObject ( $select )->execute();
	}
	
	
	public function addComplicationDiagEntree($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	
	    $this->deleteComplicationDiagEntree($values['idcons']);
	     
	    $nbChamp = $values['nbComplicationDiagEntree'];
	     
	    for($i=1 ; $i<=$nbChamp ; $i++){
	        $donnees = array();
	        $donnees['idcomplication'] = $values['valueChampInputComplication_'.$i];
	        
	        if(!$this->array_empty($donnees)){
	            $donnees['note'] = $values['noteInputComplication_'.$i];
	            $donnees['idcons'] = $values['idcons'];
	            $donnees['idemploye'] = $idemploye;
	             
	            $sQuery = $sql->insert()->into('complication_cons')->values($donnees);
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }
	}
	
	public function getComplicationDiagEntree($idcons){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('cc' => 'complication_cons' ));
	    $select->where(array('idcons' => $idcons));
	    return $sql->prepareStatementForSqlObject ( $select )->execute();
	}
	
	
	public function getPlaintesSuivi($idsuiv){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->select ();
	    $select->from( array('ps' => 'plainte_suivi' ));
	    $select->where(array('idsuiv' => $idsuiv));
	    return $sql->prepareStatementForSqlObject ( $select )->execute();
	}
	
	public function deletePlaintesSuivi($idsuiv){
	    $sql = new Sql ( $this->tableGateway->getAdapter () );
	    $select = $sql->delete();
	    $select->from('plainte_suivi');
	    $select->where(array('idsuiv' => $idsuiv));
	    $sql->prepareStatementForSqlObject ( $select )->execute();
	}
	
	public function addPlaintesSuivi($values, $idemploye){
	    $sql = new Sql($this->tableGateway->getAdapter());
	
	    $this->deletePlaintesSuivi($values['idsuiv']);
	
	    $nbChamp = $values['nbPlaintesEntree'];
	
	    for($i=1 ; $i<=$nbChamp ; $i++){
	        $donnees = array();
	        $donnees['idplainte'] = $values['valueChampInputPlainte_'.$i];
	
	        if(!$this->array_empty($donnees)){
	            $donnees['note'] = $values['noteInputPlainte_'.$i];
	            $donnees['idsuiv'] = $values['idsuiv'];
	            $donnees['idemploye'] = $idemploye;
	            
	            $sQuery = $sql->insert()->into('plainte_suivi')->values($donnees);
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	    }
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function addBandelette($bandelettes){
		$values = array();
		if($bandelettes['albumine'] == 1){
			$values[] = array('idtypebandelette'=>1, 'idcons'=>$bandelettes['idcons'], 'croix_bandelette'=>(int)$bandelettes['croixalbumine']);
		}
		if($bandelettes['sucre'] == 1){
			$values[] = array('idtypebandelette'=>2, 'idcons'=>$bandelettes['idcons'], 'croix_bandelette'=>(int)$bandelettes['croixsucre']);
		}
		if($bandelettes['corpscetonique'] == 1){
			$values[] = array('idtypebandelette'=>3, 'idcons'=>$bandelettes['idcons'], 'croix_bandelette'=>(int)$bandelettes['croixcorpscetonique']);
		}
		
		for($i = 0 ; $i < count($values) ; $i++ ){
			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->insert()
			->into('bandelette')
			->columns(array('idtypebandelette', 'idcons', 'croix_bandelette'))
			->values($values[$i]);
			$stat = $sql->prepareStatementForSqlObject($sQuery);
			$stat->execute();
		}
	}
	
	public function getBandelette($id_cons){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from('bandelette')
		->columns(array('*'))
		->where(array('id_cons' => $id_cons));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$result = $stat->execute();
		
		$donnees = array();
		$donnees['temoin'] = 0;
		foreach ($result as $resultat){
			if($resultat['idtypebandelette'] == 1){
				$donnees['albumine'] = 1; //C'est � coch�
				$donnees['croixalbumine'] = $resultat['croix_bandelette'];
			}
			if($resultat['idtypebandelette'] == 2){
				$donnees['sucre'] = 1; //C'est � coch�
				$donnees['croixsucre'] = $resultat['croix_bandelette'];
			}
			if($resultat['idtypebandelette'] == 3){
				$donnees['corpscetonique'] = 1; //C'est � coch�
				$donnees['croixcorpscetonique'] = $resultat['croix_bandelette'];
			}
			
			//temoin
			$donnees['temoin'] = 1;
		}
		
		return $donnees;
	}
	
	public function deleteBandelette($id_cons){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->delete()
		->from('bandelette')
		->where(array('id_cons' => $id_cons));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$result = $stat->execute();
	}
	
	
	
	/**
	 * Recuperation de la liste des medicaments
	 */
	public function listeDeTousLesMedicaments(){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql ( $adapter );
		$select = $sql->select('consommable');
		$select->columns(array('ID_MATERIEL','INTITULE'));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
	
		return $result;
	}
	
	/**
	 * RECUPERER LA FORME DES MEDICAMENTS
	 */
	
	public function formesMedicaments(){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from( array( 'forme' => 'forme_medicament' ));
	
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
	
		return $result;
	}
	
	/**
	 * RECUPERER LES TYPES DE QUANTITE DES MEDICAMENTS
	 */
	
	public function typeQuantiteMedicaments(){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from( array( 'typeQuantite' => 'quantite_medicament' ));
	
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
	
		return $result;
	}
	
	public function getInfoPatient($id_personne) {
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))
		->columns( array( '*' ))
		->join(array('pers' => 'personne'), 'pers.ID_PERSONNE = pat.idpersonne' , array('*'))
		->where(array('pat.idpersonne' => $id_personne));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$resultat = $stat->execute()->current();
	
		return $resultat;
	}
	
	public function getPhoto($id) {
		$donneesPatient =  $this->getInfoPatient( $id );
	
		$nom = null;
		if($donneesPatient){$nom = $donneesPatient['PHOTO'];}
		if ($nom) {
			return $nom . '.jpg';
		} else {
			return 'identite.jpg';
		}
	}
	
	
 	
 	
 	public function fetchConsommable(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql ( $adapter );
 		$select = $sql->select('consommable');
 		$select->columns(array('ID_MATERIEL','INTITULE'));
 		$stat = $sql->prepareStatementForSqlObject($select);
 		$result = $stat->execute();
 		foreach ($result as $data) {
 			$options[$data['ID_MATERIEL']] = $data['INTITULE'];
 		}
 		return $options;
 	}
 	
 	
 	//GESTION DES FICHIER MP3
 	//GESTION DES FICHIER MP3
 	//GESTION DES FICHIER MP3
 	public function insererMp3($titre , $nom, $id_cons, $type){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->insert()
 		->into('fichier_mp3')
 		->columns(array('titre', 'nom', 'id_cons', 'type'))
 		->values(array('titre' => $titre , 'nom' => $nom, 'id_cons'=>$id_cons, 'type'=>$type));
 	
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		return $stat->execute();
 	}
 	
 	public function getMp3($id_cons, $type){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('f' => 'fichier_mp3'))->columns(array('*'))
 		->where(array('id_cons' => $id_cons, 'type' => $type))
 		->order('id DESC');
 	
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute();
 		return $result;
 	}
 	
 	public function supprimerMp3($idLigne, $id_cons, $type){
 		$liste = $this->getMp3($id_cons, $type);
 	
 		$i=1;
 		foreach ($liste as $list){
 			if($i == $idLigne){
 				unlink('C:\wamp\www\simens\public\audios\\'.$list['nom']);
 	
 				$db = $this->tableGateway->getAdapter();
 				$sql = new Sql($db);
 				$sQuery = $sql->delete()
 				->from('fichier_mp3')
 				->where(array('id' => $list['id']));
 	
 				$stat = $sql->prepareStatementForSqlObject($sQuery);
 				$stat->execute();
 	
 				return true;
 			}
 			$i++;
 		}
 		return false;
 	}
 	
 	
 	//GESTION DES FICHIERS VIDEOS
 	//GESTION DES FICHIERS VIDEOS
 	//GESTION DES FICHIERS VIDEOS
 	public function insererVideo($titre , $nom, $format, $id_cons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->insert()
 		->into('fichier_video')
 		->columns(array('titre', 'nom', 'format', 'id_cons'))
 		->values(array('titre' => $titre , 'nom' => $nom, 'format' => $format, 'id_cons'=>$id_cons));
 	
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		return $stat->execute();
 	}
 	
 	public function getVideos($id_cons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('f' => 'fichier_video'))->columns(array('*'))
 		->where(array('id_cons' => $id_cons))
 		->order('id DESC');
 	
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute();
 		return $result;
 	}
 	
 	public function getVideoWithId($id){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('f' => 'fichier_video'))->columns(array('*'))
 		->where(array('id' => $id));
 	
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute()->current();
 		return $result;
 	}
 	
 	public function supprimerVideo($id){

 		$laVideo = $this->getVideoWithId($id);
 		$result = unlink('C:\wamp\www\simens\public\videos\\'.$laVideo['nom']);
 		
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->delete()->from('fichier_video')->where(array('id' => $id));

 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$stat->execute();
 		
 		return $result;
 	}
 	
 	//Verifier si un tableau est vide ou pas
 	function array_empty($array) {
 		$is_empty = true;
 		foreach($array as $k) {
 			$is_empty = $is_empty && empty($k);
 		}
 		return $is_empty;
 	}
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	public function getListeQuatiers($idcommune)
 	{
 	    $sql = new Sql($this->tableGateway->getAdapter());
 	    $select = $sql->select('quartier_saint_louis')->order('id ASC')->where(array('idtypeelement' => $idcommune));
 	    $result = $sql->prepareStatementForSqlObject($select)->execute();
 	
 	    $options = array();
 	    foreach ($result as $data) {
 	        $options[] = array('id' => $data['id'], 'libelle' => $data['libelle']);
 	    }
 	    return $options;
 	}
 	
 	
 	/**
 	 * GESTION DES DONNEES PARAMETRABLES
 	 */
 	
 	public function getListeTypeElementsOrdreDecroissant($table)
 	{
 	    $sql = new Sql($this->tableGateway->getAdapter());
 	    $select = $sql->select($table)->order('id ASC');
 	    $result = $sql->prepareStatementForSqlObject($select)->execute();
 	
 	    $options = array();
 	    foreach ($result as $data) {
 	        $options[] = array('id' => $data['id'], 'libelle' => $data['libelle']);
 	    }
 	    return $options;
 	}
 	
 	
 	public function getInfosElements($table, $idTypeElement, $libelle)
 	{
 	    $sql = new Sql ( $this->tableGateway->getAdapter () );
 	    $select = $sql->select ();
 	    $select->from( array('tab' => $table ));
 	    $select->where(array('libelle' => $libelle, 'idtypeelement' => $idTypeElement));
 	    return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
 	}
 	
 	
 	public function addInfosElements($tabTypeElement, $tabElement, $table, $idemploye)
 	{
 	    $sql = new Sql($this->tableGateway->getAdapter());
 	
 	    for($i = 0 ; $i  < count($tabElement) ; $i++) {
 	        
 	        $exist = $this->getInfosElements($table, $tabTypeElement[$i], $tabElement[$i]);
 	        
 	        if( !$exist ){
 	            if(!$this->array_empty($tabTypeElement)){ $donnees['idtypeelement'] = $tabTypeElement[$i]; }
 	            
 	            $donnees['libelle'] = $tabElement[$i];
 	            $donnees['idemploye'] = $idemploye;
 	        
 	            $sQuery = $sql->insert()->into($table)->values($donnees);
 	            $sql->prepareStatementForSqlObject($sQuery)->execute();
 	        }
 	    }
 	
 	}
 	
 	
 	public function getListeElement($table)
 	{
 	    $sql = new Sql($this->tableGateway->getAdapter());
 	    $select = $sql->select($table)->order('libelle ASC');
 	    $result = $sql->prepareStatementForSqlObject($select)->execute();
 	
 	    $options = array();
 	    foreach ($result as $data) {
 	        $options[] = array('id' => $data['id'], 'libelle' => $data['libelle']);
 	    }
 	    return $options;
 	}
 	
 	public function getListeElementAvecType($table, $idTypeElement)
 	{
 	    $sql = new Sql($this->tableGateway->getAdapter());
 	    $select = $sql->select($table)->order('id ASC')->where(array('idtypeelement' => $idTypeElement));
 	    $result = $sql->prepareStatementForSqlObject($select)->execute();
 	
 	    $options = array();
 	    foreach ($result as $data) {
 	        $options[] = array('id' => $data['id'], 'libelle' => $data['libelle']);
 	    }
 	    return $options;
 	}
 	
 	
 	public function updateInfosElements($table, $libelleElement, $idElement, $idemploye){
 	    
 	    $donnees = array(
 	        'libelle' => $libelleElement,
 	        'idemploye' => $idemploye,
 	    );
 	    
 	    $sql = new Sql($this->tableGateway->getAdapter());
 	    $sQuery = $sql->update()->table($table)->set($donnees)->where(array('id' => $idElement));
 	    $sql->prepareStatementForSqlObject($sQuery)->execute();
 	}
 	
 	
 	public function getListeElementsUV($table)
 	{
 	    $sql = new Sql($this->tableGateway->getAdapter());
 	    $select = $sql->select($table)->order('libelle ASC');
 	    $result = $sql->prepareStatementForSqlObject($select)->execute();
 	
 	    $options = array();
 	    foreach ($result as $data) {
 	        $options[] = array('id' => $data['id'], 'libelle' => $data['libelle']);
 	    }
 	    return $options;
 	}
 	
 	
 	public function getInfosElementsUV($table, $libelle)
 	{
 	    $sql = new Sql ( $this->tableGateway->getAdapter () );
 	    $select = $sql->select ();
 	    $select->from( array('tab' => $table ));
 	    $select->where(array('libelle' => $libelle));
 	    return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
 	}
 	
 	
 	public function addInfosElementsUV($tabElement, $table, $idemploye)
 	{
 	    $sql = new Sql($this->tableGateway->getAdapter());
 	
 	    for($i = 0 ; $i  < count($tabElement) ; $i++) {
 	
 	        $exist = $this->getInfosElementsUV($table, $tabElement[$i]);
 	
 	        if( !$exist ){
 	            $donnees['libelle'] = $tabElement[$i];
 	            $donnees['idemploye'] = $idemploye;
 	
 	            $sQuery = $sql->insert()->into($table)->values($donnees);
 	            $sql->prepareStatementForSqlObject($sQuery)->execute();
 	        }
 	    }
 	
 	}
 	
 	
 	
 	
}