function saveLocation() {
	var params = {};
	params.ID = $('input[name="id"]').val();
	params.CountryID = $('select[name="CountryID"]').val();
	params.RegionID = $('select[name="RegionID"]').val();
	params.CityID = $('select[name="CityID"]').val();
	params.Name = $('input[name="Name"]').val();
	params.seoTitle = $('textarea[name="seoTitle"]').val();
	params.seoDescription = $('textarea[name="seoDescription"]').val();
	params.seoKeywords = $('textarea[name="seoKeywords"]').val();
	params.active = $('input[name="active"]').is(':checked') ? 1 : 0;
	
	//Обработчик нажатия на кнопку Сохранить
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-admin/location/save',
		data: params,
		success: function(data, textStatus){processSaveLocation(data);},
		dataType: 'json'
	});
} 

//Получие даных о регионах
function reloadRegions(country) {
	$('#RegionID').children().remove();
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-admin/location/reload-regions',
		data: {id:country},
		success: function(data, textStatus){showRegions(data);},
		dataType: 'json'
	});
}

//Вывод даных о регионах
function showRegions(data) {
	for (i in data.regions) $('#RegionID').append($('<option />').val(i).html(data.regions[i])); 
}

//Получие даных о городах
function reloadCities(region) {
	$('#CityID').children().remove();
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-admin/location/reload-cities',
		data: {id:region},
		success: function(data, textStatus){showCities(data);},
		dataType: 'json'
	});
}

//Вывод даных о городах
function showCities(data) {
	for (i in data.cities) $('#CityID').append($('<option />').val(i).html(data.cities[i])); 
}

//Обработчик удачной добавки в базу
function processSaveLocation(data) {
	if (data.error != 0) {
		alert(data.errorStr);
		return;
	}
	window.location.href = data.redirect;
}