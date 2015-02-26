﻿ $(document).ready(function(){
	$("#send").click(function(){
	  var name   = $("#name").val();
	  var email  = $("#email").val();
	  var message  = $("#message").val();

	  var error = false;

		if(name.length == 0){
			var error = true;
			$("#error_name").fadeIn(500);
		}else{
			$("#error_name").fadeOut(500);
		}
		if(email.length == 0 || email.indexOf("@") == "-1" || email.indexOf(".") == "-1"){
		  var error = true;
		  $("#error_email").fadeIn(500);
		}else{
		  $("#error_email").fadeOut(500);
		}
		if(message.length == 0){
			var error = true;
			$("#error_message").fadeIn(500);
		}else{
			$("#error_message").fadeOut(500);
		}
		
		if(error == false){
		  $("#send").attr({"disabled" : "true", "value" : "Отправка..." });
			
		  $.ajax({
			 type: "POST",
			 url : $("form#contact").attr('action'),
		    data: "name=" + name + "&email=" + email + "&subject=" + "You Got Email" + "&message=" + message,
			 success: function(data){    
			  if(data == 'success'){
				 $("#btnsubmit").remove();
				 $("#mail_success").fadeIn(500);
			  }else{
				 $("#mail_failed").fadeIn(500);
				 $("#send").removeAttr("disabled").attr("value", "Отправить еще");
			  }     
			 },
		 error: function(data){
			$("#mail_failed").html(data).fadeIn(500);
				 $("#send").removeAttr("disabled").attr("value", "Не отправлено");
		 }
		  });  
	  }
		 return false;                      
	});    

});
