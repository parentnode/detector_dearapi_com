Page.Calendar = function(element){

	this.calendar = element;
	this.calendar_type = Page.Util.getIJ("calendar", element);
	this.server_update_timer = false;
	
	// appointments
	this.appointments = new Array();
	// references to date cells
	this.dates = new Array();
	// change indicator to avoid conflict of interest
	this.changingMonth = false;

	// todays date
	this.date = this.calendar.title.split("-")[0];
	this.month = this.calendar.title.split("-")[1];
	this.year = this.calendar.title.split("-")[2];
	this.calendar.title = "";

	this.rows = this.calendar.getElementsByTagName("tr");

	// get info/submit url
	this.url = (this.rows[0].title.match(/linkto:/g) ? this.rows[0].title.split(":")[1] : false);
	this.rows[0].title = "";
	
	// initiate header
	Page.Util.unSelectify(this.rows[0]);
	var headers = this.rows[0].getElementsByTagName("th");
	for(var header, i = 0; header = headers[i]; i++){
		if(header.className.match(/prev|next/g)){
			header.className += (header.className ? " clickable" : "clickable");
			header.calendar = this.calendar;
			header.onmouseover = function(){
				this.calendar.calendar.over(this);
			}
			header.onmouseout = function(){
				this.calendar.calendar.out(this);
			}
			header.onclick = function(){
				this.calendar.calendar.changeMonth(this);
			}
		}else{
			this.headline = header;
			// get current view
			var view = (this.headline.firstChild ? this.headline.firstChild.nodeValue.replace(/ /g,"").split("-") : [this.month,this.year]);
			this.month_view = view[0];
			this.year_view = view[1];
		}
	}

	// index calendar grid;
	for(var row, i = 1; row = this.rows[i]; i++){
		row.calendar = this.calendar;
		var cells = row.getElementsByTagName("td");
		for(var cell, u = 0; cell =  cells[u]; u++){
			cell.row = row;
			// only index cells with dates
			if(cell.firstChild != null && parseInt(cell.firstChild.nodeValue)){
				this.dates[parseInt(cell.firstChild.nodeValue)] = cell;
			}
		}
	}

	// activate calender grid
	this.activateGrid = function(){
		for(var date = 1, cell; cell = this.dates[date]; date++){
			cell.className += cell.className ? " clickable" : "clickable";
			cell.onclick = function(){
				this.row.calendar.calendar.selectDate(this);
			}
			cell.onmouseover = function(){
				this.row.calendar.calendar.over(this);
			}
			cell.onmouseout = function(){
				this.row.calendar.calendar.out(this);
			}
		}
		if(this.year == this.year_view && this.month == this.month_view){
			this.dates[this.date].className += this.dates[this.date].className ? " today" : "today";
		}
	}

	this.activateGrid();

	this.over = function(element){
		element.className += element.className ? " over" : "over";
	}
	this.out = function(element){
		element.className = element.className.replace(/ over|over/g, "");
	}

	// creates two dimensional array containing structure of month
	this.getMonthMatrix = function(){
		var new_month = new Date(this.year_view, (this.month_view-1));
		var month_matrix = new Array();
		for(var date = 1, week = 1; (new Date(new_month.setDate(date)).getMonth()+1) == this.month_view; date++){
				var day_of_week = new_month.getDay();
				// correct sunday as first day
				day_of_week = (day_of_week == 0 ? 6 : day_of_week - 1);
				month_matrix[week] = (!month_matrix[week] ? new Array() : month_matrix[week]);
				month_matrix[week][day_of_week] = date;
				if(day_of_week == 6){
					week++;
				}
		}
		return month_matrix;
	}

	// change month
	this.changeMonth = function(direction){
		
		// experimental
		while(this.changingMonth){ /* wait */}
		
		// change begun
		this.changingMonth = true;

		// change month
		if(direction.className.match(/prev/g)){
			if(this.month_view > 1){
				this.month_view--;
			}else{
				this.month_view = 12;
				this.year_view--;
			}
		}else{
			if(this.month_view < 12){
				this.month_view++;
			}else{
				this.month_view = 1;
				this.year_view++;
			}
		}
		this.headline.innerHTML = (this.month_view < 10 ? "0"+this.month_view : this.month_view) + " - " + this.year_view;

		// get month grid
		var this_month = this.getMonthMatrix();
		// reset dates
		this.dates = new Array();

		// update calender
		for(var row, week = 1; row = this.rows[week]; week++){
			var cells = row.getElementsByTagName("td");
			for(var cell, date, u = 0; cell = cells[u]; u++){
				date = this_month[week] && this_month[week][u] ? this_month[week][u] : 0;
				this.dates[date] = date ? cell : false;
				cell.innerHTML = date ? date : "&nbsp;";
				cell.className = cell.className.replace(/ clickable|clickable| deadline|deadline| today|today/g, "");
				cell.onclick = null;
				cell.onmouseover = null;
				cell.onmouseout = null;
			}
		}
		// activate grid
		this.activateGrid();
		// change done
		this.changingMonth = false;

		// reset existing timer 
		/*
		if(this.server_update_timer !== false){
			Page.sTo.resetTimer(this.server_update_timer);
		}
		// if appointments are not stored
		if(!this.appointments[this.year_view] || !this.appointments[this.year_view][this.month_view]){
			// set timer for getting appointments
			this.server_update_timer = Page.sTo.setTimer(this,this.getAppointments, 2000);
		}else{
			this.insertAppointments(this.year_view, this.month_view);
		}
		*/
	}
	
	// make server request for appointments
	this.getAppointments = function(){
		this.server_update_timer = false;
		this.headline.innerHTML = "Loading...";
		Page.Ajax.send("../xml/calendar.xml", this.storeAppointments, this, "year="+this.year_view+"&month"+this.month_view);
		//Page.Ajax.send(this.url, this.storeAppointments, this, "year="+this.year_view+"&month"+this.month_view);
	}

	// stores appointments return with ajax
	this.storeAppointments = function(response){
		// set headline (from loading message)
		this.headline.innerHTML = (this.month_view < 10 ? "0"+this.month_view : this.month_view) + " - " + this.year_view;

		// did we get a useful response?
		if(response && typeof(response.result) == "object" && response.result.getElementsByTagName("calendar").length){

			var this_calendar = response.result.getElementsByTagName("calendar")[0];
			var month = this_calendar.getAttribute("month");
			var year = this_calendar.getAttribute("year");

			// check for appointments[year] array existance
			this.appointments[year] = this.appointments[year] ? this.appointments[year] : new Array();
			this.appointments[year][month] = new Array();
			// create appointments objects
			var appointments = this_calendar.getElementsByTagName("appointment");
			for(var appointment, i = 0; appointment = appointments[i]; i++){
				this.appointments[year][month][i] = new this.Appointment(appointment);
			}

			// only insert appointments if view is matching response
			if(year == this.year_view && month == this.month_view){
				this.insertAppointments(year, month);
			}
		}else{
			Page.Util.addMessageBoard("Could not contact server!", "error");
		}
		
	}

	// insert appointments into calendar grid
	this.insertAppointments = function(year, month){
		for(var appointment, cells, cell, i = 0; (appointment = this.appointments[year][month][i]) && !this.changingMonth; i++){
			if(appointment.date < this.dates.length){
				this.dates[appointment.date].className += appointment.deadline ? " deadline" : "";
				var text = document.createElement("span");
				text.innerHTML = appointment.value;
				this.dates[appointment.date].appendChild(text);
			}
		}
	}
	

	/*
	* DONT KNOW WHAT TO DO ABOUT THIS YET
	*/
	this.selectDate = function(element){
		var new_date = element.firstChild.nodeValue;
		// use AJAX to submit (so far we just show off)
		Page.Util.debug("selected date:" + new_date+"-"+this.month_view+"-"+this.year_view + "#" + this.calendar_type);
	}



	// appointment object (extracting values from xml element)
	this.Appointment = function(object){
		this.date = object.getAttribute("date");
		this.time = object.getAttribute("time");
		this.value = object.firstChild.nodeValue;
		this.deadline = object.getAttribute("deadline");
		return this;
	}
	
}

