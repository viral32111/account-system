const shouldEnableRegisterButton = () => {
	const emailAddress = $( "#emailAddressInput" ).val()
	const password = $( "#passwordInput" ).val()

	const emailAddressMinimumLength = $( "#emailAddressInput" ).attr( "minlength" )
	const passwordMinimumLength = $( "#passwordInput" ).attr( "minlength" )

	$( "#registerButton" ).prop( "disabled", emailAddress.length < emailAddressMinimumLength || password.length < passwordMinimumLength )
}

/*
$( "#emailAddressInput" ).on( "input", shouldEnableRegisterButton )
$( "#passwordInput" ).on( "input", shouldEnableRegisterButton )

$( () => {
	shouldEnableRegisterButton()
} )
*/

/*
$( "#registerForm" ).submit( ( event ) => {
	event.preventDefault()

	const emailAddress = $( "#emailAddressInput" ).val()
	const password = $( "#passwordInput" ).val()

	$.ajax( {
		type: "POST",
		url: "/api/register",
		data: JSON.stringify( {
			emailAddress: emailAddress,
			password: password
		} ),
		contentType: "application/json",
		success: ( response ) => {
			$( "#registerForm" ).hide()
			$( "#registerSuccessMessage" ).show()
		},
		error: ( response ) => {
			$( "#registerForm" ).hide()
			$( "#registerErrorMessage" ).show()
		}
	} )

} )
*/