var base_url = window.location.toString();
var tabUrl = base_url.split("public");

$(function(){
	//Les accordeons
	$( "#accordions" ).accordion();
	$( "#consultationDeSuivi" ).accordion();
    
	
    
    //Les tables
    $( "#tabsAntecedents" ).tabs();

    
    
    //Les boutons
    $( "button" ).button();
	
    
    
    //Cliquer les bouton annuler
    $( "#annulerConsultation" ).click(function(){
    	
    	$( "#confirmationAnnulation" ).dialog({
    		resizable: false,
    	    height:180,
    	    width:520,
    	    autoOpen: false,
    	    modal: true,
    	    buttons: {
    	    	"Non": function() {
    	        	$( this ).dialog( "close" );
    	        },
    	        
    	        "Oui": function() {
    	        	$( this ).dialog( "close" );
    	    		$(location).attr("href",tabUrl[0]+"public/consultation/liste-consultations");
    	        },
    	   }
    	});
    	
    	$("#confirmationAnnulation").dialog('open');
    	
    	return false;
    });
    
    
    
    //Ajustement de la hauteur de l'interface des "Symptômes"
    setTimeout(function(){
        $(".zoneContenuObmre").css({"max-height":"320px"});
    },1000);
    
    
    
    
    
    
    
    
    //ANTECEDENTS ET HISTORIUES ---  ANTECEDENTS ET HISTORIUES
    //ANTECEDENTS ET HISTORIUES ---  ANTECEDENTS ET HISTORIUES
    $('.image1_TP').click(function(){
    	$("#MenuTerrainParticulier").fadeOut(function(){
    		$("#HabitudesDeVie").fadeIn();
    	});
    });
    
    $('.image2_TP').click(function(){
    	$("#MenuTerrainParticulier").fadeOut(function(){
    		$("#AntecedentMedicaux").fadeIn();
    	});
    });
    
    $('.image3_TP').click(function(){
    	$("#MenuTerrainParticulier").fadeOut(function(){
    		$("#AntecedentChirurgicaux").fadeIn();
    	});
    });
	
	setTimeout(function(){
		
		//HABITUDES DE VIE ---- HABITUDES DE VIE
		//HABITUDES DE VIE ---- HABITUDES DE VIE
		$(".TerminerContenuDiabetiqueConnu" ).html("<button id='TerminerContenuDiabetiqueConnu' style='height:35px;'>Terminer</button>"); 
	
		$("#TerminerContenuDiabetiqueConnu").click(function(){
			$("#HabitudesDeVie").fadeOut(function(){ 
				$("#MenuTerrainParticulier").fadeIn("fast");
			});

			return false;
		}); 
		
		
		//ANTECEDENTS MEDICAUX --- ANTECEDENTS MEDICAUX
		//ANTECEDENTS MEDICAUX --- ANTECEDENTS MEDICAUX
		$(".TerminerAntecedentsMedicaux" ).html("<button id='TerminerAntecedentsMedicaux' style='height:35px;'>Terminer</button>"); 
		
		$("#TerminerAntecedentsMedicaux").click(function(){
			$("#AntecedentMedicaux").fadeOut(function(){ 
				$("#MenuTerrainParticulier").fadeIn("fast");
			});

			return false;
		}); 
		
		
		//ANTECEDENTS CHIRURGICAUX --- ANTECEDENTS CHIRURGICAUX
		//ANTECEDENTS CHIRURGICAUX --- ANTECEDENTS CHIRURGICAUX
		$(".TerminerAntecedentsChirurgicaux" ).html("<button id='TerminerAntecedentsChirurgicaux' style='height:35px;'>Terminer</button>"); 
		
		$("#TerminerAntecedentsChirurgicaux").click(function(){
			$("#AntecedentChirurgicaux").fadeOut(function(){ 
				$("#MenuTerrainParticulier").fadeIn("fast");
			});

			return false;
		}); 
	},1000);
    
});
  

function libelleAntChirurgicaux(id){
	if(id==1){
		$('#libelleAntecedentsChirurgicaux').toggle(true);
	}else{
		$('#libelleAntecedentsChirurgicaux').toggle(false);
	}
}


function getNoteObservation(id){
	if(id == 2){
		$('#labelNoteObservation, #interfaceDemDepistageCovid, #interfaceDepTraitement').toggle(true);

	}else{
		$('#labelNoteObservation, #interfaceDemDepistageCovid, #interfaceDepTraitement').toggle(false);
	}
}


