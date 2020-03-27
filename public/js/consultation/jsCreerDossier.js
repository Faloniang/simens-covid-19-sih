var base_url = window.location.toString();
var tabUrl = base_url.split("public");

$(function() {
	//GESTION DES ACCORDEONS
	//GESTION DES ACCORDEONS
	//GESTION DES ACCORDEONS
    
	$('#div_supprimer_photo').css({'visibility':'hidden'}); 
    /********************************************************************************************/
    /********************************************************************************************/ 
    //BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
    function confirmation(){
	  $( "#confirmation" ).dialog({
	    resizable: false,
	    height:170,
	    width:485,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Oui": function() {
	            $( this ).dialog( "close" );

	             $('#photo').children().remove(); 
	             $('<input type="file" />').appendTo($('#photo')); 
	             $("#div_supprimer_photo").children().remove();
	             Recupererimage();          	       
	             
	             $('#div_supprimer_photo').css({'visibility':'hidden'});
	    	     return false;
	    	     
	        },
	        "Annuler": function() {
                $( this ).dialog( "close" );
            }
	   }
	  });
    }
    
    //FONCTION QUI RECUPERE LA PHOTO ET LA PLACE SUR L'EMPLACEMENT SOUHAITE
    function Recupererimage(){
    	$('#photo input[type="file"]').change(function() {
    	  
    	   $('#div_supprimer_photo').css({'visibility':'visible'});
    		
    	   var file = $(this);
 		   var reader = new FileReader;
 		   
	       reader.onload = function(event) {
	    		var img = new Image();
                 
        		img.onload = function() {
				   var width  = 105;
				   var height = 105;
				
				   var canvas = $('<canvas></canvas>').attr({ width: width, height: height });
				   file.replaceWith(canvas);
				   var context = canvas[0].getContext('2d');
	        	    	context.drawImage(img, 0, 0, width, height);
			    };
			    document.getElementById('fichier_tmp').value = img.src = event.target.result;
			    $("#fichier_tmp").val(img.src);
			
    	};
    	 $("#modifier_photo").remove(); //POUR LA MODIFICATION
    	reader.readAsDataURL(file[0].files[0]);
    	//Cr�ation de l'onglet de suppression de la photo
    	$("#div_supprimer_photo").children().remove();
    	$('<input alt="supprimer_photo" title="Supprimer la photo" name="supprimer_photo" id="supprimer_photo">').appendTo($("#div_supprimer_photo"));
      
    	//SUPPRESSION DU PHOTO
        //SUPPRESSION DU PHOTO
          $("#supprimer_photo").click(function(e){
        	 e.preventDefault();
        	 confirmation();
             $("#confirmation").dialog('open');
          });
      });
    	     
    }
    //AJOUTER LA PHOTO DU PATIENT
    //AJOUTER LA PHOTO DU PATIENT
    Recupererimage();
    
    //AJOUT LA PHOTO DU PATIENT EN CLIQUANT SUR L'ICONE AJOUTER
    //AJOUT LA PHOTO DU PATIENT EN CLIQUANT SUR L'ICONE AJOUTER
    $("#ajouter_photo").click(function(e){
    	e.preventDefault();
    });
    
    //VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
    //VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
    //VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
    
            var      nom = $("#NOM");
            var   prenom = $("#PRENOM");
            var     sexe = $("#SEXE");
            var date_naissance = $("#DATE_NAISSANCE");
            var lieu_naissance = $("#LIEU_NAISSANCE");
            var nationalite_origine = $("#NATIONALITE_ORIGINE");
            var nationalite_actuelle = $("#NATIONALITE_ACTUELLE");
            var     adresse = $("#ADRESSE");
            var   telephone = $("#TELEPHONE");
            var       email = $("#EMAIL");
            var  profession = $("#PROFESSION");
            var  age_ = $("#AGE");
    	
    //$( "button" ).button(); // APPLICATION DU STYLE POUR LES BOUTONS
    var mdclick = 0;
  	$( "#modifer_donnees" ).click(function(){
  		
    		if(mdclick == 1){
               nom.attr( 'readonly', false );    
            prenom.attr( 'readonly', false );  
              sexe.attr( 'readonly', false );
    date_naissance.attr( 'readonly', false );
    lieu_naissance.attr( 'readonly', false );
    nationalite_origine.attr( 'readonly', false );
    nationalite_actuelle.attr( 'readonly', false );
        adresse.attr( 'readonly', false );
      telephone.attr( 'readonly', false );
          email.attr( 'readonly', false );
     profession.attr( 'readonly', false );
     age_.attr( 'readonly', false );
     
    			mdclick = 0;
    		} else { 
	            nom.attr( 'readonly', true );    
	         prenom.attr( 'readonly', true );  
	           sexe.attr( 'readonly', true );
     date_naissance.attr( 'readonly', true );
     lieu_naissance.attr( 'readonly', true );
     nationalite_origine.attr( 'readonly', true );
     nationalite_actuelle.attr( 'readonly', true );
          adresse.attr( 'readonly', true );
        telephone.attr( 'readonly', true );
            email.attr( 'readonly', true );
       profession.attr( 'readonly', true );
       age_.attr( 'readonly', true );
       
    			mdclick = 1;
    		}
          
  	});
  	
  	//MENU GAUCHE
  	//MENU GAUCHE
  	
  	$("#vider").click(function(){
  		$('#LIEU_NAISSANCE').val('');
  		$('#EMAIL').val('');
  		$('#NOM').val('');
  		$('#TELEPHONE').val('');
  		//$('#NATIONALITE_ORIGINE').val('');
  		$('#PRENOM').val('');
  		//$('#NATIONALITE_ACTUELLE').val('');
  		$('#DATE_NAISSANCE').val('');
  		$('#ADRESSE').val('');
  		$('#PROFESSION').val('');
  		
  		return false;
  	});
  	
  	
 
  		$('#vider_champ').hover(function(){
  			
  			 $(this).css('background','url("'+tabUrl[0]+'public/images_icons/annuler2.png") no-repeat right top');
  		},function(){
  			  $(this).css('background','url("'+tabUrl[0]+'public/images_icons/annuler1.png") no-repeat right top');
  	    });

  		$('#div_supprimer_photo').hover(function(){
  			
  			 $(this).css('background','url("'+tabUrl[0]+'public/images_icons/mod2.png") no-repeat right top');
  		},function(){
  			  $(this).css('background','url("'+tabUrl[0]+'public/images_icons/mod.png") no-repeat right top');
  	    });

  		$('#div_ajouter_photo').hover(function(){
  			
  			 $(this).css('background','url("'+tabUrl[0]+'public/images_icons/ajouterphoto2.png") no-repeat right top');
  		},function(){
  			  $(this).css('background','url("'+tabUrl[0]+'public/images_icons/ajouterphoto.png") no-repeat right top');
  	    });

  		$('#div_modifier_donnees').hover(function(){
  			
  			 $(this).css('background','url("'+tabUrl[0]+'public/images_icons/modifier2.png") no-repeat right top');
  		},function(){
  			  $(this).css('background','url("'+tabUrl[0]+'public/images_icons/modifier.png") no-repeat right top');
  	   });
  
  //FIN VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
  //FIN VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
  //FIN VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
  		
  		$('#DATE_NAISSANCE').datepicker(
    			$.datepicker.regional['fr'] = {
    					closeText: 'Fermer',
    					changeYear: true,
    					yearRange: 'c-80:c',
    					prevText: '&#x3c;Préc',
    					nextText: 'Suiv&#x3e;',
    					currentText: 'Courant',
    					monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
    					'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
    					monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Juin',
    					'Jul','Aout','Sep','Oct','Nov','Dec'],
    					dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
    					dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
    					dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
    					weekHeader: 'Sm',
    					dateFormat: 'dd/mm/yy',
    					firstDay: 1,
    					isRTL: false,
    					showMonthAfterYear: false,
    					yearRange: '1900:2050',
    					showAnim : 'bounce',
    					changeMonth: true,
    					changeYear: true,
    					yearSuffix: '',
    					maxDate: 0
    			}
    	);
  		

  		function lesMois(n)
  		{
  			switch(n) {
  			  case 1: return "January";
  			  case 2: return "February";
  			  case 3: return "March";
  			  case 4: return "April";
  			  case 5: return "May";
  			  case 6: return "June";
  			  case 7: return "July";
  			  case 8: return "August";
  			  case 9: return "September";
  			  case 10: return "October";
  			  case 11: return "November";
  			  case 12: return "December";
  			}
  		}
  		
  		function age(birthday)
  		{
  			birthday = new Date(birthday);
  			return parseInt( new Number((new Date().getTime() - birthday.getTime()) / 31536000000));
  		}

  		$('#DATE_NAISSANCE').change(function(){

  			var date = $('#DATE_NAISSANCE').val(); 
  			var mois = parseInt(date[3]+''+date[4]);
  			var moisEnLettre = lesMois(mois);
  		    var birthday = date[0]+date[1]+' '+moisEnLettre+' '+date[6]+date[7]+date[8]+date[9];
  		    var Age = age(birthday);
 
  		    if( date && !isNaN(Age)){
  		    	$('#AGE').val(Age).attr('readonly', true).css('background','#efefef');
  		    }else{
  		    	$('#AGE, #DATE_NAISSANCE').val('').attr('readonly', false).css('background','#ffffff');
  		    }

  		});
  		
  		function miseAJourAge(date){
  			var mois = parseInt(date[3]+''+date[4]);
  			var moisEnLettre = lesMois(mois);
  		    var birthday = date[0]+date[1]+' '+moisEnLettre+' '+date[6]+date[7]+date[8]+date[9];
  		    var Age = age(birthday);
  		}
  		
});



