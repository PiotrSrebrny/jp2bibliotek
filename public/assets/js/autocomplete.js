function lookUp(input_element, type) 
{
	$.get("/dbquery/" + type + "/"+ input_element.value, function(data, status) {
		cache: false
		if (status == "success") {
      $('#ac_div').html(data);
			$("#"  + input_element.id).autocomplete({ source:auto	})
		}
  });
}

function getLastTag()
{
	$.get("/dbquery/last_tag/" + $('#form_type').val(), function(data, status) {
		cache: false
		if (status == "success") {
			$('#form_last_tag').val(data)
		}
	})
}