function getTraitementWithResultatTestNegatif(id){

	/*
	if(idtypepatient == 2 && id == 2) {
		$('#interfaceDepTraitement').toggle(true);
	}else{
		$('#interfaceDepTraitement').toggle(false);
	}
	*/
}

function getResultatTestCovid19(id){

	if(id == 1){
		$('#selectResultatTestCovid19').toggle(true);
	}else{
		$('#selectResultatTestCovid19').toggle(false);
	}

}

/**
 * -------------------------------------------------------------------------
 * *************************************************************************
 * '''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
 * PARTIE GESTION DES SYMPTOMES --- DES SYMPTOMES
 * PARTIE GESTION DES SYMPTOMES --- DES SYMPTOMES
 * PARTIE GESTION DES SYMPTOMES --- DES SYMPTOMES
 * =========================================================================
 * =========================================================================
 * """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
 * _________________________________________________________________________
 *  
 */ 



 
/**
 * ZONE D'AJOUT DES INTERFACES DES LIGNES DE CONSULTATIONS
 * ZONE D'AJOUT DES INTERFACES DES LIGNES DE CONSULTATIONS
 * ZONE D'AJOUT DES INTERFACES DES LIGNES DE CONSULTATIONS
 */

var tableauInfosConsultations = new Array();
var tableauPlaintesSelectionnees = new Array();
var tableauNotePlaintesSelect = new Array();
var tabDisabledPlainteSelect = new Array();

function ajouterUneLigneDeSymptomes(){	
	var	uneDate = new Date();

	var nbLigne = $('.ligneConsultation').length;
	
	var interfaceChamps = '<table style="width: 99.5%; margin-left: 1px; margin-top: 0px; margin-bottom: 0px; height: 35px;" class="ligneConsultation ligneConsultation_'+(nbLigne+1)+' styleLineHoverNone">'+
	                        '<tr style="width: 100%; border-bottom: 2px solid #cccccc; font-family: times new roman; font-size: 19px; background: #fbfbfb" class="designStyleLabel">'+
				
							   '<td style="width: 6%; padding: 5px; border-right: 2px solid #ccc; padding-left: 15px;"> '+(nbLigne+1)+' </td>'+
							   '<td style="width: 20%; padding: 5px; border-right: 2px solid #ccc;" id="colonneDateHeure_'+(nbLigne+1)+'"> '+uneDate.toLocaleString()+' </td>'+
							   '<td style="width: 34%; padding: 5px; border-right: 2px solid #ccc;" id="colonneNonPrenom_'+(nbLigne+1)+'"> ' + $('#prenommedecin').val() + '  ' + $('#nommedecin').val() + ' </td>'+
							   '<td style="width: 10%; padding: 5px; border-right: 2px solid #ccc;" id="colonneNbSymptome_'+(nbLigne+1)+'"><span id="nbSymptomesSelected'+(nbLigne+1)+'"> </span></td>'+
							   '<td style="width: 20%; padding: 5px; border-right: 2px solid #ccc;" id="colonneSymptome'+(nbLigne+1)+'"> - </td>'+
							   '<td style="width: 10%; padding: 5px; "> <img style="cursor: pointer;" onclick="afficherTableauSymptomes('+(nbLigne+1)+')" src="../images_icons/Table16X16.png" title="Ajouter des sympt&ocirc;mes" /> </td>'+
							   
							'</tr>'+
                          '</table>';
						  
	$('.ligneConsultation_'+(nbLigne)).after(interfaceChamps);

	if(nbLigne == 0){ $('#symptome_moins').toggle(false); }
	else{ $('#symptome_moins').toggle(true); }
	$('#symptome_plus').toggle(true);

	$('#nbLigneConsultation').val(nbLigne+1);

	//alert('1aa');
	//Initialisation des tableaux de données
	//Initialisation des tableaux de données
	setTimeout(function(){
		tableauPlaintesSelectionnees[(nbLigne+1)] = new Array(); 
		tableauNotePlaintesSelect[(nbLigne+1)] = new Array();
		tabDisabledPlainteSelect [(nbLigne+1)] = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,];
		
		//Enregistrement des données de constantes
		tableauInfosConsultations[(nbLigne)] = new Array();
		tableauInfosConsultations[(nbLigne)][0] = $('#idcons').val();
		tableauInfosConsultations[(nbLigne)][1] = uneDate.toLocaleString();
		tableauInfosConsultations[(nbLigne)][2] = $('#idmedecin').val();
		tableauInfosConsultations[(nbLigne)][3] = $('#idpatient').val();
		//alert(tableauPlaintesSelectionnees.length);
	},100);

}


