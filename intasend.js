new window.IntaSend({
    // Replace with your Publishable Key
    publicAPIKey: "ISPubKey_live_59358cd0-88f0-4ea5-b5a1-691dc78a34e8",
    live: true //set to true when going live
  })
  .on("COMPLETE", (results) => console.log("Do something on success", results))
  .on("FAILED", (results) => console.log("Do something on failure", results))
  .on("IN-PROGRESS", (results) => console.log("Payment in progress status", results))