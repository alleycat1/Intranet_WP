<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Addons' ) ) :

	final class WPSC_Addons {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {}

		/**
		 * List of all supportcandy add-ons
		 *
		 * @return void
		 */
		public static function layout() {
			?>

			<style>
				@import url('https://fonts.googleapis.com/css2?family=Nunito&display=swap');

				body {
					font-family: 'Nunito', sans-serif;
					margin: 0;
				}

				.header {
					position: relative;
				}

				.inner-header {
					height: 100%;
					width: 100%;
					margin: 0;
					padding: 0;
				}

				.flex {
					/*Flexbox for containers*/
					justify-content: center;
					align-items: center;
					text-align: center;
				}

				section {
					min-height: 100%;
					position: relative;
					z-index: 10;
				}

				@media (min-width: 320px) and (max-width: 768px) {

					.header-title {
						font-size: 2.2em;
						color: #2C3E50;
						line-height: 1.1em;
					}

					.header-title-main {
						font-size: 3em;
						color: #2C3E50;
						line-height: 1.1em;
					}

					.header-subtitle {
						font-size: 1.5em;
						color: #171717;
						text-align: left;
						margin: 0em 0 1.4em 0;
					}

					.header-subtitle-main {
						font-size: 1.3em;
						color: #171717;
						margin: 0px 1em 3em 1em;
					}
				}

				@media (min-width: 769px) and (max-width: 1024px) {

					.header-title {
						font-size: 2.2em;;
						color: #2C3E50;
					}

					.header-title-main {
						font-size: 3em;
						color: #2C3E50;
					}

					.header-subtitle {
						font-size: 1.3em;
						color: #171717;
						text-align: left;
						margin: 0em 0 1.4em 0;
					}

					.header-subtitle-main {
						font-size: 1.5em;
						color: #171717;
						margin: 0px 1em 3em 1em;
					}
				}

				@media (min-width: 1025px) and (max-width: 1200px) {

					.header-title {
						font-size: 2.2em;;
						color: #2C3E50;
					}

					.header-title-main {
						font-size: 3em;
						color: #2C3E50;
					}

					.header-subtitle {
						font-size: 1.5em;
						color: #171717;
						text-align: left;
						margin: 0em 0 1.4em 0;
					}

					.header-subtitle-main {
						font-size: 1.3em;
						color: #171717;
						margin: 0px 1em 3em 1em;
					}
				}

				@media (min-width: 1201px) {

					.header-title-main {
						font-size: 3em;
						color: #2C3E50;
						margin: 1.2em 0 0.67em 0;
					}

					.header-title {
						font-size: 2.2em;
						color: #2C3E50;
						margin: 2em 0 0.4em 0;
						text-align: left;
					}

					.header-subtitle {
						font-size: 1.3em;
						color: #171717;
						text-align: left;
						margin: 0em 0 1.4em 0;
					}

					.header-subtitle-main {
						font-size: 1.5em;
						color: #171717;
						margin: 0px 1em 3em 1em;
					}
				}

				@keyframes move-forever {
					0% {
						transform: translate3d(-90px, 0, 0);
					}

					100% {
						transform: translate3d(85px, 0, 0);
					}
				}
				.wpsc-licenses-container button {
					margin-top: 1em;
				}
				.wpsc-licenses-container a {
					text-decoration: none;
					color: #3c434a;
				}
				.wpsc-licenses-container .license-container {
					cursor: pointer;
				}
				.wpsc-licenses-container .license-container:hover {
					-webkit-transform: scale(1.04);
					transform: scale(1.04);
					-webkit-transition: 0.2s ease;
					transition: 0.2s ease;
				}
				.wpsc-licenses-container .license-container {
					-webkit-transition: 0.2s ease;
					transition: 0.2s ease;
				}
			</style>

			<body>
				<section>
					<div class="header">
						<div class="inner-header">
							<div class="flex">
								<h1 class="header-title-main">Simple, straight forward pricing.</h1>
								<p class="header-subtitle-main">We believe in providing honest and fair pricing. Purchase in confidence with our 30-day money-back guarantee.</p>
							</div>
						</div>
					</div>
					<div class="header">
						<div class="inner-header">
							<div class="flex">
								<h1 class="header-title">Extensions</h1>
								<p class="header-subtitle">Supercharge your ticket system with our premium extensions available as add-ons so that you install them only if needed.</p>
							</div>
						</div>
					</div>
					<div id="wpsc-container">
						<div class="wpsc-licenses-container">
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/email-piping')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/email-piping.png" alt="">
								<p>Allows customers and agents to create and reply to tickets directly from their email inboxes.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/workflows')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/workflows.png" alt="">
								<p>Unlock the power of automation with SupportCandy workflows, revolutionizing the way you manage your processes.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/sla')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/sla.png" alt="">
								<p>You can offer and track the time you take to respond to and resolve different types of incoming tickets from customers.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/usergroups')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/usergroups.png" alt="">
								<p>You can create a group of users or companies so that the company’s supervisor can manage all tickets created by the group members.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/agentgroups')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/agentgroups.png" alt="">
								<p>You can create groups of agents to assign tickets just like individual agents. The supervisor of the group can assign tickets to his team members.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/satisfaction-survey')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/satisfaction-survey.png" alt="">
								<p>Collect customer feedback and rating for each ticket. This helps you understand how your team performs.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/timer')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/timer.png" alt="">
								<p>Allows your agents to separately record the time spent on each ticket in the form of a stopwatch.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/private-credentials')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/private-credentials.png" alt="">
								<p>Allows your customers to share sensitive information within the ticket so that it is visible to only agents with permission.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/schedule-tickets')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/schedule-tickets.png" alt="">
								<p>Automatically create periodic tickets by setting recurring time and information.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/canned-reply')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/canned-reply.png" alt="">
								<p>Agents can save their replies which can be accessed in just a few clicks while replying to the tickets.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/automatic-close-tickets')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/automatic-close-tickets.png" alt="">
								<p>Automatically close the ticket after x days of inactivity. You can also send an inactivity warning email to the customer before x days of closing the ticket.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/reports')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/reports.png" alt="">
								<p>Measure and improve the efficiency of your support using our advanced reporting.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/export-tickets')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/export-tickets.png" alt="">
								<p>Export tickets in CSV format as per the current filter from the ticket list page.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/print-ticket')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/print-ticket.png" alt="">
								<p>Add print ticket feature to SupportCandy using custom templates.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/assign-agent-rules')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/assign-agent-rules.png" alt="">
								<p>Conditionally assign agents to new tickets automatically using set rules and workload.</p>
							</div>
						</div>
					</div>
					<div class="header">
						<div class="inner-header">
							<div class="flex">
								<h1 class="header-title">Integrations</h1>
								<p class="header-subtitle">Seamlessly connect all your favorite tools and streamline your workflow with our powerful integrations.</p>
							</div>
						</div>
					</div>
					<div id="wpsc-container">
						<div class="wpsc-licenses-container">
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/woocommerce-integration')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/woocommerce.png" alt="">
								<p>Allows your customers to choose orders and products within the ticket form. Also, allows your agents to view customer orders within the ticket.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/slack-integration/')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/slack.png" alt="">
								<p>Get instant notifications to your Slack Channel and respond directly from Slack thread reply.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/gravity-forms-integration')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/gravity-forms.png" alt="">
								<p>Integrate Gravity Forms with SupportCandy and allows you to create multiple ticket forms.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/edd-integration')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/edd.png" alt="">
								<p>Allows your customers to choose orders and products within the ticket form. Also, allows your agents to view customer orders within the ticket.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/faq-integrations')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/faq-integrations.png" alt="">
								<p>Intergrates popular FAQ plugins with SupportCandy.</p>
							</div>
							<div class="license-container" onclick="window.open('https://supportcandy.net/downloads/knoledgebase-integrations')">
								<img src="http://localhost/wp-content/plugins/supportcandy/asset/images/knowledgebase-integrations.png" alt="">
								<p>Integrates popular knowledge-base plugins to SupportCandy.</p>
							</div>
						</div>
					</div>
				</section>
			</body>
			<?php
		}
	}
endif;

WPSC_Addons::init();
