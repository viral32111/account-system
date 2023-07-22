using System;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Hosting;
using Microsoft.AspNetCore.Http;
using System.Net;

namespace AccountSystem;

public class Program {
	public static async Task Main( string[] arguments ) {
		WebApplicationBuilder builder = WebApplication.CreateBuilder( arguments );
		builder.Logging.ClearProviders();
		builder.Logging.AddSimpleConsole( options => {
			options.IncludeScopes = false;
			options.SingleLine = true;
			options.TimestampFormat = "[HH:mm:ss] ";
		} );

		builder.Services.AddRazorPages(); // Add services to the container

		WebApplication app = builder.Build();

		// Configure the HTTP request pipeline
		if ( !app.Environment.IsDevelopment() ) {
			app.UseExceptionHandler( "/Error ");
			//app.UseHsts(); // The default HSTS value is 30 days. You may want to change this for production scenarios, see https://aka.ms/aspnetcore-hsts.
		}

		//app.UseHttpsRedirection();
		app.UseStaticFiles();
		app.UseRouting();
		app.UseAuthorization();
		app.UseStatusCodePages();

		app.MapRazorPages();
		app.MapControllerRoute( name: "default", pattern: "api/{controller=Home}/{action=Index}/{id?}" );
	
		app.UseStatusCodePages( async ( context ) => {
			HttpRequest request = context.HttpContext.Request;
			HttpResponse response = context.HttpContext.Response;

			if ( request.Path.StartsWithSegments( "/api" ) ) {
				response.ContentType = "application/json";
				await response.WriteAsync( "{}" );
			}
		} );

		await app.RunAsync();
	}
}