function enleverUneLigneDeSymptomes(){
	
	var nbLigne = $('.ligneConsultation').length; 

	for(var i=0 ; i<tableauPlaintesSelectionnees[nbLigne].length ; i++){
		if(tableauPlaintesSelectionnees[nbLigne][i] && tableauPlaintesSelectionnees[nbLigne][i] != -1){
			alert('Impossible de supprimer la ligne '+ nbLigne +', des symptomes sont deja selectionne');
			return false;
		}
	}
	//alert(tableauPlaintesSelectionnees[nbLigne]);

	if(nbLigne > 1){
		$('.ligneConsultation_'+nbLigne).remove();
		if(nbLigne == 2){ $('#symptome_moins').toggle(false); }
		
		$('#symptome_plus').toggle(true);
		$('#nbLigneConsultation').val(nbLigne-1);
	}
	
}

/**
 * GESTION DES DONNEES DES POPUPS ---  GESTION DES DONNEES DES POPUPS
 * GESTION DES DONNEES DES POPUPS ---  GESTION DES DONNEES DES POPUPS
 * AJOUT DE SYMPTOMES --- AJOUT DE SYMPTOMES --- AJOUT DE SYMPTOMES --- AJOUT DE SYMPTOMES
 */
var rowInfosSymptomes = new Array();

function afficherTableauSymptomes(id){ 
	//alert(tableauDonneesSymptomesBD);

	var zoneListeSymptomesHtml = ""+
		'<div class="zoneContenuObmre" id="zoneListeSymptomes_'+ id +'">'+
				
			'<div style="width: 100%; padding: 3px; margin-top: 6px; padding-bottom: 1px; ">'+
				'<table class="plainteEntre_'+ id +' plainteEntree_'+ id +'_0"></table>'+
			'</div>'+
			
			'<table style="width:100%;" >'+
				'<tr style="width: 100%">'+
					'<td style="width: 20%;">'+
					'<div id="plainte_moins_'+ id +'" class="examen_physique_pm" onclick="enleverUnePlainte('+ id +');" style="display: none;"> &minus; </div>'+ 
					'<div id="plainte_plus_'+ id +'"  class="examen_physique_pm" onclick="ajouterUneNouvellePlainte('+ id +');" style="display: none;"> + </div>'+
					'</td>'+
					'<td style="width: 80%;" ></td>'+
				'</tr>'+
			'</table>'+
			'<script type="text/javascript">ajouterUneNouvellePlainte('+ id +');</script>'+
		'</div>';
		//alert(2);
	/** PLACER DANS LA ZONE DU POPUP */
	$('#interfacePopupSymptomes').html(zoneListeSymptomesHtml);

	/** Vérifier s'il y a des symptomes existants à l'entrée */
	//alert(tableauDonneesSymptomesBD);
	if(tableauPlaintesSelectionnees.hasOwnProperty(id)){

		if(tableauDonneesSymptomesBD.length > 1 && tableauPlaintesSelectionnees[id].length == 0){
			//alert(tableauPlaintesSelectionnees[id].length);
			tableauPlaintesSelectionnees = tableauDonneesSymptomesBD;
			tableauNotePlaintesSelect = tableauDonneesNotesSympBD;

			//On initialise les infos de consltation du tableau 
			tableauInfosConsultations = tableauConsultationsBD;
		}

		//alert(id);
		//alert(tableauPlaintesSelectionnees.hasOwnProperty(id));


		/** Afficher les symptomes déjà sélectionné par consultation */
		for(var i=0 ; i<tableauPlaintesSelectionnees[id].length ; i++){
			var valSelect = tableauPlaintesSelectionnees[id][i];
			var note = tableauNotePlaintesSelect[id][i];

			if(valSelect && valSelect != -1){
				$('#listePlaintesSelect_'+id+'_'+(i)).val(valSelect);
				$('#noteInputPlainte_'+id+'_'+(i)).val(note);
				ajouterUneNouvellePlainte( id );
			}
		}

		/** Enlever la dernière ligne créée s'il y'en a */
		if(tableauPlaintesSelectionnees[id].length > 0){
			enleverUnePlainte( id );
		}
	}
	else{
		tableauPlaintesSelectionnees[id] = new Array();
	}
	

	/** AFFICHAGE DU POPUP */
	$( "#interfacePopupSymptomes" ).dialog({
		resizable: false,
		height:450,
		width:950,
		autoOpen: false,
		modal: true,
		buttons: {
			"Annuler": function() {
				$( this ).dialog( "close" );
			},
			
			"Valider": function() {
				$( this ).dialog( "close" );
				sauvegarderDonneesSymptomesValidees(id);
			},
		}
	});

	$( "#interfacePopupSymptomes" ).dialog('open');

}

