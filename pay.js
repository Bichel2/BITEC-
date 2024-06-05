const IntaSend = require('intasend-node');

let intasend = new IntaSend(
    '<ISPubKey_live_59358cd0-88f0-4ea5-b5a1-691dc78a34e8>',
    '<ISSecretKey_live_4d2a59e9-9df9-44c1-ab57-6a08402b8b14>',
    true, // Test ? Set true for test environment
);

let collection = intasend.collection();
collection
   .mpesaStkPush({
  		first_name: 'Joe',
    	last_name: 'Doe',
    	
    	host:'https://bichel2.github.io/Bitec-/',
  		amount: 10,
    	phone_number: '254722000000',
    	api_ref: 'test',
  })
  .then((resp) => {
  	// Redirect user to URL to complete payment
  	 console.log(`STK Push Resp:`,resp);
	})
  .catch((err) => {
     console.error(`STK Push Resp error:`,err);
  });