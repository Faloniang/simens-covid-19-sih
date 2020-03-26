<?php

namespace Personnel\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Patient implements InputFilterAwareInterface {
	public $idpersonne;
	public $type_patient;
	public $nationalite;
	public $region;
	public $departement;
	public $date_enregistrement;
	public $numero_dossier;
	protected $inputFilter;
	
	public function exchangeArray($data) {
		$this->idpersonne = (! empty ( $data ['idpersonne'] )) ? $data ['idpersonne'] : null;
		$this->type_patient = (! empty ( $data ['type_patient'] )) ? $data ['type_patient'] : null;
		$this->nationalite = (! empty ( $data ['nationalite'] )) ? $data ['nationalite'] : null;
		$this->region = (! empty ( $data ['region'] )) ? $data ['region'] : null;
		$this->departement = (! empty ( $data ['departement'] )) ? $data ['departement'] : null;
		$this->numero_dossier = (! empty ( $data ['numero_dossier'] )) ? $data ['numero_dossier'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
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