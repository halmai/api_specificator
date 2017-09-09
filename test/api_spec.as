swagger: "2.0"
info:
	version: "1.0.0"
	title: "API Specificator Sample"
paths: 
	/auth/account:
		post: 
			summary: Create a new user
			description: 
				If the given email address doesn't exist in the database yet then it creates the new user.
			parameters:
			- in: "body"
				name: "body"
				description: "Pet object that needs to be added to the store"
				required: true
				schemaTemplate:
					{
						age: 43.5 // asdasdasd
						iq: 100
						is_male: true
						*email:  "johndoe@gmail.com"  // email address of the user
						*password: "abc123"
						*displayed_name: "Johny D. Good"
					}
			responses:
				200:
					description: Registration was successful
					schemaTemplate:
						{
							*result: "ERROR"
							error_code: "ERROR__DATE_OUT_OF_INTERVAL"
						}
					