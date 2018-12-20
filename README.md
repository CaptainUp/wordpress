# Captain Up WordPress Plugin

Captain Up is an engagement platform for your WordPress blog. Quickly add badges, levels and leaderboards to your site and start rewarding your users.

![Captain Up - Engagement Platform as a Service](https://user-images.githubusercontent.com/550061/50300165-3bfed600-048c-11e9-84d5-c14de32ac3ca.png)

---

[Captain Up](https://captainup.com/) ∞ [Download from the WordPress plugin directory](https://wordpress.org/plugins/captain-up/) ∞ [Features](https://captainup.com/solutions/product) ∞ [Case Studies](https://captainup.com/solutions/case-study)

---

[Captain Up](https://captainup.com/) boosts engagement on your WordPress site using game mechanics. Plug it in and immediately start rewarding users for engaging with your content, using points, levels, badges, and leaderboards, and keep them coming back for more.

Note: You will need to connect your WordPress site to a Captain Up account. If you don’t have one yet, contact us at team@captainup.com.

**NEW**: Rewards: Give your loyal users badges, trophies, coupons, and rewards.

**NEW**: Messaging: Communicate with your users, send them welcome messages, updates, and promotions.

## Features

- **Total customization** – You can create new badges that users get by visiting specific pages or categories on your WordPress site, or for liking your Facebook page. You can fully customize all badges and levels, including how they look and what users have to do to get each one.

- **Works out of the box** – With dozens of badges and levels, Captain Up gives your users a challenging gaming layer with a balanced learning curve, right away.

- **Deep insights and statistics** – Get to know your most passionate users. See who has the most Twitter followers and who brought you the most visitors. Understand how users are engaging with your WordPress site and how to improve your users' experience.

- **Widgets!** – The Activity Widget shows recent activity on your site, bringing together and showcasing your vibrant community. The Leaderboard Widget gets users competing and comparing their progress against one another.

- **Tons of actions** – You can reward users for visiting your site regularly, Tweeting about it, Liking things, visiting specific pages, commenting, or watching videos on your site. Set up custom actions for anything our default actions don’t cover yet.

- **Automatic support for WordPress User Integration**

- **Deep analytics on user behavior**

- **In-app messages and notifications**

- **Custom theme & design**

### Installing the Plugin

###### Automatic Install through WordPress

1. Go to the _Plugins -> Add New_ screen in your WordPress Admin Panel.

2. Search for 'CaptainUp'.

3. Click 'Install Now' and activate the plugin.

###### Manual Install through WordPress

1. Download the Captain Up plugin from the WordPress plugin directory.

2. Go to the _Plugins -> Add New_ screen in your WordPress Admin Panel and click on _upload_ tab.

3. Pick the Captain Up downloaded zip file and upload it.

4. Activate the plugin.

###### Manual Install with FTP

1. Download the Captain Up plugin from the WordPress plugin directory.

2. Extract the zip file you downloaded to a folder.

3. Upload the folder to your server, place it inside your WordPress install under `/wp-content/plugins/` directory.

4. Go to the _Plugins_ tab in your WordPress Admin Panel and activate the plugin.

###### After you Activate Captain Up

1. Go to the new _Captain Up_ tab in your WordPress Admin Panel.

2. Add your Captain Up API Key and Save. You can find your API key in the [Settings tab in your Captain Up Admin Panel](https://captainup.com/manage/settings). If you don't have a Captain Up account yet you can contact us at team@captainup.com.

## Shortcodes

You can add the Captain Up leaderboard widget or activity widget inside your posts using a shortcode:

* `[captain-leaderboard width="250px" height="400px" title="Leaderboard" leaderboard="all-time-ranking"]` - adds the leaderboard widget. All attributes are optional. By default the width of the widget will be 300 pixels, the height 400 pixels and the title will be "Leaderboard". The leaderboard option selects the default leaderboard to show, can be either one of `"all-time-ranking"`, `"monthly-ranking"`, `"weekly-ranking"` or `"daily-ranking"`.

* `[captain-activity width="250px" height="400px" title="Activity Feed"]` - adds the activity widget. All attributes are optional. By default the width of the widget will be 300 pixels, the height 400 pixels and the title will be "Activity".

* `[captain-sign-up text="Join the Game"]` - adds a link to join the Captain Up game. It will open the sign up modal, incentivizing your users to start playing. By default the text of the link will be "Sign Up Now".

## Contributing code

The source code for this plugin is available on [Captain Up's GitHub account](https://github.com/CaptainUp/wordpress-captainup). Pull Requests and issues are welcome.

## Changelog

###### 3.0.3

* Support for WordPress 5.0.1

###### 3.0.2

* Version bump.

###### 3.0.1

* Documentation updates
* Security improvements
 
###### 3.0.0

* Added client token to settings

###### 2.3.0

* Support for BuddyPress avatars
* Support for WordPress 4.3.0

###### 2.2.1

* Various bug fixes.

###### 2.2.0

* Support for WordPress 4.2.2
* Fixed issue when updating blank user attributes with secure user integration

###### 2.1.0

* Automatic user integration with WordPress user accounts
* Support for WordPress 4.1

###### 2.0.2

* Support for mobile and tablet devices
* Support for WordPress 4.0
* Added an app icon
* Small code modifications

###### 2.0.1

* Support for WordPress 3.9.1

###### 2.0.0

* Brand new design!

* Customize where the Captain Up widget appears on your site, including a new bottom bar widget.

* Configure on which pages of the site Captain Up will appear on using either a blacklist or a whitelist.

* Support for WordPress 3.8.2

###### 1.4.4

* Support for custom actions.

* Updated links to the Captain Up admin panel.

* Support for WordPress 3.8.1

###### 1.4.3

* Support for WordPress 3.8

###### 1.4.2

* Support for WordPress 3.7.1

* Fixed issue with MU installs and language selection.

###### 1.4.1

* Support for WordPress 3.6.1

* Better indication of a successful install.

* Fixed issue with `cptup_widgets_edit.js`.

###### 1.4.0

* Support for WordPress 3.5.2 and 3.6.0

* You can now select the default leaderboard for the Leaderboard widget. There are four options: All Time, Monthly, Weekly and Daily. This can be set in the Widgets tab or inside the Shortcodes.

* Fixed an issue with Cloudflare's Rocket Loader.

###### 1.3.1

* Hotfix: cleared a reference to a development version of Captain Up.

###### 1.3

* Beta: Added an option to add the API Secret in the Captain Up tab. This can be used by advanced users to connect user IDs between the Captain Up platform and your site for more customized user profiles.

* Fixed redirect after submitting the settings form in the admin panel.

* Added support for WordPress comments. Users can now be rewarded with points and badges for commenting with the native WordPress comments along with Disqus and Facebook comments.

###### 1.2

* Fixed a caching bug with Internet Explorer 10

* We no longer to release new WordPress versions for additional languages

* Added French, German, Portuguese (Brazil) and Thai support.

###### 1.1

* Removed the width option from the widgets

* Added Shortcodes for adding the Leaderboard Widget, the Activity Widget and a sign up link.

* Added Localization Options (Hebrew, English, Italian, Russian)

* Added missing semicolon in the embed script;

###### 1.0

* First Release.
