using System.ComponentModel.DataAnnotations;
using Microsoft.AspNetCore.Mvc;

namespace AccountSystem.Models;

public class AccountCreateModel {

	[ Required ]
	[ DataType( DataType.EmailAddress ) ]
	//[ BindProperty( Name = "emailAddress" ) ]
	public string EmailAddress { get; set; } = string.Empty;

	[ Required ]
	[ DataType( DataType.Password ) ]
	//[ BindProperty( Name = "password" ) ]
	public string Password { get; set; } = string.Empty;

}
