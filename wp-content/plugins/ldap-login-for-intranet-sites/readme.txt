=== Active Directory Integration / LDAP Integration ===
Contributors: miniOrange
Donate link: https://miniorange.com
Tags: active directory, active directory integration, ldap, ldap authentication, authentication, ldap authorization, active directory login, ldap directory, sso, kerberos ntlm, windows sso, ad login
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 5.2.0
Stable tag: 4.1.11
License: MIT/Expat
License URI: https://docs.miniorange.com/mit-license

Active Directory Integration/LDAP Integration supports login into WordPress using Active Directory/other Directory credentials, ACTIVE SUPPORT PROVIDED

== Description ==

[Showcase](https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites) | [Documentation](https://plugins.miniorange.com/wordpress-ldap-setup-guides) | [Features](https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites#section-key-features) | [Add-Ons](https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites#LDAP-intranet-add-ons) | [Contact Us](https://www.miniorange.com/contact)

[Active Directory Integration / LDAP Integration Login for Intranet Sites plugin](https://plugins.miniorange.com/step-by-step-guide-for-wordpress-ldap-login-plugin) allows you to authenticate your users using their Active Directory/LDAP credentials into your WordPress site. It allows you to map the Active Directory/LDAP attributes to the WordPress user profile attributes and also lets you assign WordPress roles to your LDAP/Active Directory users. Additionally, the plugin has a user authentication report feature, which logs each unsuccessful Active Directory/LDAP authentication request made, providing additional security functionalities. 

This plugin allows users to authenticate against various Active Directory /other LDAP Servers like:

* Microsoft Active Directory
* Azure Active Directory
* Sun Active Directory
* OpenLDAP Directory
* JumpCloud
* FreeIPA Directory
* Synology
* OpenDS and other LDAP directories.

The LDAP/Active Directory Login for Intranet plugin includes user management features as well, such as adding users from Active Directory or another LDAP Directory who are not registered in WordPress, WordPress role mapping, LDAP/Active Directory to WordPress attribute mapping, and more. We also provide additional add-ons that enhance the functionality of the basic plugin such as enabling [Kerberos/NTLM SSO Authentication](https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites#LDAP-intranet-add-ons), importing users from Active Directory/LDAP Server to WordPress, creating users in Active Directory/LDAP server when created/registered in the WordPress site, sync users between the Active Directory/LDAP server and WordPress site, sync LDAP/Active Directory Profile Picture thumbnail attribute to WordPress user profile picture, integration with third-party plugins and more.

= Minimum Requirements =
* Compatible with WordPress version 5.0 or higher.
* Compatible with PHP version 5.2.0 or higher.


= Free Version Features:- =

* Perform LDAP Authentication for any user trying to log into the WordPress website whose Active Directory/LDAP credentials are stored in the Active Directory/LDAP server. Additionally, login with WordPress credentials is also supported.
* Keep the WordPress User's profile information in sync with the Active Directory/other LDAP Directories upon authentication.
* Automatic User Registration in WordPress: Automatically create WordPress users who are present in the LDAP server/Active Directory upon login.
* Role Mapping: You can select a default WordPress role and assign it to all the Active Directory/LDAP users while LDAP/AD login is performed.
* Attribute mapping: Map the LDAP/Active Directory mail attribute to the WordPress user email and sync upon every successful LDAP/Active Directory Login.
* [LDAPS (LDAP Secure Connection) support](https://www.miniorange.com/guide-to-setup-ldaps-on-windows-server): Supports establishing Secure Connection between WordPress site and Active Directory/LDAP server via LDAPS protocol, this ensures protection against credential theft.
* Authentication Report: Keep logs of all the Active Directory/LDAP users who try to authenticate in your WordPress website and fail. A lot of the time these are security risks. The LDAP User Authentication report will give you a list of these users, you can also export a CSV of this report.
* Automatic fetching of LDAP Organizational Units from LDAP Server/Active Directory while configuring Search Base for LDAP/AD Login.
* Test connection to your Active Directory/other LDAP Directory while configuring LDAP server information in the plugin.
* Test authentication using credentials stored in your Active Directory/other LDAP Directory after configuring LDAP server information in the plugin.
* Ability to test against demo Active Directory/other LDAP Directory and demo credentials. You can do this using the demo LDAP Directory credentials from [here](https://www.forumsys.com/2022/05/10/online-ldap-test-server/).
* Support Integration with hybrid Active Directory infrastructure.
* Compatible with the latest versions of WordPress and PHP.
* We provide extensive easy-to-understand [documentation](https://plugins.miniorange.com/wordpress-ldap-setup-guides) as well as [YouTube setup videos](https://www.youtube.com/playlist?list=PL2vweZ-PcNpd3lEzmiLZwL_cAG_Evg2QC) which will assist you while configuring our LDAP Active Directory plugin.


**You can find out how to configure the Active Directory Integration / LDAP Integration plugin through the video below**

https://www.youtube.com/watch?v=5DUGgP-Hf-k

This LDAP/Active Directory Login plugin is free to use under the MIT/Expat license. If you wish to use enhanced features, you may purchase our [Premium version](https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites). We also provide additional [add-ons](https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites#LDAP-intranet-add-ons) that enhance the functionality of the basic WordPress LDAP/AD Login plugin. This will help support further development of our LDAP plugin, and in turn, serve our customers better.

= [Premium Version Features](https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites)=

* <strong>Login With Any LDAP Attribute Of Your Choice:</strong> Authenticate users against multiple LDAP/Active Directory username attributes like sAMAccountName, UID, UserPrincipalName, mail, cn, or any other custom LDAP attribute(s) according to your LDAP Active Directory/any other LDAP directory.
* <strong>Auto-register of LDAP users in WordPress site:</strong> Allows users of Active Directory/other LDAP Directory to auto-register in WordPress.
* <strong>Advanced Role Mapping:</strong> Assign specific WordPress roles based on the LDAP/Active Directory group memberships or the Organizational Units which are set in the LDAP Server/Active Directory. You can also assign a default WordPress role to all the LDAP/Active Directory users.
* <strong>Fetch LDAP groups automatically for Role Mapping:</strong> Fetches the LDAP/AD Security Groups present in your Active Directory/other LDAP Directory.
* <strong>Attribute Mapping:</strong> Configure and fetch the LDAP/AD attributes such as UID, cn (common name), mail, telephoneNumber, givenName, sn, sAMAccountName, and map with WordPress user profile attributes upon LDAP/AD Login.
* <strong>Custom Attribute Mapping:</strong> You can create your own WordPress custom user profile attributes which is a nifty tool if your organization has various attributes present in the Active Directory/LDAP server.
* <strong>Custom Search Filter:</strong> Allows you to restrict user authentication on the basis of LDAP/Active Directory security groups, userAccountControl etc.
* <strong>Authenticate Users from Multiple LDAP Search Bases:</strong> Authenticate users against multiple search bases from your Active Directory/other LDAP Directory.
* <strong>Automatic LDAP/Active Directory Users Search Base Selection:</strong> Fetches and allows you to select the Organization Unit (OU) present in your Active Directory/other LDAP Directory for the user's search base.
* <strong>Multiple LDAP Directories Configuration:</strong> Perform LDAP/Active Directory authentication against multiple directories through sequential search or on the basis of domain membership.
* <strong>WordPress to LDAP User Profile Sync:</strong> Update/Sync the user profile in Active Directory/other LDAP Directory when updated from WordPress.
* <strong>Authenticate users from LDAP and WordPress:</strong> Enable all WordPress users or WordPress administrators to login even if they are not present in the LDAP/Active Directory.
* <strong>Redirect to Custom URL after Authentication:</strong> Redirect to WordPress Profile page/ Home page/ Custom URL after successful LDAP/Active Directory authentication.
* <strong>Detailed User Authentication Report:</strong>  Keep track of user's authentication requests for your WordPress site. Get detailed logging information for FAILED LDAP Authentication of individual users.
* <strong>Support for Import/Export Plugin Configuration:</strong> Export your LDAP plugin configuration from the staging/testing site and import it to the production/live site. This will save you the hassle of reconfiguring the LDAP plugin.
* <strong>[Multisite Support](https://plugins.miniorange.com/guide-to-setup-multisite-ldap-ad-plugin):</strong> The plugin supports LDAP/AD Login integration for multisite environments as well.
* <strong>Restrict login based on WordPress roles:</strong> Restrict LDAP/Active Directory login to certain users based on the roles which are assigned on WordPress.
* Provides seamless integration with [third-party plugins](https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites#LDAP-intranet-add-ons) such as BuddyBoss, BuddyPress, Ultimate Member, Gravity forms, Groups, and eMember.

**You can find out Active Directory Integration / LDAP Integration Premium Version Features through the video below**

https://www.youtube.com/watch?v=r0pnB2d0QP8

= [Add-ons List](https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites#LDAP-intranet-add-ons)=

* <strong>[Active Directory Single Sign-On (SSO) using Kerberos/NTLM]((https://plugins.miniorange.com/guide-to-setup-kerberos-single-sign-sso)):</strong> Enable Active Directory SSO (auto-login) on your WordPress site for Domain Joined Machines using Kerberos/NTLM SSO protocol. This supports Kerberos SSO authentication for Linux with Apache server, Windows authentication on IIS server, Windows with Apache server, etc. We also support Active Directory SSO solutions using the GSSAPI module as well.
* <strong>[Sync Users LDAP Directory](https://plugins.miniorange.com/guide-to-configure-miniorange-directory-sync-add-on-for-wordpress):</strong> Sync/Import WordPress users from Active Directory/other LDAP directory. Schedules can be configured for the synchronization to run at a specific time and after a specific time interval. Additionally, you can also enable WordPress to LDAP/Active Directory user sync which would enable you to update/create an LDAP/AD user's profile.
* <strong>[Sync BuddyPress Extended Profiles](https://plugins.miniorange.com/guide-to-setup-miniorange-ldap-buddypress-integration-add-on):</strong> Update the BuddyPress users extended profiles with Active Directory/LDAP Server attributes upon LDAP/AD login.
* <strong>[Password Sync with Active Directory/LDAP Directory](https://plugins.miniorange.com/guide-to-setup-password-sync-with-ldap-add-on):</strong> Update your Active Directory/other LDAP Directory user password, the WordPress password of LDAP users will be synced to the LDAP server when you update or reset it in WordPress.
* <strong>[Profile Picture Sync for WordPress and BuddyPress](https://plugins.miniorange.com/configure-miniorange-profile-picture-map-add-on-for-wordpress):</strong> Update your WordPress and BuddyPress profile picture with thumbnail photos stored in your Active Directory/other LDAP Directory or vice-versa.
* <strong>[Ultimate Member Login and Profile Integration](https://plugins.miniorange.com/guide-to-setup-ultimate-member-login-integration-with-ldap-credentials):</strong> Enable LDAP/AD Login for Ultimate Member Login form and map Active Directory / other LDAP Directory User Profile attributes with ultimate member profile page.
* <strong>Page/Post Restriction:</strong> This allows you to control access to your site's content (pages/posts) based on LDAP groups/WordPress roles.
* <strong>[Search Staff From Active Directory/other LDAP Directory](https://plugins.miniorange.com/guide-to-setup-miniorange-ldap-search-widget-add-on):</strong> Search and display your Active Directory/other LDAP Directory users on your website page using a search widget and shortcode.
* <strong>[Third-Party Plugin User Profile Integration](https://plugins.miniorange.com/guide-to-setup-third-party-user-profile-integration-with-ldap-add-on):</strong> Update user profiles created using any third-party plugin with information from your Active Directory/other LDAP Directory stored in WordPress user meta table.
* <strong>Gravity Forms Integration:</strong> Populate Gravity Form fields with information from Active Directory / other LDAP Directory. You can integrate with unlimited forms.
* <strong>[Sync BuddyPress Groups](https://plugins.miniorange.com/guide-to-setup-miniorange-ldap-buddypress-integration-add-on):</strong> Assign BuddyPress groups to users based on LDAP/AD group membership in Active Directory / other LDAP Directory.
* <strong>MemberPress Plugin Integration:</strong> Login to MemberPress-protected content with Active Directory / other LDAP Directory Credentials.
* <strong>eMember Plugin Integration:</strong> Login to eMember profiles with Active Directory / other LDAP Directory Credentials.
* <strong>WP Groups Plugin Integration:</strong> Assign users to WordPress groups created using the Groups plugin based on their LDAP/AD groups memberships present in the Active Directory / LDAP Server. You can map any number of Active Directory groups with WordPress groups.

= Why the free plugins are not sufficient? :- =

https://www.youtube.com/watch?v=VdAIDLCN-cQ

*    With authentication being one of the essential functions of the day, a fast and <strong>[priority support](https://www.miniorange.com/support-plans)</strong> (provided in paid versions) ensure that any issues you face on a live production site can be resolved in a timely manner.
*   <strong>Regular updates</strong> to the premium plugin compatible with the latest WordPress version. The updates include security and bug fixes. These updates <strong>ensure that you are updated with the latest security fixes</strong>.
*   Ensure timely updates for <strong>new WordPress/PHP releases</strong> with our premium plugins and compatibility updates to make sure you have adequate support for smooth transitions to new versions for WordPress and PHP.
*   <strong>Reasonably priced</strong> with various plans tailored to suit your needs.
*   <strong>Easy to setup</strong> with lots of support and documentation to assist with the setup.
*   High level of <strong>customization</strong> and <strong>add-ons</strong> to support specific requirements.

= Other Use-Cases we support:- =
* <strong>[miniOrange Active Directory/LDAP Integration for Cloud & Shared Hosting Platforms Plugin](https://plugins.miniorange.com/wordpress-ldap-login-cloud)</strong> supports login to WordPress sites hosted on a shared hosting platform using credentials stored in active directory and LDAP Directory systems in case you are not able to enable <strong>[LDAP Extension](https://faq.miniorange.com/knowledgebase/how-to-enable-php-ldap-extension/)</strong> on your site.
* <strong> [Search Staff/Employee present in your Active Directory](https://plugins.miniorange.com/wordpress-ldap-directory-search)</strong>: allows you to search and display the users present in your Active Directory / LDAP Server on a WordPress page using a shortcode.
* <strong>[WordPress Login and User Management Plugin](https://plugins.miniorange.com/wordpress-login-and-user-management-plugin)</strong>: This plugin offers several functionalities, including bulk user management, user redirection based on WordPress roles, user session management, auto-logout users, and the ability to make a page or post private or public based on an ID or URL.
* miniOrange also supports <Strong>[VPN use cases](https://www.miniorange.com/solutions/vpn-mfa-multi-factor-authentication)</Strong> Log in to your VPN client using Active Directory /other LDAP Directory credentials and <strong>[Multi-Factor Authentication](https://www.miniorange.com/products/multi-factor-authentication-mfa)</strong>.
* miniOrange supports <Strong>[API Security use cases](https://apisecurity.miniorange.com)</Strong> to protect and secure your APIs using our product <strong>[XecureAPI](https://apiconsole.miniorange.com)</strong> which helps you to enable Authentication methods ( like OAuth, SAML, LDAP, API Key Authentication, JWT Authentication etc ), Rate Limiting, IP restriction and much more on your APIs for complete protection.
* miniOrange supports <strong>[Single-Sign-On (SSO)](https://www.miniorange.com/products/single-sign-on-sso)</strong> into a plethora of applications and supports various protocols like(<strong>[RADIUS](https://blog.miniorange.com/radius-server-authentication/), [SAML](https://plugins.miniorange.com/wordpress-single-sign-on-sso), [OAuth](https://plugins.miniorange.com/wordpress-sso), [LDAP/LDAPS](https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites)</strong>, using various IDP's like <strong>Azure Active Directory, Microsoft On-Premise Active Directory, Octa, ADFS</strong>, etc.
* Contact us at info@xecurify.com to know more.

= Need support? =
Please email us at info@xecurify.com or <a href="https://xecurify.com/contact" target="_blank">Contact us</a>.

== Installation ==

= Prerequisites =
Active Directory Integration/LDAP Integration requires a few prerequisites before you can enable LDAP login for your WordPress sites.

I. Active Directory Integration/LDAP Integration requires a few `PHP Modules` to be enabled. Make sure these are enabled.

1. **PHP LDAP Module**:
Step-1: Open the php.ini file.
Step-2: Search for "extension=php_ldap.dll" in the php.ini file. Uncomment this line, if not present then add this line to the file and save the file.

2. **OPENSSL Module**:
Step-1: Open the php.ini file.
Step-2: Search for "extension=php_openssl.dll" in the php.ini file. Uncomment this line, if not present then add this line to the file and save the file.

II. To install Active Directory Integration/LDAP Integration the minimum requirements are:
1. **WordPress version 5.0**
2. **PHP version 5.2.0**

= From your WordPress dashboard =
1. Visit `Plugins > Add New`.
2. Search for `Active Directory Integration for Intranet Sites`. Find and Install `Active Directory Integration for Intranet Sites`.
3. Activate the plugin from your Plugins page.

= From WordPress.org =
1. Download Active Directory Integration for Intranet Sites.
2. Unzip and upload the `ldap-login-for-intranet-sites` directory to your `/wp-content/plugins/` directory.
3. Activate Active Directory Integration for Intranet Sites from your Plugins page.

= Once Activated =
1. Go to `Settings-> LDAP Login Config`, and follow the instructions.
2. Click on `Save`.

Make sure that if there is a firewall, you `OPEN THE FIREWALL` to allow incoming requests to your LDAP from your WordPress Server IP and open port 389 (636 for SSL or LDAPS).

== Frequently Asked Questions ==

Click [here](https://faq.miniorange.com/kb/ldap-authentication/) to view our FAQ page.

For support or troubleshooting help please email us at info@xecurify.com or [Contact us](https://miniorange.com/contact).


== Screenshots ==

1. Configure LDAP Plugin
2. Sign-In Settings
3. Configure Multiple Directories
4. LDAP Groups to WordPress Users Role Mapping
5. User Attributes Mapping between LDAP and WP
6. Export/Import LDAP Plugin Configuration
7. LDAP Authentication Report
8. LDAP Premium Add-ons

== Changelog ==

= 4.1.11 =
* Active Directory Integration :
 * Usability Improvements.

= 4.1.10 =
* Active Directory Integration :
 * Security Fixes.

= 4.1.9 =
* Active Directory Integration :
 * UI Improvements.

= 4.1.8 =
* Active Directory Integration :
 * Improvements in Error Messages.
 * Compatibility with WordPress version 6.3.

= 4.1.7 =
* Active Directory Integration :
 * UI Improvements.

= 4.1.6 =
* Active Directory Integration :
 * Vulnerability Fixes.

= 4.1.5 =
* Active Directory Integration :
 * Security Fixes.
 * Code Optimization.

= 4.1.4 =
* Active Directory Integration :
 * Removed Plugin Tour.

= 4.1.3 =
* Active Directory Integration :
 * Compatibility with WordPress version 6.2.

= 4.1.2 =
* Active Directory Integration :
 * Usability Improvements.
 * UI Enhancement.

= 4.1.1 =
* Active Directory Integration :
 * Vulnerability Fixes.
 * Readme update.

= 4.1.0 =
* Active Directory Integration :
 * WP Guideline & Security Fixes.
 * Code Optimization.

= 4.0.8 =
* Active Directory Integration :
 * Advertisement of Christmas Offers.
 * Usability Improvements.

= 4.0.7 =
* Active Directory Integration :
 * Updated Licensing Plans.
 * Compatibility with PHP 8.1.

= 4.0.6 =
* Active Directory Integration :
 * Compatibility with WordPress 6.1.
 * Minor UI fixes.

= 4.0.5 =
* Active Directory Integration :
 * Compatibility fixes.

= 4.0.4 =
* Active Directory Integration :
 * UI Improvement.
 * Updated setup video and guide for configuration of the Plugin.
 * Improved Account Registration form.
 
= 4.0.3 =
* Active Directory Integration :
 * UI Improvements.
 * Added new FAQ's.

= 4.0.2 =
* Active Directory Integration :
 * Usability Improvements.
 * UI Improvements.

= 4.0.1 =
* Active Directory Integration :
 * UI Improvements.
 * Usability Improvements

= 4.0 =
* Active Directory Integration :
 * UI Improvements.
 * Improved visibility of Error and Success messages.

= 3.7.7 =
* Active Directory Integration :
 * UI Enhancement.
 * Vulnerabilities Fixes & Security Improvements.

= 3.7.6 =
* Active Directory Integration :
 * Introduced new licensing plans.
 * Usability Improvements.

= 3.7.5 =
* Active Directory Integration :
 * Compatibility with WordPress 6.0.
 * Authentication report bug fixes.
 * Usability Improvements.

= 3.7.4 =
* Active Directory Integration :
 * Compatibility with WordPress 5.9.3.
 * Added a new FAQ.
 * Added a new feature to set multiple roles and persist existing roles for the user.

= 3.7.3 =
* Active Directory Integration :
 * Compatibility with WordPress 5.9.2.
 * Added Export User Authentication Reports to the CSV file feature.
 * Custom email domain feature for Users email.

= 3.7.2 =
* Active Directory Integration :
 * Bug fixes for empty email field in username attribute
 * Code optimization
 * Usability Improvements

= 3.7.1 =
* Active Directory Integration :
 * Compatibility with WordPress 5.9.
 * Usability Improvements.
 
= 3.7 =
* Active Directory Integration :
 * Usability Improvements.

= 3.6.99 =
* Active Directory Integration :
 * New Year Offers.
 * Bug fix in default email domain mapping.

= 3.6.98 =
* Active Directory Integration :
 * Christmas Offers & Usability Improvements.

= 3.6.97 =
* Active Directory Integration :
 * Usability & Security Improvements.

= 3.6.96 =
* Active Directory Integration :
 * Vulnerabilities Fixes & Security Improvements.

= 3.6.95 =
* Active Directory Integration :
 * Bug Fixes - Sanitization of input fields.

= 3.6.94 =
* Active Directory Integration :
 * Usability Improvements.
 * Added option to set user's email to username@email_domain in WordPress, if the "mail" attribute is not set in LDAP directory.

= 3.6.93 =
* Active Directory Integration :
 * Usability Improvements.

= 3.6.92 =
* Active Directory Integration :
 * Usability Improvements.

= 3.6.91 =
* Active Directory Integration :
 * Usability Improvements.

= 3.6.9 =
* Active Directory Integration :
 * Compatible with WordPress 5.8.
 * Usability Improvements.

= 3.6.8 =
* Active Directory Integration :
 * Integrated a support form for scheduling a call for assistance.

= 3.6.7 =
* Active Directory Integration :
 * Bug Fix for auto registration of LDAP user.

= 3.6.6 =
* Active Directory Integration :
 * Added new add-ons to integrate with third party plugins.
 * Usability Improvements.

= 3.6.5 =
* Active Directory Integration :
 * Usability Improvements.
 * Default Role Mapping feature.
  * Assign default WordPress role for all users after login.

= 3.6.4 =
* Active Directory Integration :
 * Usability Improvements.

= 3.6.3 =
* Active Directory Integration :
 * Tested for WordPress 5.7.
 * Compatibility Fixes for PHP 8.0.
 * Usability Improvements.
 
= 3.6.2 =
* Active Directory Integration :
 * Usability Improvements.

= 3.6.1 =
* Active Directory Integration :
 * Usability Improvements.

= 3.6 =
* Active Directory Integration :
 * Added setup guides and videos for premium add-ons.
 * Compatible with WordPress 5.6

= 3.5.93 =
* Active Directory Integration :
 * Added dropdown to select Directory Server Type.
 * Improvements in "Premium Plugin Trial Request" feature.
 * Usability Improvemnts in Licensing Page.

= 3.5.92 =
* Active Directory Integration :
 * Improvements for possible Base DNs from Active Directory.
 * Plugin tour fixes and usability improvements.
 * Added "Premium Plugin Trial Request" feature.

= 3.5.91 =
* Active Directory Integration :
 * Compatibility with WordPress 5.5.
 * Usability improvements and fixes
 * fetch users DN from Active Directory.

= 3.5.9 =
* Active Directory Integration : Usability improvements for Active Directory Integration

= 3.5.85 =
* Active Directory Integration : Usability improvement to fetch list of possible Base DNs from Active Directory

= 3.5.8 =
* Active Directory Integration : Usability improvements.

= 3.5.7 =
* Active Directory Integration : Usability improvements and bug fixes.

= 3.5.6 =
* Active Directory Integration : Compatibility with 5.4.2, Usability improvements for search attribute.

= 3.5.5 =
* Active Directory Integration : Usability changes and fix for fetching email address at login time.

= 3.5.4 =
* Active Directory Integration : PHP 7.4 and WordPress 5.4 compatibility

= 3.5.3 =
* Active Directory Integration : Compatibility fixes

= 3.5.2 =
* Active Directory Integration : Fixes
 * Compatibility Fixes
 * UI fixes

= 3.5.1 =
* Active Directory Integration : Usability Improvements.

= 3.5 =
* Active Directory Integration : 
 * Compatibility to WordPress 5.3
 * Bug Fixes and Improvements.

= 3.0.13 =
* Active Directory Integration : UI fix.

= 3.0.12 =
* Active Directory Integration : UI fix.

= 3.0.11 =
* Active Directory Integration : Bug fix for anonymous bind and uploading/editing images in wordpress.

= 3.0.10 =
* Active Directory Integration : Change in Contact Us email.

= 3.0.9 =
* Active Directory Integration : Improvements
 * Audit logs for authentication
 * Compatibility to WordPress 5.2
 * Bug Fixes and Improvements.

= 3.0.8 =
* Active Directory Integration : Bug Fixes and Improvements.

= 3.0.7 =
* Active Directory Integration : Bug Fixes and Improvements.

= 3.0.6 =
* Active Directory Integration : Multisite upgrade links added.

= 3.0.5 =
* Active Directory Integration : Bug Fixes and Improvement.

= 3.0.4 =
* Active Directory Integration : Bug Fixes and Improvement.

= 3.0.3 =
* Active Directory Integration : Bug Fixes and Improvement.

= 3.0.2 =
* Active Directory Integration : Improvements
 * Improved Visual Tour
 * Added tab for making feature requests
 * Made registration optional
 * Listed add-ons in licensing plans.

= 3.0.1 =
* Active Directory Integration : Compatibility Fix
 * Support for PHP version > 5.3
 * Wordpress 5.0.1 Compatibility

= 3.0 =
* Active Directory Integration : Added Visual Tour

= 2.92 =
* Active Directory : Role Mapping bug fixes

= 2.91 =
* Active Directory : Improvements
 * Usability fixes
 * Bug fixes
 * Licensing page revamp

= 2.9 =
* Active Directory : Usability fixes

= 2.8.3 =
* Active Directory : Added Feedback Form

= 2.8 =
* Active Directory : Removed MCrypt dependency. Bug fixes

= 2.7.7 =
* Active Directory : Phone number visible in profile

= 2.7.6 =
* Active Directory : Compatible with WordPress 4.9.4 and removed external links

= 2.7.43 =
* Active Directory : On-premise IdP information

= 2.7.42 =
* Active Directory : WordPress 4.9 Compatibility

= 2.7.4 =
* Active Directory : Fix for login with user name/email

= 2.7.3 =
* Active Directory : Additional feature links.

= 2.7.2 =
* Active Directory : Licensing fixes.

= 2.7.1 =
* Active Directory : Activation warning fix. Basic registration fields required for upgrade.

= 2.7 =
* Active Directory : Registration removal, role mapping fixes and user name attribute configurable.

= 2.6.6 =
* Active Directory : Updating Plugin Title

= 2.6.5 =
* Active Directory : Licensing fix

= 2.6.4 =
Name fixes

= 2.6.2 =
Name changed

= 2.6.1 =
Added TLS support

= 2.5.8 =
Increased priority for authentication hook

= 2.5.7 =
Licensing fixes

= 2.5.6 =
WordPress 4.6 Compatibility

= 2.5.5 =
Added option to authenticate Administrators from both LDAP and WordPress

= 2.5.4 =
More page fixes

= 2.5.3 =
Page fixes

= 2.5.2 =
Registration fixes

= 2.5.1 =
*	UI improvement and fix for WP 4.5

= 2.5 =
Added more descriptive error messages and licensing plans updated.

= 2.3 =
Support for Integrated Windows Authentication - contact info@xecurify.com if interested

= 2.2 =
+Added alternate verification method for user activation.

= 2.1 =
+Minor Bug fixes.

= 2.0 =
Attribute Mapping and Role Mapping Bug fixes and Enhancement.

= 1.9 =
Attribute Mapping bug fixes

= 1.8 =
Role Mapping Bug fixes

= 1.7 =
Fallback to local password in case LDAP server is unreacheable.

= 1.6 =
Added attribute mapping and custom profile fields from LDAP.

= 1.5 =
Added mutiple role support in WP users to LDAP Group Role Mapping.

= 1.4 =
Improved encryption to support special characters.

= 1.3 =
Enhanced Usability and UI for the plugin.

= 1.2 =
Added LDAP groups to WordPress Users Role Mapping

= 1.1 =
Enhanced Troubleshooting

= 1.0 =
* this is the first release.

== Upgrade Notice ==

= 4.1.11 =
* Active Directory Integration :
 * Usability Improvements.

= 4.1.10 =
* Active Directory Integration :
 * Security Fixes.

= 4.1.9 =
* Active Directory Integration :
 * UI Improvements.

= 4.1.8 =
* Active Directory Integration :
 * Improvements in Error Messages.
 * Compatibility with WordPress version 6.3.

= 4.1.7 =
* Active Directory Integration :
 * UI Improvements.

= 4.1.6 =
* Active Directory Integration :
 * Vulnerability Fixes.

= 4.1.5 =
* Active Directory Integration :
 * Security Fixes.
 * Code Optimization.

= 4.1.4 =
* Active Directory Integration :
 * Removed Plugin Tour.

= 4.1.3 =
* Active Directory Integration :
 * Compatibility with WordPress version 6.2.

= 4.1.2 =
* Active Directory Integration :
 * Usability Improvements.
 * UI Enhancement.

= 4.1.1 =
* Active Directory Integration :
 * Vulnerability Fixes.
 * Readme update.

= 4.1.0 =
* Active Directory Integration :
 * WP Guideline & Security Fixes.
 * Code Optimization.

= 4.0.8 =
* Active Directory Integration :
 * Advertisement of Christmas Offers.
 * Usability Improvements.

= 4.0.7 =
* Active Directory Integration :
 * Updated Licensing Plans.
 * Compatibility with PHP 8.1.

= 4.0.6 =
* Active Directory Integration :
 * Compatibility with WordPress 6.1.
 * Minor UI fixes.

= 4.0.5 =
* Active Directory Integration :
 * Compatibility fixes.

= 4.0.4 =
* Active Directory Integration :
 * UI Improvement.
 * Updated setup video and guide for configuration of the Plugin.
 * Improved Account Registration form.
 
= 4.0.3 =
* Active Directory Integration :
 * UI Improvements.
 * Added new FAQ's.

= 4.0.2 =
* Active Directory Integration :
 * Usability Improvements.
 * UI Improvements.

= 4.0.1 =
* Active Directory Integration :
 * UI Improvements.
 * Usability Improvements

= 4.0 =
* Active Directory Integration :
 * UI Improvements.
 * Improved visibility of Error and Success messages.

= 3.7.7 =
* Active Directory Integration :
 * UI Enhancement.
 * Vulnerabilities Fixes & Security Improvements.

= 3.7.6 =
* Active Directory Integration :
 * Introduced new licensing plans.
 * Usability Improvements.

= 3.7.5 =
* Active Directory Integration :
 * Compatibility with WordPress 6.0.
 * Authentication report bug fixes.
 * Usability Improvements.

= 3.7.4 =
* Active Directory Integration :
 * Compatibility with WordPress 5.9.3.
 * Added a new FAQ.
 * Added a new feature to set multiple roles and persist existing roles for the user.

= 3.7.3 =
* Active Directory Integration :
 * Compatibility with WordPress 5.9.2.
 * Added Export User Authentication Reports to the CSV file feature.
 * Custom email domain feature for Users email.

= 3.7.2 =
* Active Directory Integration :
 * Bug fixes for empty email field in username attribute
 * Code optimization
 * Usability Improvements

= 3.7.1 =
* Active Directory Integration :
 * Compatibility with WordPress 5.9.
 * Usability Improvements.

= 3.7 =
* Active Directory Integration :
 * Usability Improvements.

= 3.6.99 =
* Active Directory Integration :
 * New Year Offers.
 * Bug fix in default email domain mapping.


= 3.6.98 =
* Active Directory Integration :
 * Christmas Offers & Usability Improvements.

= 3.6.97 =
* Active Directory Integration :
 * Usability & Security Improvements.

= 3.6.96 =
* Active Directory Integration :
 * Vulnerabilities Fixes & Security Improvements.

= 3.6.95 =
* Active Directory Integration :
 * Bug Fixes - Sanitization of input fields.

= 3.6.94 =
* Active Directory Integration :
 * Usability Improvements.
 * Added option to set user's email to username@email_domain in WordPress, if the "mail" attribute is not set in LDAP directory.

= 3.6.93 =
* Active Directory Integration :
 * Usability Improvements.

= 3.6.92 =
* Active Directory Integration :
 * Usability Improvements.

= 3.6.91 =
* Active Directory Integration :
 * Usability Improvements.
 
= 3.6.9 =
* Active Directory Integration :
 * Compatible with WordPress 5.8.
 * Usability Improvements.

= 3.6.8 =
* Active Directory Integration :
 * Integrated a support form for scheduling a call for assistance.

= 3.6.7 =
* Active Directory Integration :
 * Bug Fix for auto registration of LDAP user.
 
= 3.6.6 =
* Active Directory Integration :
 * Added new add-ons to integrate with third party plugins.
 * Usability Improvements.

= 3.6.5 =
* Active Directory Integration :
 * Usability Improvements.
 * Default Role Mapping feature.
   * Assign default WordPress role for all users after login.

= 3.6.4 =
* Active Directory Integration :
 * Usability Improvements.
 
= 3.6.3 =
* Active Directory Integration :
 * Tested for WordPress 5.7.
 * Compatibility Fixes for PHP 8.0.
 * Usability Improvements.

= 3.6.2 =
* Active Directory Integration :
 * Usability Improvements.

= 3.6.1 =
* Active Directory Integration :
 * Usability Improvements.

= 3.6 =
* Active Directory Integration :
 * Added setup guides and videos for premium add-ons.
 * Compatible with WordPress 5.6

= 3.5.93 =
* Active Directory Integration :
 * Added dropdown to select Directory Server Type.
 * Improvements in "Premium Plugin Trial Request" feature.
 * Usability Improvements in Licensing Page.

= 3.5.92 =
* Active Directory Integration :
 * Improvements for possible Base DNs from Active Directory.
 * Plugin tour fixes and usability improvements.
 * Added "Premium Plugin Trial Request" feature.

= 3.5.91 =
* Active Directory Integration :
 * Compatibility with WordPress 5.5.
 * Usability improvements and fixes
 * fetch users DN from Active Directory.

= 3.5.9 =
* Active Directory Integration : Usability improvements for Active Directory Integration

= 3.5.85 =
* Active Directory Integration : Usability improvement to fetch list of possible Base DNs from Active Directory

= 3.5.8 =
* Active Directory Integration : Usability improvements.

= 3.5.7 =
* Active Directory Integration : Usability improvements and bug fixes.

= 3.5.6 =
* Active Directory Integration : Compatibility with 5.4.2, Usability improvements for search attribute.

= 3.5.5 =
* Active Directory Integration : Usability changes and fix for fetching email address at login time.

= 3.5.4 =
* Active Directory Integration : PHP 7.4 and WordPress 5.4 compatibility

= 3.5.3 =
* Active Directory Integration : Compatibility fixes

= 3.5.2 =
* Active Directory Integration : Fixes
 * Compatibility Fixes
 * UI fixes

= 3.5.1 =
* Active Directory Integration : Usability Improvements.

= 3.5 =
* Active Directory Integration : 
 * Compatibility to WordPress 5.3
 * Bug Fixes and Improvements.

= 3.0.13 =
* Active Directory Integration : UI fix.

= 3.0.12 =
* Active Directory Integration : UI fix.

= 3.0.11 =
* Active Directory Integration : Bug fix for anonymous bind and uploading/editing images in wordpress.

= 3.0.10 =
* Active Directory Integration : Change in Contact Us email.

= 3.0.9 =
* Active Directory Integration : Improvements
 * Audit logs for authentication
 * Compatibility to WordPress 5.2
 * Bug Fixes and Improvements.

= 3.0.8 =
* Active Directory Integration : Bug Fixes and Improvements.

= 3.0.7 =
* Active Directory Integration : Bug Fixes and Improvements.

= 3.0.6 =
* Active Directory Integration : Multisite upgrade links added.

= 3.0.5 =
* Active Directory Integration : Bug Fixes and Improvement.

= 3.0.4 =
* Active Directory Integration : Bug Fixes and Improvement.

= 3.0.3 =
* Active Directory Integration : Bug Fixes and Improvement.

= 3.0.2 =
* Active Directory Integration : Improvements
 * Improved Visual Tour
 * Added tab for making feature requests
 * Made registration optional
 * Listed add-ons in licensing plans.

= 3.0.1 =
* Active Directory Integration : Compatibility Fix
 * Support for PHP version > 5.3
 * Wordpress 5.0.1 Compatibility

= 3.0 =
* Active Directory Integration : Added Visual Tour

= 2.92 =
* Active Directory : Role Mapping bug fixes

= 2.91 =
* Active Directory : Improvements
 * Usability fixes
 * Bug fixes
 * Licensing page revamp

= 2.9 =
* Active Directory : Usability fixes

= 2.8.3 =
* Active Directory : Added Feedback Form

= 2.8 =
* Active Directory : Removed MCrypt dependency. Bug fixes

= 2.7.7 =
* Active Directory : Phone number visible in profile

= 2.7.6 =
* Active Directory : Compatible with WordPress 4.9.4 and removed external links

= 2.7.43 =
* Active Directory : On-premise IdP information

= 2.7.42 =
* Active Directory : WordPress 4.9 Compatibility

= 2.7.4 =
* Active Directory : Fix for login with username/email

= 2.7.3 =
* Active Directory : Additional feature links.

= 2.7.2 =
* Active Directory : Licensing fixes.

= 2.7.1 =
* Active Directory : Activation warning fix. Basic registration fields required for upgrade.

= 2.7 =
* Active Directory : Registration removal, role mapping fixes and username attribute configurable.

= 2.6.6 =
* Active Directory : Updating Plugin Title

= 2.6.5 =
* Active Directory : Licensing fix

= 2.6.4 =
Name fixes

= 2.6.2 =
Name changed

= 2.6.1 =
Added TLS support

= 2.5.8 =
Increased priority for authentication hook

= 2.5.7 =
Licensing fixes

= 2.5.6 =
WordPress 4.6 Compatibility

= 2.5.5 =
Added option to authenticate Administrators from both LDAP and WordPress

= 2.5.4 =
More page fixes

= 2.5.3 =
Page fixes

= 2.5.2 =
Registration fixes

= 2.5.1 =
*	UI improvement and fix for WP 4.5

= 2.5 =
Added more descriptive error messages and licensing plans updated.

= 2.3 =
Support for Integrated Windows Authentication - contact info@xecurify.com if interested

= 2.2 =
+Added alternate verification method for user activation.

= 2.1 =
+Minor Bug fixes.

= 2.0 =
Attribute Mapping and Role Mapping Bug fixes and Enhancement.

= 1.9 =
Attribute Mapping bug fixes

= 1.8 =
Role Mapping Bug fixes

= 1.7 =
Fallback to local password in case LDAP server is unreacheable.

= 1.6 =
Added attribute mapping and custom profile fields from LDAP .

= 1.5 =
Added mutiple role support in WP users to LDAP Group Role Mapping .

= 1.4 =
Improved encryption to support special characters.

= 1.3 =
Enhanced Usability and UI for the plugin.

= 1.2 =
Added LDAP groups to WordPress Users Role Mapping

= 1.1 =
Enhanced Troubleshooting

= 1.0 =
First version of plugin.