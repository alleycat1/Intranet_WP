This app used in our plugin "ThemeREX Socials" to show recent user's posts from Instagram, filtered by hashtag.
The app send queries only to follow endpoints:
1. If hashtag specified - GET /tags/tag-name/media/recent to show 
2. If no hashtag specified - GET /users/self/media/recent

To create an app:
1. Go to URL https://developers.instagram.com and log in.
2. Go to page "My Applications" and Click "Create Application"
3. Select a type of a new application: "User"
4. Specify a name of application and your e-mail and press the button "Create application"
5. On a next screen select "Instagram Basic Display"
6. In the menu "Settings - General" fill required fields:
   - Application domain - enter a domain of your site