//GESTION DES ALERTES DES PATIENTS DEJA EXISTANTS
//GESTION DES ALERTES DES PATIENTS DEJA EXISTANTS
//GESTION DES ALERTES DES PATIENTS DEJA EXISTANTS
function nbOccurrenceElement(myarray, element){
	
	var indices = [];
	var idx = myarray.indexOf(element);
	
	while (idx != -1) {
	  indices.push(idx);
	  idx = myarray.indexOf(element, idx + 1);
	}

	return indices.length;
}


function alertPatientExistant(myarray, myarrayId, myarrayAge, myarrayTelephone){
	
	$(function(){ 
	  
		var sexeSaisi = "";
		var nomSaisi = "";
		var prenomSaisi = "";
	  
		$('#SEXE').change(function(){
			sexeSaisi = $(this).val();
			
			var valeurSaisie = (sexeSaisi+''+nomSaisi+''+prenomSaisi).toLowerCase().replace(/ /g,"");
			var valeurRecherche = $.inArray(valeurSaisie, myarray);
			
			if( valeurRecherche > -1 ){ 
				
				var nbOccEl = nbOccurrenceElement(myarray, valeurSaisie);
				
				//Affichage des donn�es
				$('.alertPrenom').html(prenomSaisi);
				$('.alertNom').html(nomSaisi);
				$('.alertAge').html(myarrayAge[valeurSaisie]);
				if(myarrayId[valeurSaisie]){
					$('.alertVisualiserLien').html("<a href='"+tabUrl[0]+"public/facturation/info-patient/id_patient/"+myarrayId[valeurSaisie]+"'>  <img style='width: 25px; height: 16px; cursor: pointer;' src='"+tabUrl[0]+"public/images_icons/voir2.png' /> </a>");
					$('.alertModifierLien').html("<a href='"+tabUrl[0]+"public/facturation/modifier/id_patient/"+myarrayId[valeurSaisie]+"'>  <img style='width: 24px; height: 25px; cursor: pointer;' src='"+tabUrl[0]+"public/images_icons/2.png' /> </a>");					
				}
				if(myarrayTelephone[valeurSaisie]){ $('.alertTel').html(myarrayTelephone[valeurSaisie]); }else{ $('.alertTel').html('n&eacute;ant'); }
				
				//Afficher le nombre d'�l�ments s'il y en a plus d'un
				if(nbOccEl > 1){
					$('.alertNbPatient').html("<a href='"+tabUrl[0]+"public/facturation/liste-patient' style='color: red;' > "+nbOccEl+" </a>");					
				}
				
				$('#clickOuvrirPopup').trigger('click');
				//setTimeout(function(){ $('#volet').trigger('dblclick'); }, 30000);
			}else{
				$('#volet').trigger('dblclick');
			}

		});

	
		$('#NOM').change(function(){
			nomSaisi = $(this).val();
		  
			var valeurSaisie = (sexeSaisi+''+nomSaisi+''+prenomSaisi).toLowerCase().replace(/ /g,"");
			var valeurRecherche = $.inArray(valeurSaisie, myarray);
			
			if( valeurRecherche > -1 ){ 
				
				var nbOccEl = nbOccurrenceElement(myarray, valeurSaisie);
				
				//Affichage des donn�es
				$('.alertPrenom').html(prenomSaisi);
				$('.alertNom').html(nomSaisi);
				$('.alertAge').html(myarrayAge[valeurSaisie]);
				if(myarrayId[valeurSaisie]){
					$('.alertVisualiserLien').html("<a href='"+tabUrl[0]+"public/facturation/info-patient/id_patient/"+myarrayId[valeurSaisie]+"'>  <img style='width: 25px; height: 16px; cursor: pointer;' src='"+tabUrl[0]+"public/images_icons/voir2.png' /> </a>");
					$('.alertModifierLien').html("<a href='"+tabUrl[0]+"public/facturation/modifier/id_patient/"+myarrayId[valeurSaisie]+"'>  <img style='width: 24px; height: 25px; cursor: pointer;' src='"+tabUrl[0]+"public/images_icons/2.png' /> </a>");					
				}
				if(myarrayTelephone[valeurSaisie]){ $('.alertTel').html(myarrayTelephone[valeurSaisie]); }else{ $('.alertTel').html('n&eacute;ant'); }
				
				//Afficher le nombre d'�l�ments s'il y en a plus d'un
				if(nbOccEl > 1){
					$('.alertNbPatient').html("<a href='"+tabUrl[0]+"public/facturation/liste-patient' style='color: red;'> "+nbOccEl+" </a>");					
				}
				
				$('#clickOuvrirPopup').trigger('click');
				//setTimeout(function(){ $('#volet').trigger('dblclick'); }, 30000);
			}else{
				$('#volet').trigger('dblclick');
			}
	  
		});
		
	  
		$('#PRENOM').change(function(){
			prenomSaisi = $(this).val();

			var valeurSaisie = (sexeSaisi+''+nomSaisi+''+prenomSaisi).toLowerCase().replace(/ /g,"");
			var valeurRecherche = $.inArray(valeurSaisie, myarray);
			
			if( valeurRecherche > -1 ){ 
				
				var nbOccEl = nbOccurrenceElement(myarray, valeurSaisie);
				
				//Affichage des donn�es
				$('.alertPrenom').html(prenomSaisi);
				$('.alertNom').html(nomSaisi);
				$('.alertAge').html(myarrayAge[valeurSaisie]);
				if(myarrayId[valeurSaisie]){
					$('.alertVisualiserLien').html("<a href='"+tabUrl[0]+"public/facturation/info-patient/id_patient/"+myarrayId[valeurSaisie]+"'>  <img style='width: 25px; height: 16px; cursor: pointer;' src='"+tabUrl[0]+"public/images_icons/voir2.png' /> </a>");
					$('.alertModifierLien').html("<a href='"+tabUrl[0]+"public/facturation/modifier/id_patient/"+myarrayId[valeurSaisie]+"'>  <img style='width: 24px; height: 25px; cursor: pointer;' src='"+tabUrl[0]+"public/images_icons/2.png' /> </a>");					
				}
				if(myarrayTelephone[valeurSaisie]){ $('.alertTel').html(myarrayTelephone[valeurSaisie]); }else{ $('.alertTel').html('n&eacute;ant'); }
				
				//Afficher le nombre d'�l�ments s'il y en a plus d'un
				if(nbOccEl > 1){
					$('.alertNbPatient').html("<a href='"+tabUrl[0]+"public/facturation/liste-patient' style='color: red;'> "+nbOccEl+" </a>");					
				}
				
				$('#clickOuvrirPopup').trigger('click');
				//setTimeout(function(){ $('#volet').trigger('dblclick'); }, 30000);
			}else{
				$('#volet').trigger('dblclick');
			}
	  
		});
		
  
		
	});

}





































