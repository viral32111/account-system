using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Logging;
using System.Text.Encodings.Web;
using AccountSystem.Models;

namespace AccountSystem.Controllers;

public class AccountController : Controller {

	private readonly ILogger<AccountController> logger;

	public AccountController( ILogger<AccountController> _logger ) => ( logger ) = ( _logger );

	[ HttpGet ]
	[ Route( "api/account" ) ]
	public ActionResult Index() {
		Response.StatusCode = 400;
		return Json( new {} );
	}

	[ HttpPost ]
	[ Route( "api/account/create" ) ]
	public ActionResult Create( AccountCreateModel model ) {
		logger.LogInformation( "Account create requested" );

		if ( !ModelState.IsValid ) {
			logger.LogWarning( "Account create model invalid" );

			Response.StatusCode = 400;
			TempData[ "Message" ] = "Missing required form properties.";
			return RedirectToPage( "/register" );
		}

		logger.LogInformation( $"Email address: '{ model.EmailAddress }'" );
		logger.LogInformation( $"Password: '{ model.Password }'" );

		// TODO: Insert into MS SQL Server database

		TempData[ "Message" ] = "Account created successfully.";
		return RedirectToPage( "/register" );
	}

	[ HttpPost ]
	[ Route( "api/account/authenticate" ) ]
	public ActionResult Authenticate() {
		Response.StatusCode = 501;
		return Json( new {} );
	}

}
