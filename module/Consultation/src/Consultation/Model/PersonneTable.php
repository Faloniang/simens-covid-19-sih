<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Consultation\View\Helper\DateHelper;

class PersonneTable {
    
    protected $tableGateway;
    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    
    public function getListeProfessions()
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('liste_profession')->order('libelle ASC');
        $result = $sql->prepareStatementForSqlObject($select)->execute();
        
        $options = array('' => '');
        foreach ($result as $data) {
            $options[$data['id']] = $data['libelle'];
        }
        return $options;
    }
    
    public function getListeNationalites()
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('pays')->order('nom_fr_fr ASC');
        $result = $sql->prepareStatementForSqlObject($select)->execute();
    
        $options = array('' => '');
        foreach ($result as $data) {
            $options[$data['id']] = $data['nom_fr_fr'];
        }
        return $options;
    }
    
    public function getListeStatutMatrimonial()
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('liste_statut_matrimonial')->order('libelle ASC');
        $result = $sql->prepareStatementForSqlObject($select)->execute();
    
        $options = array('' => '');
        foreach ($result as $data) {
            $options[$data['id']] = $data['libelle'];
        }
        return $options;
    }
    
    public function getListeRegimeMatrimonial()
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('liste_regime_matrimonial')->order('libelle ASC');
        $result = $sql->prepareStatementForSqlObject($select)->execute();
    
        $options = array('' => '');
        foreach ($result as $data) {
            $options[$data['id']] = $data['libelle'];
        }
        return $options;
    }
    
    public function getListeRegion()
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('liste_region')->order('libelle ASC');
        $result = $sql->prepareStatementForSqlObject($select)->execute();
    
        $options = array('' => '');
        foreach ($result as $data) {
            $options[$data['id']] = $data['libelle'];
        }
        return $options;
    }
    
    
    public function getListeRace()
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('liste_race')->order('id ASC');
        $result = $sql->prepareStatementForSqlObject($select)->execute();
    
        $options = array('' => '');
        foreach ($result as $data) {
            $options[$data['id']] = $data['libelle'];
        }
        return $options;
    }
    
    public function getListeSigne()
    {
    	$sql = new Sql($this->tableGateway->getAdapter());
    	$select = $sql->select('liste_signe')->order('libelle ASC');
    	$result = $sql->prepareStatementForSqlObject($select)->execute();
    
    	$options = array('' => '');
    	foreach ($result as $data) {
    		$options[$data['id']] = $data['libelle'];
    	}
    	return $options;
    }
    
    public function savePersonne($donnees, $personne)
    {
        $personne ['NOM'] = $donnees['NOM'];
        $personne ['PRENOM'] = rtrim($donnees['PRENOM']);
        $personne ['ADRESSE'] = ($donnees['ADRESSE'])?$donnees['ADRESSE']:null;
        
        $personne ['TELEPHONE'] = ($donnees['TELEPHONE'])?$donnees['TELEPHONE']:null;
        $personne ['TELEPHONE_2'] = ($donnees['TELEPHONE_2'])?$donnees['TELEPHONE_2']:null;
        $personne ['AGE'] = ($donnees['AGE'])?$donnees['AGE']:null;
        $personne ['DATE_NAISSANCE'] = ($donnees['DATE_NAISSANCE'])? ((new DateHelper())->convertDateInAnglais($donnees['DATE_NAISSANCE'])):null ;
        $personne ['PROFESSION'] = ($donnees['PROFESSION'])?$donnees['PROFESSION']:null;
        $personne ['SEXE'] = ($donnees['SEXE'])?$donnees['SEXE']:null;
        $personne ['STATUT_MATRIMONIAL'] = ($donnees['STATUT_MATRIMONIAL'])?$donnees['STATUT_MATRIMONIAL']:null;
        $personne ['REGIME_MATRIMONIAL'] = ($donnees['REGIME_MATRIMONIAL'])?$donnees['REGIME_MATRIMONIAL']:null;
       
        $idpersonne = null;
        $this->tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();
        try {

            $idpersonne = $this->tableGateway->getLastInsertValue( $this->tableGateway->insert($personne) );
            
            $this->tableGateway->getAdapter()->getDriver()->getConnection()->commit();
		} catch (\Exception $e) {
			$this->tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
		}
        
		return $idpersonne;
    }
    
    
    public function updatePersonne($idpersonne, $donnees, $personne)
    {
        $personne ['NOM'] = $donnees['NOM'];
        $personne ['PRENOM'] = rtrim($donnees['PRENOM']);
        $personne ['ADRESSE'] = ($donnees['ADRESSE'])?$donnees['ADRESSE']:null;
        $personne ['TELEPHONE'] = ($donnees['TELEPHONE'])?$donnees['TELEPHONE']:null;
        $personne ['TELEPHONE_2'] = ($donnees['TELEPHONE_2'])?$donnees['TELEPHONE_2']:null;
        $personne ['AGE'] = ($donnees['AGE'])?$donnees['AGE']:null;
        $personne ['DATE_NAISSANCE'] = ($donnees['DATE_NAISSANCE'])? ((new DateHelper())->convertDateInAnglais($donnees['DATE_NAISSANCE'])):null ;
        $personne ['PROFESSION'] = ($donnees['PROFESSION'])?$donnees['PROFESSION']:null;
        $personne ['SEXE'] = ($donnees['SEXE'])?$donnees['SEXE']:null;
        $personne ['STATUT_MATRIMONIAL'] = ($donnees['STATUT_MATRIMONIAL'])?$donnees['STATUT_MATRIMONIAL']:null;
        $personne ['REGIME_MATRIMONIAL'] = ($donnees['REGIME_MATRIMONIAL'])?$donnees['REGIME_MATRIMONIAL']:null;
        
        $this->tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();
        try {
    
            $this->tableGateway->update($personne, array('ID_PERSONNE' => $idpersonne));
    
            $this->tableGateway->getAdapter()->getDriver()->getConnection()->commit();
        } catch (\Exception $e) {
            $this->tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
        }
        
    }
    
    
    public function getPersonne($idpersonne) {
        $idpersonne = ( int ) $idpersonne;
        $rowset = $this->tableGateway->select ( array ( 'ID_PERSONNE' => $idpersonne ) );
        $row =  $rowset->current ();
        if (! $row) { return null; }
        return $row;
    }
    
}