/**
 * =========================================================================================================
 * ---------------------------------------------------------------------------------------------------------
 * =========================================================================================================
 * -------------------------------------- Interface A deux Volets ------------------------------------------
 * ---------------------------------------------------------------------------------------------------------
 */


var tableBdTypeElementSelect = "";
var tableBdElementSelect = "";
var libElementSelect = "";
var inputChampSelect = "";

/**
 * AJOUTER UN QUARTIER --- AJOUTER UN QUARTIER --- AJOUTER UN QUARTIER
 * AJOUTER UN QUARTIER --- AJOUTER UN QUARTIER --- AJOUTER UN QUARTIER
 */
    
function ajouterUnQartierDeSaintLouis(){
	var libTypeElement = 'commune';
	var libElement = 'quartier';
	var tabTypeElemBD = 'commune_saint_louis';
	var tabElementBD = 'quartier_saint_louis';
        inputChampSelect = '#COMMUNE_SAINTLOUIS';
	
	tableBdTypeElementSelect = tabTypeElemBD;
	tableBdElementSelect = tabElementBD;
	libElementSelect = libElement;
	
	
	ajouterElements(libTypeElement, libElement, tabTypeElemBD);
	$('.ligneBoutonsAjout .boutonATP button').toggle(false);
	$(".ligneInfosAjoutElements .LIAP span span").html('Ajout de quartiers');
	
	afficherListeElementDuType(0);
}

