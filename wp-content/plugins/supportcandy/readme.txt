=== SupportCandy - Helpdesk & Support Ticket System ===
Contributors: supportcandy,pradeepmakone07,nsgawli
License: GPL v3
Tags: helpdesk,support,support plugin,ticketing,support desk,contact form,ticket system,tickets,support ticket,email ticket,customer support,ticketing,ticket form,support ticketing,help desk
Requires at least: 5.6
Tested up to: 6.3
Requires PHP: 7.4
Stable tag: 3.2.1

== Description ==

This plugin adds to WordPress the features of a complete helpdesk ticket system. It is one of the oldest support ticket systems available for WordPress. We aim to keep the plugin simple, secure, and feature-rich through continuous improvement and innovation.

[Follow this](https://supportcandy.net/docs-category/getting-started/) getting started guide and be ready to support your customers like never before within a few minutes!

[Click here](https://supportcandy.net/) to visit our official website.

= Key features : =
- Unlimited number of tickets
- Unlimited number of agents
- Advanced custom filter and search functionality
- Saved filters for customers and agents
- Private notes for internal communication of agents
- Agents can create a ticket on the customer's behalf
- 16+ Custom field types
- Ticket fields for additional information about ticket
- Agent-only fields to keep internal data of the ticket
- Customer fields to store information about the customer across the tickets
- Guest tickets (disabled by default)
- Guest can also see their ticket list using OTP Login (One Time Password)
- Rich-text editor (customizable)
- Email notifications
- Working hours
- Google reCaptcha integration
- GDPR Compatible
- Terms & Conditions checkbox in the ticket form
- Macros or placeholders for ticket fields

= Premium Add-Ons : =
* [Email Piping](https://supportcandy.net/email-piping/) - Create or reply to tickets from the comfort of an email account. For example, you can provide a dedicated email address to your customers, such as support@yourdomain.com. The emails received in this email will be converted to tickets.
* [WooCommerce Integration](https://supportcandy.net/woocommerce-integration/) - Add a support tab on the My Account page and a help button for orders. Customers can select the order in the ticket form. In addition, the agent can view customer orders in an individual ticket.
* [Canned Reply](https://supportcandy.net/canned-reply/) - Allows your agent to save replies. It requires a couple of clicks to insert an answer in the description.
* [Assign Agent Rules](https://supportcandy.net/assign-agent-rules/) - Set conditions to assign an agent automatically when a new ticket is created.
* [SLA (Service Lavel Agreement)](https://supportcandy.net/sla/) - Calculate the due date based on rules for tickets to match. The remaining time is shown in the ticket list if added SLA field to the ticket list and individual ticket.
* [Satisfction Survey](https://supportcandy.net/satisfaction-survey/) - Send an email to rate ticket based on assigned agent performance. Optionally customer can provide feedback to ticket.
* [Automatic Close Tickets](https://supportcandy.net/automatic-close-tickets/) - Close tickets after x days of inactivity. Sends warning email before closing the ticket.
* [Usergroup](https://supportcandy.net/usergroups/) - Allow a group of users to access each other's tickets.
* [Agentgoup](https://supportcandy.net/agentgroups/) - Allows you to assign the ticket to a group of agents so that any agent of the group supervisor has permission to view and manage the ticket.
* [Schedule Tickets](https://supportcandy.net/schedule-tickets/) - Create recurring tickets every x days/months from the custom start date.
* [Knowledgebase Integrations](https://supportcandy.net/knowledgebase-integrations-2/) - Integrates popular knowledgebase plugins for WordPress with your helpdesk.
* [FAQ Integrations](https://supportcandy.net/faq-integrations/) - Integrates popular FAQ plugins for WordPress with your helpdesk.
* [Export Tickets](https://supportcandy.net/export-tickets/) - Export tickets to CSV format so you can use it for various purposes such as generating reports.
* [Reports](https://supportcandy.net/reports/) - This add-on gives a graphical overview of your tickets.
* [Timer](https://supportcandy.net/timer/) - This add-on adds a timer widget to a ticket so that your agent can calculate the total time spent on a ticket.
* [Print Ticket](https://supportcandy.net/print-ticket/) - Add a print button inside an individual ticket. You can set the print template in the settings.
* [EDD Integration](https://supportcandy.net/edd-integration/) - Integrate Easy Digital Downloads with the ticket so that your agents can check customer orders/licenses within the ticket.
* [Gravity Forms Integration](https://supportcandy.net/gravity-forms-2/) - Create multiple ticket forms using Gravity Forms and pipe them to SupportCandy.
* [Private Credentials](https://supportcandy.net/private-credentials/) - Allow users to provide sensitive information within a ticket and manage access to this information.
* [Slack Integration](https://supportcandy.net/downloads/slack-integration/) - Get instant notifications to your Slack Channel and respond directly from Slack thread reply.
* [Workflows](https://supportcandy.net/downloads/workflows/) - Unlock the power of automation with SupportCandy workflows, revolutionizing the way you manage your processes.
* [LMS Integrations](https://supportcandy.net/downloads/lms-integration/) - Integrates popular LMS plugins for WordPress with your helpdesk.

= Examples areas of use : =
* Helpdesk
* Technical Support
* Trouble Ticket
* Customer Relations
* Software Release Lifecycle Management
* Service Request Management
* Company, Hotel or Real Estate Service-Desk
* To-Do List Management

= Available Translations : =
* Arabic
* French (France)
* German (Germany)
* Dutch (Netherlands)
* Italian (Italy)
* Portuguese (Portugal)
* Spanish (Spain)
* Chinese (China)
* Chinese (Traditional)
* Russian
* Hebrew
* Greek
* Portuguese (Brazil)
* Turkish
* Swedish
* Hungerian
* Polish

If you are a translator, you can get free access to all premium add-ons for a year in exchange for translating them into your language if it is not available already. We will renew the Premium subscription next year if you continue contributing to the translation. If interested, don't hesitate to contact us via our [support page](https://supportcandy.net/support-ticket/).

== Installation ==

This plugin is almost plug and play! Please [follow this](https://supportcandy.net/docs-category/getting-started/) getting started guide for basic installation instructions.

== Screenshots ==

1. Ticket list
2. Create ticket form
3. Individual ticket
4. Report - Ticket statistics (Premium)
5. Report - Response delay (Premium)
6. Report - Ticket closing delay (Premium)
7. Report - Communication gap (Premium)
8. Report - Category (Premium)
9. Report - Rating (Premium)

== Changelog ==

= 3.2.1 (September 25, 2023) =
* New: Learning Management System (LMS) Plugin Integration Add-On (Premium)
* New: Organize and categorize your tickets with tags for better management
* New: Avoid conflicting actions by agents with this new agent collision detection feature.
* New: New action added in the thank you setting. You can directly open the newly created ticket.
* Fix: Attachment links in notifications do not show for the attachment custom field
* Fix: Conditions not working for customer fields
* Fix: Translation does not work for default filters
* Fix: Login not working for some users
* Fix: Reply and close ticket action add an incorrect log
* Fix: GET /wp-json/supportcandy/v2/tickets API not working
* Fix: Unable to download docx and zip file attachments on some servers

= 3.2.0 (July 10, 2023) =
* New: Workflow add-on (Premium)
* New: Added a personal info option for customer fields
* New: Agent permission added for clone/duplicate ticket action
* New: Implemented translations for the date picker, making it accessible to users in multiple languages
* Fix: Resolved the issue with the attachment garbage collector, ensuring proper cleanup of unnecessary attachments
* Fix: Visibility conditions does not work correctly
* Fix: Addressed the problem where a ticket would not be created if a long text was entered in a textfield custom field during ticket creation
* Fix: Fixed the double email sending issue for reply and close ticket actions, preventing duplicate emails from being sent
* Fix: Rectified the problem of forward slashes being stripped out in textfield
* Fix: Different error message for GDPR and T&C in create ticket form
* Fix: Incorrect display of customer field change log
* Translation added for Hungerian, Polish languages

= 3.1.9 (June 10, 2023) =
* Fix: Attachments set to delete after user profile update in some cases
* Fix: Ticket list orderby not working in some cases

= 3.1.8 (May 18, 2023) =
* Fix: Dates are not showing correct timezone after v3.1.7
* Fix: POT and all language PO files updated

= 3.1.7 (May 18, 2023) =
* New: The content entered in the reply/note editor is now automatically saved as a draft using cookies, ensuring that it is safeguarded in the event of a submission failure.
* New: You now have the flexibility to customize the fields that will be compared during the ticket list search. This will improve the search speed.
* New: Email notification attachment setting. You can now select whether you prefer to attach files directly or provide links to the attachments.
* New: Reply & Close ticket action added
* New: Custom field dates can be translate now
* New: Agent role setting UI improvements
* Fix: Nonce timeout issue
* Fix: Vulnerability fixes

= 3.1.6 (April 08, 2023) =
* Fix: Ticket form fields not loading after v3.1.5

= 3.1.5 (April 07, 2023) =
* New: You can add multiple AND and OR conditions for all applicable places like ticket list filters, email notifications, Assign agent rules, SLA, etc.
* Fix: Important security fixes

= 3.1.4 (March 27, 2023) =
* New: Copy/paste images in reply box.
* Fix: Unable to download attachments if server is changed
* Fix: Email notifications does not send if the ticket subject is very long
* Fix: Unable to create a ticket on brave browser
* Fix: Attachment security fixes

= 3.1.3 (March 02, 2023) =
* New: REST APIs are now back.
* New: Slack integration added (Premium).
* New: Clone option for agent role setting.
* Fix: Registration does not work if Google recpatch v3 is active.
* Fix: Assign agent rule does not work if a duplicate ticket is created.
* Fix: PHP notices and warnings.

= 3.1.2 (January 17, 2023) =
* Fix: Menu item colors gone after last update (v3.1.1).

= 3.1.1 (January 16, 2023) =
* New: Shortcodes enabled within HTML custom field type.
* New: Support portal menu color setting in an appearance settings.
* Fix: Date close not reset after ticket reopen.
* Fix: Attachments not working in clone ticket.
* Fix: Few custom field types not being copied in clone ticket.
* Fix: Email notification conversation view not showing in Gmail inbox.
* Fix: Quotes in alert message not working.

= 3.1.0 (November 23, 2022) =
* Fix: User registration emails not sending if user is registered via SupportCandy.
* Fix: Ordering with status, category and priority is not working as per the set order.
* Fix: Notice generating if attachment file not exists.
* Fix: Scrolling to the top in the frontend if ticket list button is clicked.
* Fix: Do not load correct display name of the user when he is registered with WordPress default functionality.
* Fix: Gravtar default setting not getting applied from WordPress.
* Fix: Dropdown translation not loading.
* Fix: Documentation links added.

= 3.0.9 (October 14, 2022) =
* New: Logout button added on the top header.
* Fix: Auto delete closed tickets improvements.
* Fix: Permanently delete tickets improvements.
* Fix: Installation bugs fixed.
* Fix: Attachment error if used with Google reCaptcha version 3.

= 3.0.8 (October 03, 2022) =
* New: Hooks added for before and after widgets to enable adding custom widgets if required.
* New: CSS classes added for thread elements to distinguish customer reply or agent reply.
* Fix: Removed auto-scroll from frontend.
* Fix: Unable to remove the placeholder from string translations.
* Fix: Create ticket not working if captcha credentials missing.
* Fix: Added few missing translation text.
* Fix: Default value for custom fields Status, Category and Priority is now a required field.
* Fix: Few framework scripts printing on all pages even if set for custom pages.
* Fix: Add-on license activation does not work if site_url and home_url is different.
* Fix: Attachment does not download if site_url and home_url is different.
* Fix: Multi-select required field validation not working in the ticket form.

= 3.0.7 (August 22, 2022) =
* New: Setting to choose pages to load scripts. Default all pages. Available in page settings.
* Fix: Placeholder text warning.
* Fix: Close button not showing if auth_code is present in the ticket URL.
* Fix: Create ticket email notification fails if there are no recipients.

= 3.0.6 (August 12, 2022) =
* Fix: Email notications not sending for custom addresses.
* Fix: Email notifications not sending for new ticket assignee if agents are assigned from assign agent rules (Also needs to update Assign agent rules addon).
* Fix: Mine filter showing closed tickets.
* Fix: Bootstrap modal conflict.
* Fix: Thread paragraph does not have spacing.
* Fix: Conditions do not match correctly for Dropdown (Single) and Radio Button custom fields.

= 3.0.5 (August 08, 2022) =
* Fix: Ticket URL authentication setting added. Now tickets can be accessed directly via URL if ticket URL authentication is disabled.
* Fix: Visibility conditions not working for guest users.
* Fix: Auto-refresh not working properly.
* Fix: My Profile link not showing correctly on Safari browser.
* Fix: Email notifications not sending when there are more than 10 recipients in TO addresses.

= 3.0.4 (August 04, 2022) =
* New: Setting to enable or disable My Profile and Agent Profile in support portal.
* Fix: Custom login url does not work.
* Fix: More than 20 agents not shown in the agents list in the agent settings.

= 3.0.3 (August 03, 2022) =
* Fix: Error while creating ticket if subject and description is not included in the ticket form. Added default value.
* Fix: Fatal error if PHP version is less than 7.4. Instead of showing notice while upgrade, it was giving fatal error.

= 3.0.2 (August 01, 2022) =
* Fix: Fatal error on some installations.

= 3.0.1 (July 31, 2022) =
* Fix: Debug warnings for general settongs.
* Fix: Old attachment link error display.
* Fix: Update warning email send multiple times on some installations.

= 3.0.0 (July 29, 2022) =
* **IMPORTANT:** This is a major release, and should be tested in your staging environments before running on your live site.
* **Upgrade:** This update will ask you to perform database maintenance once installed. Please note that SupportCandy is in disabled mode. That means neither your customers can create tickets nor your agents can view/reply to tickets until you do this.
* **Warning:** Requires PHP version 7.4 and above.
* **Warning:** Requires WordPress version 5.6 and above.
* **Warning:** You must update all premium add-ons (if any). Addons must be installed and updated. Otherwise, you will lose data and settings related to the addons.
* **Warning:** If you took any paid customizations from us, it will not work.
* **Warning:** All hooks and filters are changed.
* **Warning:** Translations are subject to availability.
* **Warning:** We discontinued the REST APIs for some time. However, it will be back soon with a new version.
* **Warning:** User interface design is changed. Previous appearance settings will not work on the new interface.
* New: Custom tables added to the database.
* New: Overall performance improvement and optimization.
* New: New custom field types - Dropdown (multi-select) and File Attachment (Single)
* New: Select multiple file attachments at once in the File Attachment (Multiple) fields and ticket description attachment sections.
* New: Default value for custom fields.
* New: Placeholder for dropdown fields.
* New: Assigned Agent and Additional Recipients are available to insert in the create ticket form.
* New: AND and OR relationship added for all condition settings.
* New: All custom fields are available for visibility conditions.
* New: Date range setting for Datetime custom field type.
* New: Time range setting for Time custom field type.
* New: Customer fields that can be accessed across all customer tickets.
* New: Separate ticket form fields section so that you can choose which fields to add to the ticket form.
* New: Customer admin submenu where you can see all the customers and their total number of active tickets.
* New: New capabilities added in the agent role, such as WP Dashboard access, Creating tickets on others' behalf, Deleted filter access, and Edit customer info.
* New: Working hour settings for overall website(organization) and agents.
* New: Custom default filters for agents and customers.
* New: Default filter setting for agents and customers.
* New: Guests can see their ticket list via OTP (One Time Password).
* New: OTP(One Time Password) based authentication for user registration and guest login.
* New: TO, CC and BCC added to the email notification recipients.
* New: Open ticket shortcode added. Users can open the ticket using ticket id and OTP authentication.
* New: My Profile section added to the support portal where users can edit their customer fields.
* New: Thread email visibility setting for agents.
* New: More control on who can close the ticket.
* New: Google reCaptcha V3 support.
* New: Permanently delete the deleted tickets after x number of days/months/years.
* New: Add CC emails in the reply.
* New: Separate rich-text editor settings for agents.

= 2.3.1 (March 23, 2022) =
* Fix: Thread seen showing incorrect date time.
* Fix: Ticket personal data get erasesed in some cases.
* Fix: Appreance setting not applied for filters.
* Fix: Open ticket view makes untidy if log text added.

= 2.3.0 (January 12, 2022) =
* Fix: Edit status in settings not working
* Fix: Datepicker not working if future/past option is set

= 2.2.9 (January 11, 2022) =
* Fix: User registration not working
* Fix: Reset ticket id setting not working
* Fix: Biographical info widget not showing the information
* Fix: {ticket_history_all_with_logs} showing the utc timings
* Fix: /tickets/{id}/updateFields API does not update name and email of customer

= 2.2.8 (January 05, 2022) =
* Fix: Not sending email notifications after reply

= 2.2.7 (December 29, 2021) =
* Fix: CSRF Vulnerability
* Fix: Cross-Site scripting Vulnerability
* Fix: Removed SupportCandy external cron settings for security reasons and shifted all external crons to default cron. If your WP Cron is disabled, you need to set manual cron from your hosting provider to wp-cron.php file.
* Fix: Removed file_get_contents() on remote urls and changed it to wp_remote_get() HTTP API of WordPress.
* Fix: Sanitized all possible user inputes which was not done before.
* Fix: Escaped all possible HTML output.

= 2.2.6 (November 23, 2021) =
* Fix: Uncheck the selected tickets if auto-refresh is ON
* Fix: Agent name not showing in ticket list if the ticket is created on behalf of the customer
* Fix: Backslash (\) are removed from ticket reply
* Fix: Unable to filter tickets of a single date
* Fix: Translation improvements

= 2.2.5 (October 07, 2021) =
* Fix: Bulk delete tickets without authentication

= 2.2.4 (September 14, 2021) =
* Fix: Datetime sliders not working for touch enabled devices
* Fix: Upload attachment not working in some cases
* Fix: Avada theme CSS conflict
* Fix: Emails are not sending with for mailster service
* Fix: Translation improvements

= 2.2.3 (July 19, 2021) =
* Fix: Unable to upload attachments due to cache
* Fix: Unable to change font of ticket list
* Fix: Few small bug fixes

= 2.2.2 (May 29, 2021) =
* Fix: Attachments not downloading. Conflict with HelpGuru Knowledgebase plugin resolved.
* Fix: WooCommerce dashboard ticket url not working

= 2.2.1 (April 26, 2021) =
* New feature: Setting added to decide ticket reporter when a ticket is created on behalf of the user
* New feature: Create assign agent rules depending on reporter wordpress role
* New feature: Search bar added to search fields in edit ticket fields
* Fix: Remove attachments when a ticket is permanantly deleted
* Fix: Conflict with myCred plugin 
* Fix: Date created filter excludes "to" date
* Fix: Additional recipient not saving
* Fix: API /tickets/addRegisteredUserTicket adds space in description text

= 2.2.0 (March 03, 2021) =
* New feature: Strikethrough, text color, text backgruond color options added in editor
* New feature: Restrict reply setting enabled for closed group
* New feature: Agent created ticket first thread will be agent itself instead customer
* New feature: "Allow create ticket" setting apply only for browser
* New feature: Number of digits for random ticket id setting added
* Fix: Download attachments not working if WordPress URL is different from Site URL
* Fix: Translation improvments for ticket filters
* Fix: Date format setting not applied on "Last reply on" column
* Fix: Date created Filter is not showing correct results for local timezone

= 2.1.9 (December 28, 2020) =
* New feature: Added "Last reply by" in ticket list
* New feature: Added "Last reply on" in ticket list
* New feature: Setting to hide New Ticket button for agents and customers
* New feature: Added Copy Ticket URL button in an individual ticket
* New feature: Date range option added in Date custom field type
* New feature: Attachment size threshold error improvemets
* New feature: Added None option in agent search advanced filter 
* New feature: Customer redirected to ticket list upon closing the ticket
* New feature: Option to send email notification to current user
* Fix: Notice mime_content_type() on file that does not exists

= 2.1.8 (October 28, 2020) =
* New feature: Allow wildcard character (*) in an outgoing email notification block list 
* New feature: Add URL to New Ticket button so that it redirects to it when we click New Ticket button. Applicable only for frontend.
* Fix: Security fixes
* Fix: Compatibility with MySQl Version 8
* Fix: Administrator unable to save cron setting
* Fix: Re-notify existing agents when another agent is added
* Fix: URLs being removed if ticket reply/note submitted via REST API

= 2.1.7 (September 19, 2020) =
* New feature: Add visibility condition for description
* New feature: New macro customer_first_name, last_reply_user_first_name, current_user_first_name, last_note_user_first_name added
* Fix: Unable to download attachment on some servers
* Fix: Image uplaod not working on windows servers
* Fix: Persian translation file 

= 2.1.6 (August 12, 2020) =
* Fix: Unable to submit reply if bcc field is disabled
* Fix: Italian translation file 

= 2.1.5 (August 11, 2020) =
* New feature: Add log after deleting file attachment from a thread
* New feature: Add visibility condition for priority
* New feature: Enable/disable reply to public tickets
* Fix: Mine filter showing all tickets
* Fix: Translation bug in the email notification
* Fix: Custom field Date range increase
* Fix: Category cannot be updated when using the REST API
* Fix: New lines removed from the description in reply ticket REST API
* Fix: Unable to create non-english Agent only fields
* Fix: Email validation for BCC field in the reply form
* Fix: Does not clear all the data after a ticket deleted permanently
* Fix: Backslaces removed from the reply

= 2.1.4 (July 03, 2020) =
Fix: Vulnerability fixes
Fix: Translation issue fix for some strings
Fix: CSS conflict with Twenty Twenty theme resolved
Fix: Conflict with registration form fields
Fix: Tickets assigned to agent group not visible in Mine filter
Fix: Agents not able to see deleted tickets

= 2.1.3 (May 14, 2020) =
* Fix: Default filter not working after login
* Fix: Sign-in not working in some cases
* Fix: Vulnerability fixes
* Fix: GDRP compatibility added for registration form

= 2.1.2 (April 03, 2020) =
* New feature: Setting to allow HTML/Text pasting in create, reply description field
* Fix: Vulnerability fixes for attachments
* Fix: Performance improvements. Working slow on some servers
* Fix: Some administrators can't save thank you page setting
* Fix: The agent only fields do not get imported in clone ticket
* Fix: Email notification not send for user registration when registered via supportcandy

= 2.1.1 (February 17, 2020) =
* New feature: You can now edit saved filters
* New feature: You can edit attachment notice in settings
* Fix: Description image upload not working for guest users
* Fix: Attachment download not working on mobile devices
* Fix: Agent not being treated as a customer if he raised a ticket
* Fix: Conflict with plugin Woocommerce Product Addons
* Fix: Filter autocomplete suggestion showing deleted ticket records
* Fix: Returns undefined message when the description is disabled from create ticket form
* Fix: Do not notify owner conflict when disabled from settings
* Fix: Image attachment download setting not working since the last version

= 2.1.0 (January 08, 2020) =
* New feature: New custom field type Time added
* New feature: Agents can set a default filter for ticket list 
* New feature: Choose to whom attachment should be accessible in create or reply ticket description.
* New feature: Allow rich text editor setting using which you can either allow or disallow guest or register the functionality of rich text editor.
* New feature: Assign agent rules: not match condition added
* New feature: Restrict date custom field to either past, future or all dates
* New feature: Change email notication sending preferance (Background/Instant)
* Fix: Rich text editor RTL based on language

= 2.0.9 (November 18, 2019) =
* New feature: New custom field HTML added
* New feature: The administrator can delete attachments of a ticket thread
* New feature: reCaptcha setting added to SupportCandy login form
* New feature: The administrator can hide BCC field in reply form
* New feature: Close ticket group setting added so that those tickets will be available in Closed filter on ticket list page
* Fix: User registration email not sending after the user has been registered (Setting to register guest user after creating a ticket)
* Fix: Model popup not working on create ticket shortcode
* Fix: Email notification not working

= 2.0.8 (October 11, 2019) =
* Fix: Fatal error while sending an email notifications

= 2.0.7 (October 09, 2019) =
* New Feature: Emails will get send in the background which will improve loading speed
* New Feature: New email recipient option (Previously Assigned Agent) has been added
* New Feature: Ticket url link added in ticket info
* Fix: Visibility condition not working for usergroup default category selected
* Fix: Attachment type not downloading in an email notification
* Fix: Date Closed macro not working
* Fix: Registration not working when SupportCandy captcha enabled

= 2.0.6 (August 30, 2019) =
* New Feature: Limit the number of characters for ticket fields.
* New Feature: Setting to delete tickets after the given time interval.
* New Feature: New filter for ticket list to filter tickets created by either registered user or guest users.
* New Feature: Add placeholders for ticket fields in create ticket form.
* New Feature: New widget Bio (Biographical info) added in open ticket.
* New Feature: Control ticket logs visibility settings. Now you can edit role and set ticket log visibility controls.
* New Feature: Control edit or delete ticket thread permission for a user role.
* New Feature: reCaptcha added for SupportCandy user registration form.
* New Feature: Control attachment types for a ticket. Only accept attachment of the given extension.
* Fix: XSS Vulnerability fix for create, reply, and notes.
* Fix: Assign agent search not working for more than one search terms.
* Fix: Automatic login guest user after ticket created. This works if setting Register user on creating ticket is enabled.
* Fix: Assign agent search results not in order.

= 2.0.5 (August 1, 2019) =
* New Feature : New micros added ticket_history_all, ticket_history_all_with_notes, ticket_notes_history
* Fix: Unable to upload more than 1 file in File upload custom field
* Fix: Backslash get added in email notifications 
* Fix: Unable to create a ticket on safari browser when description field is disabled
* Fix: Can't change raised by if (single quote) in name
* Fix: Auto refresh clears the filters
* Fix: Registration form not working in French (France) language

= 2.0.4 (June 12, 2019) =
* New Feature : Hooks added for agent role capabilities.
* Fix: Admin capabilities not working.

= 2.0.3 (June 6, 2019) =
* New Feature : REST API
* New Feature : New custom field type(Datetime) added
* New Feature : HTML Mode available for Email notification, Thank you page and Agent setting templates
* New Feature : Setting to Enable/disable reply to close tickets for Agents
* New Feature : Register user upon create ticket if not exists (Guest User)
* New Feature : Default status of auto-refresh on ticket list on page load
* New Feature : Option to create a new ticket from ticket thread (reply/note)
* New Feature : Ticket list item added: Date Close. Now you can filter/order ticket by close date of ticket.
* New Feature : Setting to download or show image attachments. So that if someone do not want to show image path, he can choose download option.
* New Feature : Alert for complete ticket reply when you accidentally do any other ticket action, and forgot to save your reply.
* New Feature : Agents can see all the tickets of the raised by user from open ticket
* New Feature : Agents can see extra information of ticket like IP Address, Browser, Operating System.
* New Feature : Agents can see extra information of reply like IP Address, Browser, Operating System and time when user saw your reply.
* New Feature : You can add extra email addresses and usergroups to a ticket. All the notifications will be send to added recipients.
* New Feature : Setting to set screen to which user/agent should be redirected after successful ticket reply.
* New Feature : Set default text for subject and description if disabled.
* New Feature : Private note capability setting added for support agent role.
* Fix : Validation not working for url and file upload type custom field.
* Fix : Translation issues fixed with WPML. Now ticket form fields can be translated in multiple languages such as Field Label, Extra Information, Statuses, Categories, Priorities, etc.
* Fix : Superuser not able to see tickets on multisite blogs.
* Fix : Unable to update agentonly fields(drop-down type) in open ticket

= 2.0.2 (April 19, 2019) =
* New Feature : Ticket submitted by agent or user himself option added in conditions.
* New Feature : User Type registered or guest user option added in conditions.
* Fix : Custom Field visibility conditions not working after v2.0.1

= 2.0.1 (April 17, 2019) =
* New Feature : Condition improvements. Now you can add conditions for all possible custom fields (text based and options based) in email notifications, SLA, Assign Agent Rules, etc.
* Fix : Arbitrary File Upload in create/reply description image upload ( CVE-2019-11223 )
* Fix : Reply button not visible in guest open ticket page if public ticket mode enabled
* Fix : ID filter does not work for Spanish (Maxico) site language
* Fix : Translation issue for Polish language

= 2.0.0 (April 4, 2019) =
* New Feature : Database performance improvements
* New Feature : Enable/Disable file attachment for create and reply description
* New Feature : Enable/Disable priority for customer in an individual ticket status section
* New Feature : Enable/Disable View More for individual ticket threads
* New Feature : All Tickets filter added in customer ticket list page
* New Feature : Always Notify default setting
* New Feature : All actions and filters arguments changed
* Fix : Ticket list ordering not working for German language 
* Fix : Avada theme css conflict 
* Fix : Custom field filters not working for Non-English characters in label of a custom field

= 1.1.5 (February 13, 2019) =
* New Feature : Apperance setting for open ticket threads
* New Feature : Add image in agent signature
* New Feature : Setting to Enable/Disable 'Don't notify owner' option in create ticket form
* New Feature : Show open ticket thread update timing in timestap or string format
* Fix : Css conflict with Avada theme for mobile view
* Fix : Customers not able to save filters 
* Fix : Attachments don't come through create ticket email notifications 
* Fix : Reset Filter not working for customers

= 1.1.4 (January 8, 2019) =
* New Feature : Setting to show or hide filters on page load
* New Feature : Add multiple recipients(emails) while replying to ticket
* New Feature : Vulnerability fixes
* New Feature : Autogrow reply description field 
* New Feature : First and Last name added in Sign-up form
* New Feature : Ticket category change notification added
* New Feature : Ticket priority change notification added
* New Feature : Setting to disable reply confirmation added
* New Feature : Custom set start ticket id
* New Feature : Browse and add images in ticket reply
* New Feature : Setting for timestamp on ticket thread to switch between timestamp and readable format (e.g. x min ago)
* New Feature : Setting to enable/disable toolbar options of tinymce editor
* Fix : Start and Dazzling theme conflict
* Fix : Attachment download issue on firefox
* Fix : Incorrect order of ticket fields in edit popup
* Fix : Custom field not showing updated name in ticket widget
* Fix : Unorder list item dots not showing in ticket thread
* Fix : Unable to remove agent signature
* Fix : Some small bug fixes

= 1.1.3 (November 26, 2018) =
* New Feature : Public Tickets. Customers can see each other's tickets if setting is enabled.
* Fix : Create Ticket not working in safari browser.
* Fix : Create Ticket not working if Description field is disabled.
* Fix : Save filters not working for Agent role. 
* Fix : Custom fields order is not same as create ticket in open ticket.

= 1.1.2 (November 15, 2018) =
* New Feature : Google reCaptacha integration for create ticket page.
* New Feature : Don't Notify Owner option in create ticket page for agents to create ticket on behalf of customer.
* New Feature : Setting to change Term & Conditions text.
* New Feature : Add previous value of field in ticket log.
* Fix : Css conflict with Avada theme resolved.
* Fix : Conflict with IP Geo Block plugin resolved.
* Fix : Ticket status does not change when support staff reply by email.
* Fix : Advance filters are not showing in Firefox.

= 1.1.1 (October 25, 2018) =
* New Feature : Setting to disable description attachments for guest ticket form.
* Fix : Performance improvements.

= 1.1.0 (October 19, 2018) =
* New Feature : Permanent delete ticket feature
* New Feature : Setting to manage ticket url visibility. Ticket URL can be set to require login to see ticket content.
* New Feature : Log out button added on ticket list action bar.
* Fix : Performance improvements.
* Fix : Condition rules does not work for same ticket fields.
* Fix : Deleted ticket should not be accesible via ticket url.
* Fix : Unable to use $ sign in ticket notification emails.
* Fix : Add image link button hides under pop-up in agent settings.
* Fix : Ticket status does not change for awaiting agent reply status if user reply via ticket url page in guest mode.

= 1.0.9 (October 06, 2018) =
* Fix : Conflict with some Page-Builders while saving pages.

= 1.0.8 (October 01, 2018) =
* New Feature : GDPR Compatibility. Export personal data, ananymise ticket data for erase personal request, personal data retention, setting to set whether custom field is accept personal data, etc. Export and erase feature is available in core wordpress tool for GDPR.
* New Feature : Show unresolved ticket count on dashboard menu "Support".
* New Feature : Apperance setting for advance filter.
* Fix : Attachments are not sending in email notifications.
* Fix : Unable to create ticket on French language site.
* Fix : Ticket widgets are not showing in some cases.

= 1.0.7 (September 15, 2018) =
* New Feature : New shortcode [wpsc_create_ticket]
* New Feature : User registration form.
* New Feature : Open ticket widget re-order setting.
* New Feature : Setting to disable rich text editor for guest users.
* New Feature : Apperance setting for login & registration form.
* Fix : Date filter not showing correct results.
* Fix : Create ticket validation issue on Firefox browser.

= 1.0.6 (September 07, 2018) =
* New Feature : Apperance Setting added. Now you can change custom color combinations for SupportCandy features & pages.
* New Feature : Term & Conditions checkbox in create ticket to force customer to agree your Terms & Conditions.
* New Feature : GDPR consent as checkbox to let customer know that you are collecting private data. He must agree to your privacy policy to create a ticket.
* New Feature : Setting to disable reply if ticket is closed.
* New Feature : Multiple login options. You can choose login form from Default SupportCandy, WP Login or Custom login URL.
* Fix : Image overflow in ticket reply issue fixed.
* Fix : Not able to remove users from agent setting if agent is deleted from WP users section.
* Fix : Few Translation strings.
* Fix : Close button showing even if ticket is closed.
* Fix : Conflict with Post Type Order plugin for ticket orders.

= 1.0.5 (August 31, 2018) =
* Fix : Warning: Illegal string offset ‘page’ on support page.

= 1.0.4 (August 28, 2018) =
* New Feature : Shortcode Argurment to load default ticket list or create ticket
* New Feature : Auto-Refresh ticket list & Refresh button in individual ticket.
* New Feature : Priority can be made available in create ticket for client to choose. ( Default: Disabled )
* Fix : Ticket field rename issue
* Fix : Garbage collection for unlinked attachments.
* Fix : Ticket widget overflow issue.
* Fix : Date type field issues
* Fix : Attachment link issues
* Fix : Email Notification default subject change.

= 1.0.3 (August 20, 2018) =
* Responsive issues fixed for many themes.

= 1.0.2 (August 18, 2018) =
* CSS issues fix.
* Reply actions for agent view is splited to show separate buttons.

= 1.0.1 (August 17, 2018) =
* Date filter fix
* Incorrect cron command fix

= 1.0.0 (August 15, 2018) =
* Initial release.
