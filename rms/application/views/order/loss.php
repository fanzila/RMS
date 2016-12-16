</div>
<div data-role="content">
	<? if($keylogin) { ?>
		<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
			<li>
				<form id="userform">												
					<select style="background-color:#a1ff7c" name="user" id="user" data-inline="true" data-theme="a" required>
						<option value="">User</option>
						<? foreach ($users as $user) { ?>
							<option value="<?=$user->id?>"><?=$user->first_name?> <?=$user->last_name?></option>
							<? } ?>
						</select>
					</form>
				</li>
			</ul>
			<? } ?>
			<ul id="autocomplete_loss" data-role="listview" data-inset="true" data-filter="true" data-filter-placeholder="Find a product..." data-filter-theme="d"></ul>

		</div>
	</div>
	<script>

	function updateStock(i) {

		var user = null;
		<? if($keylogin) { ?>var user = $("#user").val();<? } ?>
		var stock = $('#upstock-' + i).val();
		var type = $('#type-' + i).val();
		var $form = $('#upform-' + i);
		
		<? if($keylogin) { ?>
		if (user == "") {
			alert("User must be filled out");
			return false;
		}
		<? } ?>
		
		var content = {
			id: i,
			value: stock,
			user: user,
			type: type
		};

		$.ajax({
			url: $form.attr('action'),
			type: 'post',
			data: content,
			dataType: 'json',
			success: function(json) {
				if(json.reponse == 'ok') {
					alert('Saved!');
					return false;
				} else {
					alert('WARNING! ERROR at saving jsp: '+ json.reponse);
				}
			}
		}).done(function(data) {
			return false;
		}).fail(function(data) {
			alert('WARNING! ERROR at saving!');
		});

		return false;			
	}

	$( document ).on( "pageinit", "#pageid", function() {
		$( "#autocomplete_loss" ).on( "listviewbeforefilter", function ( e, data ) {
			var $ul = $( this ),
			$input = $( data.input ),
			value = $input.val(),
			html = "<table width='100%' border='0' cellspacing='0' cellpadding='6'>";
			$ul.html( "" );
			if ( value && value.length > 1 ) {
				$ul.html( "<li><div class='ui-loader'><span class='ui-icon ui-icon-loading'></span></div></li>" );
				$ul.listview( "refresh" );
				$.ajax({
					url: "/order/autoCompLoss",
					dataType: "jsonp",
					crossDomain: true,
					data: {
						q: $input.val()
					}
				})

				.then( function ( response ) {
					$.each( response, function ( i, val ) {
						var res = val.split("|||");
						
						bgcolor = '#f7f7ec';
						if(res[7] == 'ARTICLE') {
							bgcolor = '#eef6ee';	
						}
						
						html += "<form data-ajax='false' id='upform-" + res[1] + "' action='/order/saveloss/' method='post'><tr bgcolor='" + bgcolor + "'><td style='border: 1px solid #e8e8e8;'>" + res[7] + "</td><td style='border: 1px solid #e8e8e8;'>" + res[0] + " (" + res[2] + ")</td><td style='border: 1px solid #e8e8e8;'> QTTY: " + res[3] + " </td><td style='border: 1px solid #e8e8e8;'> Unite: " + res[5] + " (Colisage : " + res[6] + ")</td><td style='border: 1px solid #e8e8e8;'>Loss: <input type='text' id='upstock-" + res[1] + "' name='stock[" + res[1] + "]' value=''> <input type='submit' id='upclick-" + res[1] + "' name='ok' style='border: 1px ridge #e8e8e8;' value='UPDATE' onclick='updateStock(" + res[1] + ");'></td></tr><input type='hidden' id='type-" + res[1] + "' name='type[" + res[1] + "]' value='" + res[7] + "'></form>";
					});
					html += "</table>";
					$ul.html( html );
					$ul.listview( "refresh" );
					$ul.trigger( "updatelayout");
				})
			}
		});
	})
	</script>