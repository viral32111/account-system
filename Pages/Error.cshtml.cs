using System.Diagnostics;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.Extensions.Logging;

namespace AccountSystem.Pages;

[ IgnoreAntiforgeryToken ]
[ ResponseCache( Duration = 0, Location = ResponseCacheLocation.None, NoStore = true ) ]
public class ErrorModel : PageModel {
	public string? RequestId { get; set; }

	public bool ShowRequestId => !string.IsNullOrEmpty(RequestId);

	private readonly ILogger<ErrorModel> logger;

	public ErrorModel( ILogger<ErrorModel> _logger ) => ( logger ) = ( _logger );

	public void OnGet() {
		RequestId = Activity.Current?.Id ?? HttpContext.TraceIdentifier;
		logger.LogError( "Error page requested" );
	}
}
