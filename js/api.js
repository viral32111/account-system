function signIn(email,password){
	$.ajax({type:"CONNECT",url:"api/users.php",data:{email:email,password:password},
		success:function(data,status,xhr){
			console.log(data);
		},error:function(xhr,status,err){
			console.error(xhr);
		}
	});
}

function signUp(email,password){
	$.ajax({type:"POST",url:"api/users.php",data:{email:email,password:password},
		success:function(data,status,xhr){
			console.log(JSON.parse(data));
		},error:function(xhr,status,err){
			let data={};
			try{
				data=JSON.parse(xhr.responseText);
			}catch(err){
				alert(err+": "+xhr.responseText);
			}
			if(data.message==="A user with that email address already exists."){
				signIn(email,password);
			}else{
				alert(xhr.responseText);
			}
		}
	});
}