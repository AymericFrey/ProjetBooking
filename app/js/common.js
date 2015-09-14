// JS commun

var App = App || {}; //namespace commun du projet


/**
* FORM
* namespace : Booking.Form
* Permet de gérer le contrôle des input selon la méthode choisie et ainsi de prévenir un mauvais submit
*
* Mise en oeuvre :
* 1 - Déclarer data-form-control dans l'input et lui passer une valeur contenue dans {relations} (ex: data-form-control='date' )
* 2 - Déclarer le formulaire comme à vérifier : $("#id-formulaire").submit(App.Form.check);
**/

App.Form = (function(){
	var self = {};
	var relations = {'name': checkName, 'email': checkEmail, 'date': checkDate, 'time': checkTime, 
					 'box': checkBox, 'zip': checkZip, 'phone': checkPhone };
	//pré-compile les regex pour de meilleures performances
	var regex = {
		'name' : /[a-zA-z éèêëàâä'-]+/i, 
		'email': /^[a-z0-9_.-]{3,}@[a-z0-9._-]+\.[a-z]{2,4}/, 
		'date' : /^[1-2]{1}[09]{1}[0-9]{2}[/\\-]{1}[0-1]{1}[0-9]{1}[/\\-]{1}[0-3]{1}[0-9]{1}$/, 
		'date2': /^[0-3]{1}[0-9]{1}[\\/-]{1}[0-1]{1}[0-9]{1}[\\/-][1-2]{1}[09]{1}[0-9]{2}$/, 
		'time' : /^[012]{1}[0-9]{1}\:[0-5]{1}[0-9]{1}$/,
		'zip'  : /^[0-9]{5}$/,
		'phone': /^[0]{1}[0-79]{1}[0-9]{8}$/
	};
	
	//vérifications
	function checkEmail(data){
		if(regex.email.test(data))
			return 0;
		return 1;
	}
	function checkDate(data){
		if(regex.date.test(data) || regex.date2.test(data))
			return 0;
		return 1;
	}
	function checkTime(data){
		if(regex.time.test(data))
			return 0;
		return 1;
	}
	function checkBox(data){
		if(data !== undefined && data == 'on')
			return 0;
		return 1;
	}
	function checkName(data){
		if(regex.name.test(data))
			return 0;
		return 1;
	}
	function checkZip(data){
		if(regex.zip.test(data))
			return 0;
		return 1;
	}
	function checkPhone(data){
		if(regex.phone.test(data))
			return 0;
		return 1;
	}
	
	//affichage erreur
	function addError(item){
		item.style.border = "1px solid red";
		item.style.background = "#FCF0F0";
	}
	
	//publiques
	self.check = function(){
		var submit = 0;
		
		var temp, data;
		$.each($(this).find(':input'), function(i, field){
			$(field).css({'border': "", 'background': ''});
			
			data = $(field).attr("data-form-control");
			if(data !== undefined){
				for(var a in relations) {
					if(data == a){
						temp = relations[a](field.value);
						if(temp > 0)
							addError(field);
							
						submit += temp;
						break;
					}
				}
			}
		});
		
		console.log(submit);
		if(submit == 0)
			return true;
		return false;
	};
	
	
	return self;
})($);


/**
* Dialog
* namespace : Booking.Dialog
* Permet d'afficher une fenêtre utilisateur avec différentes options
**/

App.Dialog = (function(){
	var self = {};
	var parent;
	var dialog;
	var title;
	var content;
	var buttons;
	var buttonsValues = { ok: "true", oui: "true", non: "false", annuler: "false" };
	var buttonsContainer;
	var backgroundShadow;
	
	var registeredCallback;

	function closeDialog(){
		backgroundShadow.style.display = "none";
		dialog.style.display = "none";
	}
	
	function buttonClick(){
		var result = false;
		if($(this).attr('data-dialog-value') === "true")
			result = true;
		
		registeredCallback(result);
		closeDialog();
	}
	
	//public
	self.init = function(){
		if(parent === undefined)
			 parent = document.getElementsByTagName('body')[0];
			 
		if(backgroundShadow === undefined){
			backgroundShadow = document.createElement('div');
			backgroundShadow.className = "window-shadow";
			backgroundShadow.style.display = "none";
			backgroundShadow.style.height = $(document).height();
			
			$(backgroundShadow).click(closeDialog);
			
			parent.appendChild(backgroundShadow);
		}
		
		if(dialog === undefined){
			dialog = document.createElement('div');
			dialog.className = "dialog-content";
			dialog.style.display = "none";
			
			title = document.createElement('h5');
			content = document.createElement('p');
			buttonsContainer = document.createElement("div");
			buttonsContainer.style.margin = "auto";
			buttonsContainer.style.textAlign = "center";
			buttonsContainer.style.width = "400px";
			
			dialog.appendChild(title);
			dialog.appendChild(content);
			dialog.appendChild(buttonsContainer);
			dialog.style.zIndex = 9999;
			
			dialog.style.width = '400px';
			dialog.style.height = '250px';
			dialog.style.top = (window.innerHeight - parseInt(dialog.style.height)) /2 + "px";
			dialog.style.left = (window.innerWidth - parseInt(dialog.style.width)) /2 + "px";
			
			parent.appendChild(dialog);
		}
		
		if(buttons === undefined){
			buttons = { ok: undefined, oui: undefined, non: undefined, annuler: undefined }; //changer ici pour l'ordre d'affichage
			
			for(var button in buttons){
				buttons[button] = document.createElement('button');
				buttons[button].setAttribute("data-dialog-value", buttonsValues[button]);
				buttons[button].className = "custom-button custom-button-orange";
				buttons[button].style.display = "none";
				$(buttons[button]).click(buttonClick);
				buttons[button].innerHTML = button;
				
				buttonsContainer.appendChild(buttons[button]);
			}
		}
		else {
			for(var button in buttons){
				buttons[button].style.display = "none";
			}
		}
	}
	
	self.add = function(titre, contenu, types, callback){ // confirm (ok, annuler), choice (oui/non), alert
		self.init();
		
		backgroundShadow.style.display = "";
		
		title.innerHTML = titre;
		content.innerHTML = contenu;
	
		for(var i = 0; i < types.length; i++){
			buttons[types[i]].style.display = '';
		}
		
		dialog.style.display = "";
		
		registeredCallback = callback;
	};
	
	
	return self;
})($);