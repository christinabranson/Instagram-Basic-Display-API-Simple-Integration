# Instagram Basic Display API Simple Integration

A simple PHP implementation of the [Instagram Basic Display API](https://developers.facebook.com/docs/instagram-basic-display-api)

This implementation does the following:

1. index.php provides a link to authorize the implementation
2. If authorizes, we'll be taken back to /callback.php with an attached Oauth code
3. We then exchange the code, with a short-lived access token, then in the same step, we exchange that short-lived access token with a long-lived access token
4. Once we have our access tokens, we write a fake "user" to a very fake "database"
5. We redirect to view.php, where we look for new posts then display them

## File Structure


| File        | Description     |
| :------------- |:-------------|
| callback.php      | Part of Step 2, this function receieves the authorization code and starts the process of getting a long-lived token we can use to fetch the fun stuff|
| config.php      | This is where your APP ID, APP Secret, and Callback URLS are defined. See config.php.sample for example |
| config.php.sample      |  ^ |
| fake_database.txt      |  We're literally just writing JSON to a file. Like I said, it's a *very* simple integration  |
| http.php      |  Utility class to handle curl GET and POST requests. I re-use this class a lot  |
| index.php      |  Part of Step 1, this page provides the authorization URL to get started |
| settings-functions.php     |  This is the beef of the program. Defines our API endpoints, make sure our configuration is intact, makes all API calls, and adds a few helper functions |
| view.php     | Part of Step 5, this would be like the "logged in" view and shows the retrieved users posts |



## Screenshots

### Config

Set up config.php with variables from API setup

![alt text](https://raw.githubusercontent.com/christinabranson/Instagram-Basic-Display-API-Simple-Integration/master/readme/api_config_setup.png "Screenshot showing up to set up config.php")

The final result

![alt text](https://raw.githubusercontent.com/christinabranson/Instagram-Basic-Display-API-Simple-Integration/master/readme/fake_instagram.png "Screenshot showing the final result")