function getListeQuartierSaintLouis(idcommune){
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/consultation/liste-quartier-select',
		data : {'idcommune':idcommune},
		success : function(data) {
			var result = jQuery.parseJSON(data);
			
			$("#QUARTIER_SAINTLOUIS").html(result);
		}
	});
}




/**
 * GESTION DE LA PREMIERE PARTIE
 */
$(function() {
    $( "#ajouterElements button" ).button();
});
var crerAttrTitleUneFois = 0; 
function ajouterElements(libTypeElement, libElement, tabTypeElemBD){
	
	if(crerAttrTitleUneFois == 0){ 
		$("#ajouterElements").attr('title','Gestion des '+libElement+'s'); 
		$("#modifierTypeElement").attr('title','Modifier un '+libElement); 
		crerAttrTitleUneFois = 1;
	}
	$("#labListeTypeElement").html('Les '+libTypeElement+'s');
	$("#labListeElement").html('Les '+libElement+'s');
	$(".ligneBoutonsAjout .boutonATP button").html('Ajouter une '+libTypeElement);
	$(".ligneBoutonsAjout .boutonAP button").html('Ajouter un '+libElement);
	
	$( "#ajouterElements" ).dialog({
		resizable: false,
	    height:680,
	    width:750,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Fermer": function() {
              $( this ).dialog( "close" );
	        }
	    }
	});
	
	$("#ajouterElements").dialog('open');
	
	
	$("#contenuInterfaceAjoutTypeElements").toggle(false);
	$(".ligneBoutonsAjout").toggle(true);
	recupererListeTypesElementsOptionSelect(tabTypeElemBD);
	recupererListeTypesElements(tabTypeElemBD);
	
}

var listeTypesElementsSelectOption = "";
function recupererListeTypesElementsOptionSelect(tabTypeElemBD){
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/consultation/liste-types-elements-select',
		data : {'tabTypeElemBD':tabTypeElemBD},
		success : function(data) {
			var result = jQuery.parseJSON(data);
			listeTypesElementsSelectOption = result;
		}
	});

}


function recupererListeTypesElements(tabTypeElemBD){ 
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/consultation/liste-types-elements',
		data : {'tabTypeElemBD':tabTypeElemBD},
		success : function(data) {
			var result = jQuery.parseJSON(data);
			$('.listeTypeElementsExistantes table').html(result);
		}
	});

}




/**
 * GESTION DE LA DEUXIEME PARTIE
 */
