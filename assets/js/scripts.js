$(function () {
    function dump(o) {
        if (typeof o === 'array') {
            return '[array]<br>' + o.join(', ');
        } else if (typeof o === 'object') {
            res = '';
            for (var i in o) {
                res += i + ": " + dump(o[i]) + "<br>";
            }
            return '[object]<br>' + res;
        } else {
            return o;
        }
    }
    $('.format').click(function() {
        $('.format').removeClass('selected');
        $(this).addClass('selected');
    });
    $('.method').click(function() {
        $('.method').removeClass('selected');
        $(this).addClass('selected');
    });
    function formInit() {
        $('.action').removeClass('selected');
        $('#message').html('').slideUp();
        $('.subform input').attr('disabled', true);
        $('.subform').slideUp();
    }
    formInit();
	
    function updateMessage(res) {
		var format = $('input[name=format]:checked').first().val();
		if (format != 'html') {
			if (format == 'xml') {
				res = xmlToJson(res);			
			}
			$('p.error').remove();
			if (res.error == 1) {
				$('#message')
					.html('<p class="error">' + res.message + '</p>').slideDown();
			} else {
				var tbl = '<p>' + dump(res) + '</p>';
				$('#message').html(tbl).slideDown();
			}
		}
		else {
			$('p.error').remove();
			$('#message').html(res).slideDown();
		}
	}
	
	$('#gettable').click(function (e) {
        formInit();
        $('#gettable').parent().addClass('selected');
        var form = $('#api_form');
        $.ajax({
            type: $('input[name=method]:checked').first().val(),
            url: $(form).attr('action'),
            data: $(form).serialize(),
            dataType: $('input[name=format]:checked').first().val(),
            success: function (res) {
                updateMessage(res)
            }
        });
    });
    $('#get').click(function (e) {
        formInit();
        $('#get').parent().addClass('selected');
        $('.get').slideDown().find('input').removeAttr('disabled');
    });
    $('.get input[type=button]').click(function () {
        var form = $('#api_form');
        $.ajax({
            type: $('input[name=method]:checked').first().val(),
            url: $(form).attr('action'),
            data: $(form).serialize(),
            dataType: $('input[name=format]:checked').first().val(),
            success: function (res) {
                updateMessage(res)
            }
        });
    });
    $('#getbyid').click(function (e) {
        formInit();
        $('#getbyid').parent().addClass('selected');
        $('.getbyid').slideDown().find('input').removeAttr('disabled');
    });
    $('.getbyid input[type=button]').click(function () {
        var form = $('#api_form');
        $.ajax({
            type: $('input[name=method]:checked').first().val(),
            url: $(form).attr('action'),
            data: $(form).serialize(),
            dataType: $('input[name=format]:checked').first().val(),
            success: function (res) {
                updateMessage(res)
            }
        });
    });
    $('#add').click(function (e) {
        formInit();
        $('#add').parent().addClass('selected');
        $('.add').slideDown().find('input').removeAttr('disabled');
    });
    $('.add input[type=button]').click(function () {
        var form = $('#api_form');
        $.ajax({
            type: $('input[name=method]:checked').first().val(),
            url: $(form).attr('action'),
            data: $(form).serialize(),
            dataType: $('input[name=format]:checked').first().val(),
            success: function (res) {
               updateMessage(res)
            }
        });
    });
    $('#delete').click(function (e) {
        formInit();
        $('#delete').parent().addClass('selected');
        $('.delete').slideDown().find('input').removeAttr('disabled');
    });
    $('.delete input[type=button]').click(function () {
        var form = $('#api_form');
        $.ajax({
            type: $('input[name=method]:checked').first().val(),
            url: $(form).attr('action'),
            data: $(form).serialize(),
            dataType: $('input[name=format]:checked').first().val(),
            success: function (res) {
               updateMessage(res)
            }
        });
    });

    $('#update').click(function (e) {
        formInit();
        $('#update').parent().addClass('selected');
        $('.update').slideDown().find('input[name=id]').removeAttr('disabled');
    });
    $('.update input[type=button]').click(function () {
        var form = $('#api_form');
        $.ajax({
            type: $('input[name=method]:checked').first().val(),
            url: $(form).attr('action'),
            data: $(form).serialize(),
            dataType: $('input[name=format]:checked').first().val(),
            success: function (res) {
				updateMessage(res);
            }
        });
    });
    $('.update input[name=id]').change(function (e) {
        var form = $('#api_form'),
            v = $('.update input[name=id]').val(),
            dt = '{ "action": "getbyid", "id": "' + v + '" }',
            jdt = JSON.parse(dt);
         $.ajax({
            type: $('input[name=method]:checked').first().val(),
            url: $(form).attr('action'),
            data: jdt,
            dataType: $('input[name=format]:checked').first().val(),
            success: function (res) {
				var format = $('input[name=format]:checked').first().val();
				if (format == 'xml') {
					res = xmlToJson(res);			
				}
				$('p.error').remove();
				if (res.error == 1) {
					$('#message')
						.html('<p class="error">' + res.message + '</p>').slideDown();
				} else {
					$('#update_nick').val(res.nick).removeAttr('disabled');
					$('#update_email').val(res.email).removeAttr('disabled');
					$('.update .api_submit').removeAttr('disabled');
				}
            }
        });
    });
	// Changes XML to JSON
	function xmlToJson(xml) {
		
		// Create the return object
		var obj = {};

		if (xml.nodeType == 1) { // element
			// do attributes
			if (xml.attributes.length > 0) {
			obj["@attributes"] = {};
				for (var j = 0; j < xml.attributes.length; j++) {
					var attribute = xml.attributes.item(j);
					obj["@attributes"][attribute.nodeName] = attribute.nodeValue;
				}
			}
		} else if (xml.nodeType == 3) { // text
			obj = xml.nodeValue;
		}

		// do children
		if (xml.hasChildNodes()) {
			for(var i = 0; i < xml.childNodes.length; i++) {
				var item = xml.childNodes.item(i);
				var nodeName = item.nodeName;
				if (typeof(obj[nodeName]) == "undefined") {
					obj[nodeName] = xmlToJson(item);
				} else {
					if (typeof(obj[nodeName].push) == "undefined") {
						var old = obj[nodeName];
						obj[nodeName] = [];
						obj[nodeName].push(old);
					}
					obj[nodeName].push(xmlToJson(item));
				}
			}
		}
		return obj;
	};

});