/**
 * Sauvegarder les données des symptomes 
 */
function sauvegarderDonneesSymptomesValidees(id){

	//alert(tableauDonneesBD[id]);
	//alert(tableauPlaintesSelectionnees[id]);

	//return false;

	$.ajax({
		type: 'POST',
		url: tabUrl[0]+'public/consultation/enregistrement-symptomes',
		data: {'tableauInfosConsultations':tableauInfosConsultations[(id-1)], 'tableauPlaintesSelectionnees': tableauPlaintesSelectionnees[id], 'tableauNotePlaintesSelect':tableauNotePlaintesSelect[id]},       
		success: function(data) {
			// var result = jQuery.parseJSON(data);
			// alert(result);
		}
	});
}


/**
 * PLAINTES DE LA CONSULTATION  --- PLAINTES DE LA CONSULTATION 
 * PLAINTES DE LA CONSULTATION  --- PLAINTES DE LA CONSULTATION 
 * PLAINTES DE LA CONSULTATION  --- PLAINTES DE LA CONSULTATION 
 */

function getModelePlainteSelect(ligne, id){ 
	
	var liste = '<select id="listePlaintesSelect_'+ligne+'_'+id+'" style="float:right; width: 60%; height: 28px; font-family: time new roman; font-size: 19px; padding-left: 3px; margin-top: 2px;" onchange="getPlainteDisabledSelect('+ligne+', '+id+', this.value)">'+
	               '<option value=0 ></option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][1]+'" value=1 >Fi&egrave;vre</option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][2]+'" value=2 >Fatigue</option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][3]+'" value=3 >Toux s&egrave;che</option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][4]+'" value=4 >Difficulter &agrave; respirer</option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][5]+'" value=5 >Douleurs &agrave; la respiration</option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][6]+'" value=6 >Ecoulement nasal</option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][7]+'" value=7 >Congestion nasale</option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][8]+'" value=8 >Frissons</option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][9]+'" value=9 >Maux de t&ecirc;te</option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][10]+'" value=10 >Vertiges</option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][11]+'" value=11 >Pertes d\'app&eacute;tit</option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][12]+'" value=12 >Diarrh&eacute;e</option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][13]+'" value=13 >Douleurs aux muscles</option>'+
	               '<option class="disabledPlainteSelectOption'+ligne+'_'+tabDisabledPlainteSelect[ligne][14]+'" value=14 >Douleurs aux articulations</option>'+
	            "</select>";
	
	return liste;
}


function getPlainteDisabledSelect(ligneSymp, ligne, value){ //alert(ligneSymp+' - '+ligne+' - '+id);
	
	if(value==0){
		$('#plainte_plus_'+ligneSymp).toggle(false);
		
		/**Enregistrer les plaintes dans la table**/
		tableauPlaintesSelectionnees[ligneSymp][ligne] = -1;
	}else{
		$('#plainte_plus_'+ligneSymp).toggle(true);

		var nbLigne = $('.plainteEntree_'+ligneSymp).length; 

		/**Enregistrer les plaintes dans la table**/
		tableauPlaintesSelectionnees[ligneSymp][nbLigne] = value;
		// alert(tableauPlaintesSelectionnees[ligneSymp]);
	}
	
}


