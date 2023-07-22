using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.Extensions.Logging;

namespace AccountSystem.Pages;

public class IndexModel : PageModel {
	private readonly ILogger<IndexModel> logger;

	public IndexModel( ILogger<IndexModel> _logger ) => ( logger ) = ( _logger );

	public void OnGet() {
		logger.LogInformation( "Index page requested" );
	}
}
