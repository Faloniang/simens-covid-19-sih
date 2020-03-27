<?php
namespace Consultation\Model;


class Personne{
public $idpersonne;
	public $nom;
	public $prenom;
	public $date_naissance;
	public $lieu_naissance;
	public $adresse;
	public $sexe;
	public $age;
	public $situation_matrimoniale;
	public $nationalite_actuelle;
	public $nationalite_origine;
	public $telephone;
	public $telephone_2;
	public $email;
	public $profession;
	public $date_modification;
	public $photo;
	public $statut_matrimonial;
	public $regime_matrimonial;
	
	public $ethnie;
	public $depistage;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
		$this->idpersonne = (! empty ( $data ['ID_PERSONNE'] )) ? $data ['ID_PERSONNE'] : null;
 		$this->lieu_naissance = (! empty ( $data ['LIEU_NAISSANCE'] )) ? $data ['LIEU_NAISSANCE'] : null;
		$this->nom = (! empty ( $data ['NOM'] )) ? $data ['NOM'] : null;
		$this->prenom = (! empty ( $data ['PRENOM'] )) ? $data ['PRENOM'] : null;
 		$this->date_naissance = (! empty ( $data ['DATE_NAISSANCE'] )) ? $data ['DATE_NAISSANCE'] : null;
 		$this->adresse = (! empty ( $data ['ADRESSE'] )) ? $data ['ADRESSE'] : null;
 		$this->sexe = (! empty ( $data ['SEXE'] )) ? $data ['SEXE'] : null;
 		$this->age = (! empty ( $data ['AGE'] )) ? $data ['AGE'] : null;
 		$this->situation_matrimoniale = (! empty ( $data ['situation_matrimoniale'] )) ? $data ['situation_matrimoniale'] : null;
 		$this->telephone = (! empty ( $data ['TELEPHONE'] )) ? $data ['TELEPHONE'] : null;
 		$this->telephone_2 = (! empty ( $data ['TELEPHONE_2'] )) ? $data ['TELEPHONE_2'] : null;
 		$this->email = (! empty ( $data ['email'] )) ? $data ['email'] : null;
 		$this->profession = (! empty ( $data ['PROFESSION'] )) ? $data ['PROFESSION'] : null;
 		$this->statut_matrimonial = (! empty ( $data ['STATUT_MATRIMONIAL'] )) ? $data ['STATUT_MATRIMONIAL'] : null;
 		$this->regime_matrimonial = (! empty ( $data ['REGIME_MATRIMONIAL'] )) ? $data ['REGIME_MATRIMONIAL'] : null;
 		$this->photo = (! empty ( $data ['PHOTO'] )) ? $data ['PHOTO'] : null;
 		$this->date_modification = (! empty ( $data ['DATE_MODIFICATION'] )) ? $data ['DATE_MODIFICATION'] : null;
 		
 			
	}
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}