var variableTypeElement = 0;
var variableElement = 0;

function ajoutTypeElement(){
	variableTypeElement = 1;
	variableElement = 0;
	
	$('.ligneBoutonsAjout').fadeOut(function(){
		$(".ligneInfosAjoutElements .LIATP span").toggle(true);
		$(".ligneInfosAjoutElements .LIAP span").toggle(false);
		$("#contenuInterfaceAjoutTypeElements").toggle(true);
	});
	
	$('.interfaceAjoutElements .contenuIAPath .identifCAP').remove();
	
	if($('.champsAjoutTP').length == 0){
		ajouterUneNouvelleLigneElement();
	}
}

function ajoutElement(){
	variableElement = 1;
	variableTypeElement = 0;
	$('.ligneBoutonsAjout').fadeOut(function(){
		$(".ligneInfosAjoutElements .LIAP span").toggle(true);
		$(".ligneInfosAjoutElements .LIATP span").toggle(false);
		$("#contenuInterfaceAjoutTypeElements").toggle(true);
	});
	
	$('.interfaceAjoutElements .contenuIAPath .identifCAP').remove();
	
	if($('.champsAjoutTP').length == 0){
		ajouterUneNouvelleLigneElement();
	}
}

function annulerAjoutElement(){
	$('#contenuInterfaceAjoutTypeElements').fadeOut(function(){
		$(".ligneBoutonsAjout").toggle(true);
	});
}

function ajouterUneNouvelleLigneElement(){
	
	if(variableTypeElement == 1){
		
		var nbLigne = $('.champsAjoutTP').length;
		
		var ligne ="<tr class='champsAjoutTP  identifCAP  champsAjoutTP_"+(nbLigne+1)+"'>"+
	               "<td><input type='text' placeholder='Ecrire un nouveau type d\'element &agrave; ajouter'></td>"+
	               "</tr>";
		
		$('.interfaceAjoutElements .contenuIAPath .champsAjoutTP_'+(nbLigne)).after(ligne);
		
		if((nbLigne+1) > 1){ 
			$('.iconeAnnulerAP').toggle(true);
		}else if((nbLigne+1) == 1){
			$('.iconeAnnulerAP').toggle(false);
		}
		
	}else 
		if(variableElement == 1){ 
			
			var nbLigne = $('.champsAjoutP').length;
			
			var ligne ="<tr class='champsAjoutP  identifCAP  champsAjoutTP_"+(nbLigne+1)+"'>"+
		               "<td> <select>"+listeTypesElementsSelectOption+"</select> <input type='text' placeholder='Ecrire un nouvel element &agrave; ajouter'></td>"+
		               "</tr>";
			
			$('.interfaceAjoutElements .contenuIAPath .champsAjoutTP_'+(nbLigne)).after(ligne);
			
			if((nbLigne+1) > 1){ 
				$('.iconeAnnulerAP').toggle(true);
			}else if((nbLigne+1) == 1){
				$('.iconeAnnulerAP').toggle(false);
			}
			
		}
	
}


function enleverUneLigneElement(){
	
	if(variableTypeElement == 1){
		
		var nbLigne = $('.champsAjoutTP').length;
		if(nbLigne > 1){
			$('.champsAjoutTP_'+nbLigne).remove();
			if(nbLigne == 2){ $('.iconeAnnulerAP').toggle(false); }
		}
		
	}else 
		if(variableElement == 1){ 
			
			var nbLigne = $('.champsAjoutP').length; 
			if(nbLigne > 1){
				$('.champsAjoutTP_'+nbLigne).remove();
				if(nbLigne == 2){ $('.iconeAnnulerAP').toggle(false); }
			}
			
		}

}



function validerAjoutElement(){
	
	if(variableTypeElement == 1){
		var nbLigne = $('.champsAjoutTP').length;

		var tabTypeElement = new Array();
		var j = 0;
		for(var i=1; i<=nbLigne ; i++){
			var valeurChamp = $('.champsAjoutTP_'+i+' input').val(); 
			if(valeurChamp){
				tabTypeElement [j++] =  valeurChamp;
			}
		}
		
		if(tabTypeElement.length != 0){
			var reponse = confirm("Confirmer l'enregistrement de(s) type(s) de element");
			if (reponse == false) { return false; }
			else{ enregistrementTypeElement(tabTypeElement); }
		}
		
	}else
		if(variableElement == 1){ 
			var nbLigne = $('.champsAjoutP').length;
			
			var tabElement = new Array();
			var tabTypeElement = new Array();
			var j = 0;
			for(var i=1; i<=nbLigne ; i++){
				var valeurChamp = $('.champsAjoutTP_'+i+' input').val();
				if(valeurChamp){
					tabElement [j] = valeurChamp;
					tabTypeElement[j++] = $('.champsAjoutTP_'+i+' select').val();
				}
			}
			
			if(tabElement.length != 0){
				var reponse = confirm("Confirmer l'enregistrement de(s) element(s)");
				if (reponse == false) { return false; }
				else{ enregistrementElement(tabTypeElement, tabElement); }
			}
			
		}
}

