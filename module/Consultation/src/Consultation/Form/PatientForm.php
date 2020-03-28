<?php

namespace Consultation\Form;

use Zend\Form\Form;

class PatientForm extends Form {
	public function __construct($name = null) {
		parent::__construct ();

		$this->add ( array (
				'name' => 'ID_PERSONNE',
				'type' => 'Hidden',
				'attributes' => array (
				)
		) );
		$this->add ( array (
				'name' => 'CIVILITE',
				'type' => 'Select',
				'options' => array (
						'label' => 'Civilite',
						'value_options' => array (
								'Mme' => 'Mme',
								'Mlle' => 'Mlle',
								'M' => 'M'
						)
				),
				'attributes' => array (
						'id' => 'CIVILITE',
						'value' => 'M',
				        'tabindex' => 1,
				)
		) );

		$this->add ( array (
				'name' => 'SEXE',
				'type' => 'Select',
				'options' => array (
						'label' => 'Sexe',
						'value_options' => array (
								'' => '',
								'Masculin' => 'Masculin',
								iconv ( 'ISO-8859-1', 'UTF-8','Féminin') => iconv ( 'ISO-8859-1', 'UTF-8','Féminin')
						)
				),
				'attributes' => array (
						'id' => 'SEXE',
						'required' => true,
						'tabindex' => 2,
				)
		) );
		
		$this->add ( array (
		    'name' => 'PRENOM',
		    'type' => 'Text',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Prénom' )
		    ),
		    'attributes' => array (
		        'id' => 'PRENOM',
		        'required' => true,
		        'tabindex' => 3,
		    )
		) );
		
		
		$this->add ( array (
				'name' => 'NOM',
				'type' => 'Text',
				'options' => array (
						'label' => 'Nom'
				),
				'attributes' => array (
						'id' => 'NOM',
						'required' => true,
						'tabindex' => 4,
				)
		) );
		
		$this->add ( array (
				'name' => 'AGE',
				'type' => 'Number',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Âge' )
				),
				'attributes' => array (
						'id' => 'AGE',
						'tabindex' => 5,
						'required' => true,
						'min' => 0,
						'max' => 150,
				    
				)
		) );
		

		$this->add ( array (
				'name' => 'DATE_NAISSANCE',
				'type' => 'Text',
				'options' => array (
						'label' => 'Date de naissance'
				),
				'attributes' => array (
						'id' => 'DATE_NAISSANCE',
						'tabindex' => 6,
				)
		) );

		
		$this->add ( array (
				'name' => 'LIEU_NAISSANCE',
				'type' => 'Text',
				'options' => array (
						'label' => 'Lieu de naissance'
				),
				'attributes' => array (
						'id' => 'LIEU_NAISSANCE',
						'tabindex' => 7,
				)
		) );
		
		$this->add ( array (
		    'name' => 'ADRESSE',
		    'type' => 'Text',
		    'options' => array (
		        'label' => 'Adresse'
		    ),
		    'attributes' => array (
		        'id' => 'ADRESSE',
		        'tabindex' => 8,
		    )
		) );
		
		$this->add ( array (
				'name' => 'TELEPHONE',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Téléphone personnel' )
				),
				'attributes' => array (
						'id' => 'TELEPHONE',
				        'required' => true,
						'tabindex' => 9,
				)
		) );
		
		
		$this->add ( array (
		    'name' => 'TELEPHONE_2',
		    'type' => 'Text',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Téléphone secondaire' )
		    ),
		    'attributes' => array (
		        'id' => 'TELEPHONE_2',
		        'tabindex' => 10,
		    )
		) );
		
		$this->add ( array (
		    'name' => 'REGION',
		    'type' => 'Select',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Région' )
		    ),
		    'attributes' => array (
		        'id' => 'REGION',
		        'required' => true,
		        'tabindex' => 11,
		        'onchange' => "getListeDepartements(this.value);",
		    )
		) );
		
		$this->add ( array (
		    'name' => 'DEPARTEMENT',
		    'type' => 'Select',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Département' )
		    ),
		    'attributes' => array (
		        'id' => 'DEPARTEMENT',
		        'required' => true,
		        'tabindex' => 12,
		    )
		) );
		
		$this->add ( array (
				'name' => 'PROFESSION',
				'type' => 'Select',
				'options' => array (
					'label' => 'Profession',
				    'value_options' => array (
				        '' => ''
				    )
				),
				'attributes' => array (
						'id' => 'PROFESSION',
						'tabindex' => 13,
				)
		) );
		
		$this->add ( array (
		    'name' => 'NATIONALITE_ORIGINE',
		    'type' => 'Select',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8','Nationalité'),
		    ),
		    'attributes' => array (
		        'id' => 'NATIONALITE_ORIGINE',
		        'tabindex' => 14,
		    )
		) );
		
		$this->add ( array (
		    'name' => 'NATIONALITE_ACTUELLE',
		    'type' => 'Select',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8','Nationalité actuelle'),
		        'value_options' => array (
		            '' => ''
		        )
		    ),
		    'attributes' => array (
		        'id' => 'NATIONALITE_ACTUELLE',
		        'tabindex' => 15,
		    )
		
		) );
		
		$this->add ( array (
		    'name' => 'STATUT_MATRIMONIAL',
		    'type' => 'Select',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8','Statut matrimonial'),
		        'value_options' => array (
		            '' => ''
		        )
		    ),
		    'attributes' => array (
		        'id' => 'STATUT_MATRIMONIAL',
		        'tabindex' => 16,
		    )
		
		) );
		
		$this->add ( array (
		    'name' => 'REGIME_MATRIMONIAL',
		    'type' => 'Select',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8','régime matrimonial'),
		        'value_options' => array (
		            '' => ''
		        )
		    ),
		    'attributes' => array (
		        'id' => 'REGIME_MATRIMONIAL',
		        'tabindex' => 17,
		    )
		
		) );
		
		
		$this->add ( array (
				'type' => 'Email',
				'name' => 'EMAIL',
				'options' => array (
						'label' => 'Email'
				),
				'attributes' => array (
						'placeholder' => 'votre@domain.com',
						'id' => 'EMAIL',
				)
		) );
		
		$this->add ( array (
		    'name' => 'DEPARTEMENT_MEMO',
		    'type' => 'Hidden',
		    'attributes' => array (
		        'id' => 'DEPARTEMENT_MEMO',
		    )
		) );
		
		$this->add ( array (
		    'name' => 'TYPE_PATIENT',
		    'type' => 'Hidden',
		    'attributes' => array (
		        'id' => 'TYPE_PATIENT',
		    )
		) );
		
	}
}