function ajouterUneNouvellePlainte(id){
	
	var nbLigne = $('.plainteEntree_'+id).length; 
	var valInputPlainteSelect = $('.champInputPlainte_'+id+'_'+nbLigne+' select').val();
	tabDisabledPlainteSelect[id][valInputPlainteSelect] = 1;
	
	//alert(valInputPlainteSelect);

	var interfaceChamps = '<table style="width: 100%;" class="plainteEntree_'+ id +' plainteEntree_'+ id +'_'+(nbLigne+1)+'">'+
	                        '<tr style="width: 100%" class="designStyleLabel">'+
		        
							   '<td style="width: 40%; padding-right: 25px;" class="PlainteDiagEntCol1">'+
							     '<label class="modeleChampInputPlainte champInputPlainte_'+id+'_'+(nbLigne+1)+'" style="width: 100%; height:30px; text-align:left;">'+
							       '<span id="suppPlainteEntree_'+ id +'_'+(nbLigne+1)+'" class="suppPlainteEntreeVisualiser" onclick="suppPlainteEntree('+ id +','+(nbLigne+1)+');" style="visibility: hidden; color: red; position: relative; font-family: arial; top: -10px; margin-left: 0px; font-size: 12px;">X</span>'+
						           '<span style="font-size: 11px;">&#10148; </span><span>Sympt&ocirc;me '+(nbLigne+1)+' </span>'+ 
						           getModelePlainteSelect(id, (nbLigne+1))+
						         '</label>'+
							   '</td>'+
							   
							   '<td style="width: 60%; padding-right: 13px;" class="PlainteDiagEntCol2">'+
							     '<label style="width: 100%; height:30px; margin-left: -10px; text-align:right;">'+
							       '<span> Note &nbsp;&nbsp; </span>'+ 
							       '<input type="text" id="noteInputPlainte_'+ id +'_'+(nbLigne+1)+'" name="noteInputPlainte_'+ id +'_'+(nbLigne+1)+'" style="float:right; width: 85%; height: 28px; font-family: time new roman; font-size: 19px; padding-left: 3px; margin-top: 2px;">'+
							     '</label>'+
							   '</td>'+
							   
	                        '</tr>'+
                          '</table>';
	
	
	$('.plainteEntree_'+ id +'_'+(nbLigne)).after(interfaceChamps);
	
	
	if((nbLigne+1) > 1){
		$('#plainte_moins_'+id).toggle(true);
	}else if((nbLigne+1) == 1){
		$('#plainte_plus_'+id).toggle(false);
	}
	
	if(nbLigne == 0){ $('#plainte_moins_'+id).toggle(false); }

	$('#plainte_plus_'+id).toggle(false);
	$('#listePlaintesSelect_'+ id +'_'+nbLigne).attr("disabled", true);
	$(".disabledPlainteSelectOption"+id+"_1").toggle(false);
	$(".disabledPlainteSelectOption"+id+"_0").toggle(true);		
	
	/**Affichage de l'icone supprime**/
	$(".plainteEntree_"+id+"_"+(nbLigne+1)+" label").hover(function(){
		$("#suppPlainteEntree_"+id+"_"+(nbLigne+1)).css({'visibility':'visible'});
	},function(){
		$("#suppPlainteEntree_"+id+"_"+(nbLigne+1)).css({'visibility':'hidden'});
	});
	
	/**Gestion de la note des plaintes**/
	$('#noteInputPlainte_'+id+'_'+(nbLigne+1)).keyup(function(){
		tableauNotePlaintesSelect[id][nbLigne+1] = $(this).val(); 
	});
	
	/** Affihcer le nombre de symptomes selectionné */
	$('#nbSymptomesSelected'+id).html(nbLigne+1);

}


function enleverUnePlainte(id){
	
	var nbLigne = $('.plainteEntree_'+id).length; 
	if(nbLigne > 1){
		$('.plainteEntree_'+id+'_'+nbLigne).remove();
		if(nbLigne == 2){ $('#plainte_moins_'+id).toggle(false); }
		
		$('#plainte_plus_'+id).toggle(true);

		$('#listePlaintesSelect_'+id+'_'+(nbLigne-1)).attr("disabled", false);

		var valInputPlainteSelect = $('#listePlaintesSelect_'+id+'_'+(nbLigne-1)).val();
		tabDisabledPlainteSelect[id][valInputPlainteSelect] = 0;

		var listeSelect = getModelePlainteSelect(id, (nbLigne-1));

		$('#listePlaintesSelect_'+id+'_'+(nbLigne-1)).replaceWith(listeSelect);

		$(".disabledPlainteSelectOption"+id+"_1").toggle(false);
		$(".disabledPlainteSelectOption"+id+"_0").toggle(true);

		$('#listePlaintesSelect_'+id+'_'+(nbLigne-1)).val(valInputPlainteSelect);
	
		/**Enregistrer les plaintes dans la table**/
		tableauPlaintesSelectionnees[id][nbLigne] = -1;
	}

	/** Affihcer le nombre de symptomes selectionné */
	$('#nbSymptomesSelected'+id).html(nbLigne-1);
	
}


