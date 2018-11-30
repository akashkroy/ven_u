function createDatetime(date, time){
	return new Date(date+' '+time);
}

function datetimeGreaterThan(datetime1, datetime2){
	
	return datetime1 > datetime2;
}

function datetimeInRange(datetime1, datetime2, datetime){

	return datetime >= datetime1 && datetime <= datetime2;
}