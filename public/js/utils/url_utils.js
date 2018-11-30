function constructUrlWithParams(base_url, params_object){
	
	var params = $.param(params_object);

	var url = base_url += '?' + params;

	return url;
}