/**
 * POPUP DE SUPPRESSION DES SYMPTOMES INTERMEDIAIRES
 * POPUP DE SUPPRESSION DES SYMPTOMES INTERMEDIAIRES
 */

function suppPlainteEntree(ligne, id){
	
	$('#suppPlainteEntree_'+ligne+'_'+id).w2overlay({ html: "" +
		"" +
		"<div style='border-bottom: 1px solid gray; height: 28px; background: #f9f9f9; width: 100px; text-align:center; padding-top: 10px; font-size: 13px; color: gray; font-weight: bold;'>Supprimer</div>" +
		"<div style='height: 35px; width: 80px; padding-top: 10px; margin-left: 10px;'>" +
		"<button class='commentChoicePVBut' onclick='popupFermer();'>Non</button>"+
		"<button class='commentChoicePVBut' onclick='supprimerLaPlainte("+ligne+","+id+");'>Oui</button>"+
		"</div>"+
		"<style> .w2ui-overlay{ margin-left: -35px; border: 1px solid gray; } </style>"
	});

}

function popupFermer() {
	$(null).w2overlay(null);
}

function supprimerLaPlainte(ligne, id){ //alert(ligne+' - '+id);
	$(null).w2overlay(null);
	
	/**On élimine la valeur à supprimer*/
	tableauPlaintesSelectionnees[ligne][id] = -1;
	var tamponTableauPlainteSelect = new Array();
	tamponTableauPlainteSelect[ligne] = new Array();
	tamponTableauPlainteSelect[ligne] = tableauPlaintesSelectionnees[ligne];
	
	//alert(tableauPlaintesSelectionnees[ligne]);
	/**On réinitialise tout*/
	$(".plainteEntree_"+ligne).remove();
	tableauPlaintesSelectionnees[ligne] = new Array();
	tabDisabledPlainteSelect[ligne] = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];

	/**On affiche les nouvelles valeurs*/
	var indice = 1;
	var newTableauNotePlaintesSelect = new Array();
	for(var i=1 ; i<tamponTableauPlainteSelect[ligne].length ; i++){
		
		if(tamponTableauPlainteSelect[ligne][i] != -1){
			ajouterUneNouvellePlainte(ligne);
			newTableauNotePlaintesSelect[indice] = tableauNotePlaintesSelect[ligne][i];
			$("#listePlaintesSelect_"+ligne+"_"+indice).val(tamponTableauPlainteSelect[ligne][i]).trigger('change');
			$('#noteInputPlainte_'+ligne+'_'+indice).val(tableauNotePlaintesSelect[ligne][i]);
			
			indice++;
		}else if(i==1 && (i+1)==tamponTableauPlainteSelect[ligne].length){
			ajouterUneNouvellePlainte(ligne);
		}

		//alert(tamponTableauPlainteSelect[ligne][i]);
	}
	
	tableauNotePlaintesSelect[ligne] = newTableauNotePlaintesSelect;
	
}



























































































































































var tabDisabledComplicationSelect = [0,0,0,0,0,0,0,0,0,0];

function getModeleComplicationSelect(id){
	
	var liste = '<select id="listeComplicationsSelect_'+id+'" style="float:right; width: 60%; height: 28px; font-family: time new roman; font-size: 19px; padding-left: 3px; margin-top: 2px;" onchange="getComplicationDisabledSelect('+id+', this.value)">'+
	               '<option value=0 ></option>'+
	               '<option class="disabledComplicationSelectOption'+tabDisabledComplicationSelect[1]+'" value=1 >Acido-c&eacute;tose</option>'+
	               '<option class="disabledComplicationSelectOption'+tabDisabledComplicationSelect[2]+'" value=2 >Coma hyperosmolaire</option>'+
	               '<option class="disabledComplicationSelectOption'+tabDisabledComplicationSelect[3]+'" value=3 >N&eacute;phropathie diab&egrave;tique</option>'+
	               '<option class="disabledComplicationSelectOption'+tabDisabledComplicationSelect[4]+'" value=4 >R&eacute;tinopathie diab&egrave;tique</option>'+
	               '<option class="disabledComplicationSelectOption'+tabDisabledComplicationSelect[5]+'" value=5 >Neuropathie p&eacute;riph&eacute;rique</option>'+
	               '<option class="disabledComplicationSelectOption'+tabDisabledComplicationSelect[6]+'" value=6 >ACOMI</option>'+
	               '<option class="disabledComplicationSelectOption'+tabDisabledComplicationSelect[7]+'" value=7 >AVC</option>'+
	               '<option class="disabledComplicationSelectOption'+tabDisabledComplicationSelect[8]+'" value=8 >IDM</option>'+
	               '<option class="disabledComplicationSelectOption'+tabDisabledComplicationSelect[9]+'" value=9 >Pied diab&egrave;tique</option>'+
	            "</select>";
	
	return liste;
}


