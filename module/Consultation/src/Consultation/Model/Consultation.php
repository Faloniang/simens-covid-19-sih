<?php
namespace Consultation\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Consultation implements InputFilterAwareInterface{
	public $idcons;
	public $idadmission;
	public $idmedecin;
	public $idinfirmier;
	public $idpatient;
	public $date;
	public $heure;
	public $poids;
	public $taille;
	public $temperature;
	public $perimetre_cranien;
	public $consprise;
	public $date_enreg_infirm;
	public $date_enreg_medecin;
	public $idemploye;
	
	
	protected $inputFilter;

	public function exchangeArray($data) {
		$this->idcons = (! empty ( $data ['idcons'] )) ? $data ['idcons'] : null;
		$this->idadmission = (! empty ( $data ['idadmission'] )) ? $data ['idadmission'] : null;
		$this->idmedecin = (! empty ( $data ['idmedecin'] )) ? $data ['idmedecin'] : null;
		$this->idinfirmier = (! empty ( $data ['idinfirmier'] )) ? $data ['idinfirmier'] : null;
		$this->idpatient = (! empty ( $data ['idpatient'] )) ? $data ['idpatient'] : null;
		$this->date = (! empty ( $data ['date'] )) ? $data ['date'] : null;
		$this->heure = (! empty ( $data ['heure'] )) ? $data ['heure'] : null;
		$this->poids = (! empty ( $data ['poids'] )) ? $data ['poids'] : null;
		$this->taille = (! empty ( $data ['taille'] )) ? $data ['taille'] : null;
		$this->temperature = (! empty ( $data ['temperature'] )) ? $data ['temperature'] : null;
		$this->perimetre_cranien = (! empty ( $data ['perimetre_cranien'] )) ? $data ['perimetre_cranien'] : null;
		$this->consprise = (! empty ( $data ['consprise'] )) ? $data ['consprise'] : null;
		$this->date_enreg_infirm = (! empty ( $data ['date_enreg_infirm'] )) ? $data ['date_enreg_infirm'] : null;
		$this->date_enreg_medecin = (! empty ( $data ['date_enreg_medecin'] )) ? $data ['date_enreg_medecin'] : null;
		$this->idemploye = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null;

	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
	public function getInputFilter() {
		if (! $this->inputFilter) {
			$inputFilter = new InputFilter ();
			$factory = new InputFactory ();

			$this->inputFilter = $inputFilter;
		}

		return $this->inputFilter;
	}
}