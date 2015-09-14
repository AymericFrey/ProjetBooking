//js de la page booking
App.Booking = {}; //namespace

/**
* CALENDAR
* namespace : Booking.Calendar
* Permet de gérer l'interactivité avec l'utilisateur sur le calendrier
**/

App.Booking.Calendar = {}; //namespace

App.Booking.Calendar.Data = (function(){
	var self = {};
	var urlAjax = "js/ajax/selection-calendar.php";
	self.idDoctor = 0;
	
	//re-dispatch l'event aux écouteurs nécessaires
	function dispatcher(item){
		if(!$(item.target).hasClass("calendar-block")){
			App.Booking.Calendar.Display.clicked(item);
		}
	}
	
	self.retrieveDatas = function(datas){
		App.Booking.Calendar.Display.UIContent.html("<div id='selection-ajax-loader'><img src='img/ajax-loader.gif' alt='loading...' /></div>");
		$.ajax({
		  url: urlAjax,
		  data: datas,
		  success: function(resultat){
			App.Booking.Calendar.Display.UIContent.html(resultat);
			App.Booking.Calendar.Action.listenSelection();
		  }
		});
	};
	
	self.setDoctorId = function(id){
		self.idDoctor = id;
	};
	
	return self;
})($);

//
App.Booking.Calendar.Action = (function(){
	var self = {};
	var fieldsForm = {};
	var selectionClicked;
	
	//re-dispatch l'event aux écouteurs nécessaires
	function dispatcher(item){
		if($(item.target).hasClass("calendar-hour") && !$(item.target).hasClass("calendar-block")){
			App.Booking.Calendar.Display.clicked(item);
		}
		else if($(item.target).hasClass("selection-part-content")){
			if(selectionClicked !== undefined)
				selectionClicked.css({'background': ""});
				
			fieldsForm.date.val($(item.target).attr('data-calendar-selection-date'));
			fieldsForm.time.val($(item.target).prev().html());
			
			$(item.target).css({'background': "#D0E2F2"});
			selectionClicked = $(item.target);
		}
	}
	
	self.init = function(){
		$('.calendar-hour').click(dispatcher);
	};
	
	self.registerForm = function(fields){
		fieldsForm.date = $("#" + fields.date);
		fieldsForm.time = $("#" + fields.time);
	};
	
	self.listenSelection = function(){
		$('.selection-part-content').each(function(){
			if($(this).html() == "")
				$(this).click(dispatcher);
		});
	};
	
	return self;
})($);


//
App.Booking.Calendar.Display = (function(){
	var self = {};
	var UI;
	var closeBtn;
	var memorized = {}; //item précédent
	
	function closeButton(){
		UI.css({"display": "none" });
		memorized.item.currentTarget.style.background = "";
	}
	
	self.clicked = function(item){
		UI.css({ "z-index": '9999', "display": 'block', 'top': item.currentTarget.offsetTop + item.currentTarget.clientHeight});
		item.currentTarget.style.background = "grey";
		if(memorized.item !== undefined)
			memorized.item.currentTarget.style.background = "";
		
		//si c'est le même item de cliqué on masque le menu de selection
		if(memorized.item !== undefined && memorized.item.currentTarget.getAttribute('data-calendar-day') == item.currentTarget.getAttribute('data-calendar-day')){
			closeButton();
			memorized.item = undefined;
		}
		else {
			memorized.item = item;
			
			App.Booking.Calendar.Data.retrieveDatas({
				'doctor': App.Booking.Calendar.Data.idDoctor, 
				'dayStartHour':  item.currentTarget.parentElement.getAttribute('data-calendar-startHour'), 
				'dayEndHour':  item.currentTarget.parentElement.getAttribute('data-calendar-endHour'), 
				'dateStart': item.currentTarget.getAttribute('data-calendar-day'), 
				'hourStart': item.currentTarget.getAttribute('data-calendar-hour')
			});
			
		}
	};
	
	self.registerUI = function(idItem){
		UI = $("#"+idItem);
		closeBtn = $('#close-btn');
		closeBtn.click(closeButton);
		
		self.UIContent = $("#selection-hours");
	};
	
	return self;
})($);