/*
function enregistrementTypeElement(tabTypeElement){

	$('.boutonAVAV button').attr('disabled', true);
	$('.champsAjoutTP input').attr('disabled', true);
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/consultation/enregistrement-type-element',
		data : {'tabTypeElement' : tabTypeElement},
		success : function(data) {
			
			$('.listeTypeElementsExistantes table').html('<tr> <td style="margin-top: 35px; border: 1px solid #ffffff; text-align: center;"> Chargement </td> </tr>  <tr> <td align="center" style="border: 1px solid #ffffff; text-align: center;"> <img style="margin-top: 13px; width: 50px; height: 50px;" src="'+tabUrl[0]+'public/images/loading/Chargement_1.gif" /> </td> </tr>');
			recupererListeTypesElements();
			$('.listeElementsExistantes table').html('');
			recupererListeTypesElementsOptionSelect();
			$('.boutonAVAV button').attr('disabled', false);
			ajoutTypeElement();
			
		}
	});
}
*/

function enregistrementElement(tabTypeElement, tabElement){
	
	$('.boutonAVAV button').attr('disabled', true);
	$('.champsAjoutP select, .champsAjoutP input').attr('disabled', true);
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/consultation/enregistrement-element',
		data : {'tabTypeElement' : tabTypeElement, 'tabElement' : tabElement, 'tableBdElementSelect':tableBdElementSelect},
		success : function(data) {
			
			afficherListeElementDuType(tabTypeElement[0]);
			$('.boutonAVAV button').attr('disabled', false);
			ajoutElement();
		}
	});
}

function afficherListeElementDuType(id){
	$('.listeElementsExistantes table').html('<tr> <td style="margin-top: 35px; border: 1px solid #ffffff; text-align: center;"> Chargement </td> </tr>  <tr> <td align="center" style="border: 1px solid #ffffff; text-align: center;"> <img style="margin-top: 13px; width: 50px; height: 50px;" src="'+tabUrl[0]+'public/images/loading/Chargement_1.gif" /> </td> </tr>');
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/consultation/liste-elements-pour-interface-ajout',
		data : {'id':id, 'tableBdElementSelect':tableBdElementSelect, 'tableBdTypeElementSelect':tableBdTypeElementSelect},
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			$('.listeElementsExistantes table').html(result[0]);
			
			$('.LTPE1 a').html("<img src='"+tabUrl[0]+"public/images_icons/light/triangle_right.png'>");
			$('.iconeIndicateurChoix_'+result[1]+' a').html("<img src='"+tabUrl[0]+"public/images_icons/greenarrowright.png'>");
		}
	});
}

function modifierInfosTypeElement(id){
	alert('Prochaines mises a jour pour les modifications !');
}

function modifierInfosElement(id){
	
	var libelleElement = $('.listeElementsExistantes table .LPE2_'+id+' span').html();
	var html ="<tr><td>"+libelleElement+"</td></tr>";
	
	$('#infosConfirmationModification').html(html);
	
	$( "#modifierTypeElement" ).dialog({
		resizable: false,
	    height:300,
	    width:450,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	    	"Annuler": function() {
	    		$( this ).dialog( "close" );
		    },
	        "Modifier": function() {

	        	var libelleElementModif = $('#affichageMessageInfosRemplaceModification input').val();
	        	if(libelleElementModif){
	        		
		        	var reponse = confirm("Confirmer la modification du terme suivant");
					if (reponse == false) { return false; }
					else{ 
				      	$('.listeElementsExistantes table .LPE2_'+id+' span').html(libelleElement+ " <img style='margin-left: 5px; width: 18px; height: 18px;' src='"+tabUrl[0]+"public/images/loading/Chargement_1.gif' />");
			        	$( this ).dialog( "close" );
			        	
			        	$.ajax({
			        		
			        		type : 'POST',
			        		url : tabUrl[0] + 'public/consultation/modifier-element',
			        		data : {'idElement' : id, 'libelleElement' : libelleElementModif, 'tableBdElementSelect':tableBdElementSelect },
			        		success : function(data) {
			        			
			        			$(inputChampSelect).val("").trigger('change');
			        			
			        			$('.listeElementsExistantes table .LPE2_'+id+' span').html(libelleElementModif);
			        			$('#affichageMessageInfosRemplaceModification input').val('');
			        		}
			        	});
					}
	        	}
	        	
	        }
	    }
	});
	
	$("#modifierTypeElement").dialog('open');

}

















/**
 * =========================================================================================================
 * ---------------------------------------------------------------------------------------------------------
 * =========================================================================================================
 * -------------------------------------- Interface A un Volet ---------------------------------------------
 * ---------------------------------------------------------------------------------------------------------
 * =========================================================================================================
 */


$(function() {
    $( "#ajouterElementsUV button" ).button();
});

/**
 * Legend : UV = Un volet ();
 */

var tableElementUV = "";
var libElementUV = "";
var idChampElement = "";


/**
 * AJOUTER UNE RACE --- AJOUTER UNE RACE --- AJOUTER UNE RACE
 * AJOUTER UNE RACE --- AJOUTER UNE RACE --- AJOUTER UNE RACE
 */