function getComplicationDisabledSelect(ligne, id){ 
	
	if(id==0){
		$('#complication_plus').toggle(false);
	}else{
		$('#complication_plus').toggle(true);
		
		var nbLigne = $('.complicationDiagEntree').length; 
		$('#valueChampInputComplication_'+nbLigne).val($('.champInputComplication_'+nbLigne+' select').val());
	}
	
}

function ajouterComplicationDiagEntree(){ 
	
	var nbLigne = $('.complicationDiagEntree').length; 
	var valInputComplicationSelect = $('#valueChampInputComplication_'+nbLigne).val();
	tabDisabledComplicationSelect[valInputComplicationSelect] = 1;
	
	
	
	var interfaceChamps = '<table style="width: 100%;" class="complicationDiagEntree complicationDiagEntree_'+(nbLigne+1)+'">'+
	                        '<tr style="width: 100%" class="designStyleLabel">'+
		        
							   '<td style="width: 40%; padding-right: 25px;" class="complicationDiagEntCol1">'+
							     '<label class="modeleChampInputComplication champInputComplication_'+(nbLigne+1)+'" style="width: 100%; height:30px; text-align:left;">'+
						           '<span style="font-size: 11px;">&#10148; </span><span>Complication '+(nbLigne+1)+' </span>'+ 
						           getModeleComplicationSelect((nbLigne+1))+
						         '</label>'+
						         '<input type="hidden" id="valueChampInputComplication_'+(nbLigne+1)+'" name="valueChampInputComplication_'+(nbLigne+1)+'">'+
							   '</td>'+
							   
							   '<td style="width: 60%; padding-right: 13px;" class="complicationDiagEntCol2">'+
							     '<label style="width: 100%; height:30px; margin-left: -10px; text-align:right;">'+
							       '<span> Note '+(nbLigne+1)+'&nbsp;&nbsp; </span>'+ 
							       '<input type="Text" id="noteInputComplication_'+(nbLigne+1)+'" name="noteInputComplication_'+(nbLigne+1)+'" style="float:right; width: 85%; height: 28px; font-family: time new roman; font-size: 19px; padding-left: 3px; margin-top: 2px;">'+
							     '</label>'+
							   '</td>'+
							   
	                        '</tr>'+
                          '</table>';
	
	
	
	
	
	$('.complicationDiagEntree_'+(nbLigne)).after(interfaceChamps);
	
	if((nbLigne+1) > 1){ 
		$('#complication_moins').toggle(true);
	}else if((nbLigne+1) == 1){
		$('#complication_plus').toggle(false);
	}
	
	if(nbLigne == 0){ $('#complication_moins').toggle(false); }

	$('#complication_plus').toggle(false);
	$('.champInputComplication_'+nbLigne+' select').attr("disabled", true);
	$(".disabledComplicationSelectOption1").toggle(false);
	$(".disabledComplicationSelectOption0").toggle(true);
	
	$('#nbComplicationDiagEntree').val(nbLigne+1);
}


function enleverComplicationDiagEntree(){
	
	var nbLigne = $('.complicationDiagEntree').length; 
	if(nbLigne > 1){
		$('.complicationDiagEntree_'+nbLigne).remove();
		if(nbLigne == 2){ $('#complication_moins').toggle(false); }
		
		$('#complication_plus').toggle(true);
		$('.champInputComplication_'+(nbLigne-1)+' select').attr("disabled", false);
		
		var valInputComplicationSelect = $('#valueChampInputComplication_'+(nbLigne-1)).val();
		tabDisabledComplicationSelect[valInputComplicationSelect] = 0;
		
		$('#nbComplicationDiagEntree').val(nbLigne-1);
	}
	
}
