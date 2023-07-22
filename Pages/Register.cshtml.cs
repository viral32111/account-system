using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.Extensions.Logging;

namespace AccountSystem.Pages;

public class RegisterModel : PageModel {
	private readonly ILogger<IndexModel> logger;

	public RegisterModel( ILogger<IndexModel> _logger ) => ( logger ) = ( _logger );

	public void OnGet() {
		logger.LogInformation( "Register page requested" );
	}

	/*
	public void OnPost() {
		logger.LogInformation( "Register page posted" );

		string? emailAddress = Request.Form[ "emailAddress" ];
		string? password = Request.Form[ "password" ];
		logger.LogInformation( $"Email address: '{ emailAddress }'" );
		logger.LogInformation( $"Password: '{ password }'" );
	}
	*/
}