function ajouterUneRace(){
	
	libElementUV = 'race';
	tableElementUV = 'liste_race';
	idChampElement = 'RACE';

	$("#labListeElementUV").html('Les '+libElementUV+'s');
	$(".ligneBoutonsAjoutUV .boutonAPUV button").html('Ajouter une '+libElementUV);
	
	ajouterElementsUV(libElementUV, tableElementUV);
	$(".ligneInfosAjoutElementsUV .LIAPUV span").html('Ajout de races');
	$(".ligneInfosAjoutElementsUV .LIAPUV, .interfaceAjoutElementsUV, .ligneBoutonsAnnulerValiderAjout").toggle(false);
	
	afficherListeElementUV();
}


/**
 * AJOUTER UNE ETHNIE --- AJOUTER UNE ETHNIE --- AJOUTER UNE ETHNIE
 * AJOUTER UNE ETHNIE --- AJOUTER UNE ETHNIE --- AJOUTER UNE ETHNIE
 */
function ajouterUneEthnie(){
	
	libElementUV = 'ethnie';
	tableElementUV = 'liste_ethnie';
	idChampElement = 'ETHNIE';

	$("#labListeElementUV").html('Les '+libElementUV+'s');
	$(".ligneBoutonsAjoutUV .boutonAPUV button").html('Ajouter une '+libElementUV);
	
	ajouterElementsUV(libElementUV, tableElementUV);
	$(".ligneInfosAjoutElementsUV .LIAPUV span").html("Ajout d'ethnie");
	$(".ligneInfosAjoutElementsUV .LIAPUV, .interfaceAjoutElementsUV, .ligneBoutonsAnnulerValiderAjout").toggle(false);
	
	afficherListeElementUV();
}



































function ajouterElementsUV(libElementUV, tableElementUV){
	
	$( "#ajouterElementsUV" ).dialog({
		resizable: false,
	    height:680,
	    width:350,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Fermer": function() {
              $( this ).dialog( "close" );
	        }
	    }
	});
	
	$("#ajouterElementsUV").dialog('open');
	$(".ligneBoutonsAjoutUV").toggle(true);
}


function ajoutElementUV(){
	
	$('.ligneBoutonsAjoutUV').fadeOut(function(){
		$(".ligneInfosAjoutElementsUV .LIAPUV").toggle(true);
		$("#contenuInterfaceAjoutTypeElementsUV, .interfaceAjoutElementsUV, .ligneBoutonsAnnulerValiderAjout").toggle(true);
	});

	$('.interfaceAjoutElementsUV .identifCAPUV').remove();
	
	if($('.identifCAPUV').length == 0){
		ajouterUneNouvelleLigneElementUV();
	}
}

function ajouterUneNouvelleLigneElementUV(){
	
	var nbLigne = $('.identifCAPUV').length;
	
	var ligne ="<tr class='identifCAPUV  champsAjoutTPUV_"+(nbLigne+1)+"' style='width: 100%;'>"+
               "<td style='width: 100%;'><input type='text' placeholder='Ecrire un nouvel element &agrave; ajouter' style='width: 96%;'> </td>"+
               "</tr>";
	
	$('.interfaceAjoutElementsUV .champsAjoutTPUV_'+(nbLigne)).after(ligne);
	
	if((nbLigne+1) > 1){ 
		$('.iconeAnnulerAPUV').toggle(true);
	}else if((nbLigne+1) == 1){
		$('.iconeAnnulerAPUV').toggle(false);
	}
	
}

function enleverUneLigneElementUV(){
	
	var nbLigne = $('.identifCAPUV').length; 
	if(nbLigne > 1){
		$('.champsAjoutTPUV_'+nbLigne).remove();
		if(nbLigne == 2){ $('.iconeAnnulerAPUV').toggle(false); }
	}

}

function annulerAjoutElementUV(){
	$('#contenuInterfaceAjoutTypeElementsUV').fadeOut(function(){
		$(".ligneBoutonsAjoutUV").toggle(true);
	});
}

function afficherListeElementUV(){
	
	$('.listeElementsExistantesUV table').html('<tr> <td style="margin-top: 35px; border: 1px solid #ffffff; text-align: center;"> Chargement </td> </tr>  <tr> <td align="center" style="border: 1px solid #ffffff; text-align: center;"> <img style="margin-top: 13px; width: 50px; height: 50px;" src="'+tabUrl[0]+'public/images/loading/Chargement_1.gif" /> </td> </tr>');
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/consultation/liste-elements-uv',
		data : {'tableElementUV':tableElementUV},
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			$('.listeElementsExistantesUV table').html(result);
		}
	});
	
}

function validerAjoutElementUV(){
	
	var nbLigne = $('.identifCAPUV').length;
	
	var tabElement = new Array();
	var j = 0;
	for(var i=1; i<=nbLigne ; i++){
		var valeurChamp = $('.champsAjoutTPUV_'+i+' input').val();
		if(valeurChamp){
			tabElement [j++] = valeurChamp;
		}
	}
	
	if(tabElement.length != 0){
		var reponse = confirm("Confirmer l'enregistrement de(s) element(s)");
		if (reponse == false) { return false; }
		else{ enregistrementElementUV(tabElement); }
	}
			
}

function enregistrementElementUV(tabElement){
	
	$('.boutonAVAV button').attr('disabled', true);
	$('.champsAjoutP select, .champsAjoutP input').attr('disabled', true);
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/consultation/enregistrement-element-uv',
		data : {'tableElementUV' : tableElementUV, 'tabElement' : tabElement},
		success : function(data) {
			
			afficherListeElementUV();
			$('#contenuInterfaceAjoutTypeElementsUV .boutonAVAV button').attr('disabled', false);
			ajoutElementUV();
			getListeElementUV();
		}
	});
}


function modifierInfosElementUV(id){
	
	var libelleElement = $('.listeElementsExistantesUV table .LPE2_'+id+' span').html();
	var html ="<tr><td>"+libelleElement+"</td></tr>";
	
	$('#infosConfirmationModificationUV').html(html);
	
	$( "#modifierTypeElementUV" ).dialog({
		resizable: false,
	    height:300,
	    width:450,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	    	"Annuler": function() {
	    		$( this ).dialog( "close" );
		    },
	        "Modifier": function() {

	        	var libelleElementModif = $('#affichageMessageInfosRemplaceModificationUV input').val();
	        	if(libelleElementModif){
	        		
		        	var reponse = confirm("Confirmer la modification du terme suivant");
					if (reponse == false) { return false; }
					else{
				      	$('.listeElementsExistantesUV table .LPE2_'+id+' span').html(libelleElement+ " <img style='margin-left: 5px; width: 18px; height: 18px;' src='"+tabUrl[0]+"public/images/loading/Chargement_1.gif' />");
			        	$( this ).dialog( "close" );
			        	
			        	$.ajax({
			        		type : 'POST',
			        		url : tabUrl[0] + 'public/consultation/modifier-element-uv',
			        		data : {'idElement' : id, 'libelleElement' : libelleElementModif, 'tableElementUV':tableElementUV },
			        		success : function(data) {
			        			
			        			$('.listeElementsExistantesUV table .LPE2_'+id+' span').html(libelleElementModif);
			        			$('#affichageMessageInfosRemplaceModificationUV input').val('');
			        			
			        			getListeElementUV();
			        		}
			        	});
					}
	        	}
	        }
	    }
	});
	
	$("#modifierTypeElementUV").dialog('open');

}


function getListeElementUV(){
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/consultation/liste-element-select',
		data : {'tableElementUV':tableElementUV},
		success : function(data) {
			var result = jQuery.parseJSON(data);
			$("#"+idChampElement).html(result);
		}
	});
}



function getListeDepartements(id){
	var chaine = "";
	
	if ( id == 1 ) { chaine = '<option value="1">DAKAR</option><option value="2">GUEDIAWAYE</option><option value="3">PIKINE</option><option value="4">RUFISQUE</option>'; }
    if ( id == 2 ) { chaine = '<option value="5">BAMBEY</option><option value="6">DIOURBEL</option><option value="7">MBACKE</option>'; }
    if ( id == 3 ) { chaine = '<option value="8">FATICK</option><option value="9">FOUNDIOUCK</option><option value="10">GOSSAS</option>'; }
    if ( id == 4 ) { chaine = '<option value="11">BIRKELANE</option><option value="12">KAFFRINE</option><option value="13">KOUNGHEUL</option><option value="14">MALEM HODDAR</option>'; }
    if ( id == 5 ) { chaine = '<option value="15">GUINGUINEO</option><option value="16">KAOLACK</option><option value="17">NIORO</option>'; }
    if ( id == 6 ) { chaine = '<option value="18">KEDOUGOU</option><option value="19">SALEMATA</option><option value="20">SARAYA</option>'; }
    if ( id == 7 ) { chaine = '<option value="21">KOLDA</option><option value="22">MEDINA YORO FOULAH</option><option value="23">VELINGARA</option>'; }
    if ( id == 8 ) { chaine = '<option value="24">KEBEMER</option><option value="25">LINGUERE</option><option value="26">LOUGA</option>'; }
    if ( id == 9 ) { chaine = '<option value="27">KANEL</option><option value="28">MATAM</option><option value="29">RANEROU</option>'; }
    if ( id == 10 ) { chaine = '<option value="30">DAGANA</option><option value="31">PODOR</option><option value="32">SAINT LOUIS</option>'; }
    if ( id == 11 ) { chaine = '<option value="33">BOUNKILING</option><option value="34">GOUDOMP</option><option value="35">SEDHIOU</option>'; }
    if ( id == 12 ) { chaine = '<option value="36">BAKEL</option><option value="37">GOUDIRY</option><option value="38">KOUPENTOUM</option><option value="39">TAMBACOUNDA</option>'; }
    if ( id == 13 ) { chaine = '<option value="40">MBOUR</option><option value="41">THIES</option><option value="42">TIVAOUANE</option>'; }
    if ( id == 14 ) { chaine = '<option value="43">BIGNONA</option><option value="44">OUSSOUYE</option><option value="45">ZIGUINCHOR</option>'; }
    
    $('#DEPARTEMENT').val('');
    $('#DEPARTEMENT').html(chaine);
}


