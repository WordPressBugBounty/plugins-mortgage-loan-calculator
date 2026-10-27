=== Mortgage Calculator ===
Tags: mortgage-calculator, loan-calculator, mortgage, calculator, real-estate
Contributors: mlcalc
Requires at least: 5.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.6.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Mortgage and loan calculator widget with shortcode: PMI, property tax, insurance, amortization chart and schedule. 8 languages.

== Description ==

This plugin adds a mortgage and loan calculator to your WordPress site, as a Gutenberg block, a sidebar widget or the `[mlcalc]` shortcode. Visitors enter the purchase price or loan amount, down payment, rate and term, then press Calculate to see a summary, a chart and an amortization schedule in a popup. It is built for mortgage loan officers, real-estate agents and finance or home-buying sites.

The calculation and the results popup are provided by the [MLCALC.com](https://www.mlcalc.com/mortgage-calculator/ "Mortgage Calculator") service, which means the calculator needs an internet connection and sends the entered values there when Calculate is pressed (see External services below).

= Features =

* Gutenberg block with a live preview and sidebar settings
* Mortgage calculator with property tax, home insurance and PMI
* Loan calculator for general loans
* Summary, chart and amortization schedule (by month or by year) in a popup window
* Choose which calculators to show: mortgage and loan, mortgage only or loan only
* Narrow (150px) or wide (300px) form, suitable for sidebars and page content
* Preset default values with shortcode attributes or in the widget settings
* Currency: US dollar, euro, pound or a currency code of your own
* Interface languages: English, German, Spanish, French, Italian, Russian, Portuguese and Albanian
* CSS, JavaScript and images are bundled with the plugin and loaded from your own server
* Free for commercial use

= How to use =

In the block editor, click the "+" button, search for "Mortgage Calculator" and add the block; the sidebar lets you pick the calculators, size, currency, language and default values.

Or add the shortcode to a page or post:

`[mlcalc]`

Set default values:

`[mlcalc default="mortgage" size="narrow" price="125,000" rate="6.2" language="es" currency="eur"]`

You can also add the "Mortgage Calculator" widget under Appearance > Widgets, or call `<?php display_mlcalc(); ?>` from a theme template.

= Shortcode parameters =

* default = mortgage | loan | mortgage_only | loan_only
* size = wide | narrow
* currency = usd | eur | gbp | any code of your own
* rate = interest rate
* price = purchase price
* mterm = mortgage term in years
* down = down payment in percent
* tax = property tax
* insurance = property insurance
* pmi = PMI in percent
* amount = loan amount
* lterm = loan term in years
* schedule = month | year | month_nc | year_nc | none (_nc stands for "no choice" for the visitor)
* language = en | de | es | fr | it | pt | ru | al

= Shortcode examples =

`[mlcalc]`

`[mlcalc default="mortgage_only"]`

`[mlcalc default="loan_only" amount="20,000" lterm="5"]`

`[mlcalc default="mortgage" size="narrow" price="125,000" rate="6.2" language="es" currency="eur"]`

== Installation ==

1. Upload the `mortgage-loan-calculator` directory (including all files within) to the `/wp-content/plugins/` directory.
2. Activate the mortgage calculator plugin through the 'Plugins' menu in WordPress.

= Usage =

* If you are using widgets, just add the Mortgage Calculator to the sidebar through the `Appearance -> Widgets -> Mortgage Calculator` menu in WordPress.
* Add the following code: `<?php display_mlcalc(); ?>` to your template where you would like the mortgage loan calculator form to appear.
* Add the shortcode `[mlcalc]` to your page or post and configure default mortgage parameters.

== External services ==

This plugin relies on the MLCALC.com calculator service to compute and display results. When a visitor presses "Calculate", the values typed into the form (purchase price or loan amount, down payment, term, interest rate, property tax, insurance, PMI, start date, currency and language) are sent to https://www.mlcalc.com/ and the results page opens in a popup window served by mlcalc.com. Nothing is sent until the visitor submits the form, and no personal data is collected. The service is provided by ARSIDIAN LLC: [Terms of use](https://www.mlcalc.com/terms.htm), [Privacy policy](https://www.mlcalc.com/privacy.htm).

== Changelog ==

= 1.6.1 =
* Fixed: the results popup cut off the bottom of the results; it now fits the window and scrolls when needed.

= 1.6.0 =
* Gutenberg block: add the calculator in the block editor with a live preview and sidebar settings.
* Translation-ready: the admin texts can be translated on translate.wordpress.org.
* A review reminder in the admin after two weeks, dismissible.

Older versions are listed under Upgrade Notice.

== Screenshots ==

1. The mortgage calculator form with property tax, insurance and PMI fields.
2. The result: monthly payment breakdown, loan balance chart and payment totals.
3. The Mortgage Calculator block in the block editor, with its settings in the sidebar.

== Upgrade Notice ==

= 1.6.1 =
Fixes the results popup, which cut off the bottom of the results. It now fits the window and scrolls when needed.

= 1.6.0 =
Adds a Mortgage Calculator block for the block editor, makes the admin texts translatable and adds a dismissible review reminder.

= 1.5.24 =
Fixes the loan calculator popup, which showed the plain mlcalc.com loan page instead of the results. Results now use the widget's currency.

= 1.5.23 =
All output escaped, fields set to "null" are hidden again, widget title no longer turned into a slug, license and external service documented.

= 1.5.22 =
Security fix: shortcode attributes are now sanitized before output (CVE-2025-0805).

= 1.5.21 =
Tested with WordPress 7.1. Calculator links now carry rel="nofollow".

= 1.5.20 =
Tested with Wordpress 6.4.0.

= 1.5.19 =
Tested with Wordpress 5.9.4.

= 1.5.18 =
Tested with Wordpress 5.9.3.

= 1.5.17 =
Security XSS issue fixed for shortcode.

= 1.5.16 =
Security XSS issue fixed for shortcode.

= 1.5.15 =
Switched to calling file locations properly, using plugins_url().

= 1.5.14 =
Security fix. Added sanitizing for all input fields.

= 1.5.13 =
Security XSS issue fixed for shortcode.

= 1.5.12 =
Small fix.

= 1.5.11 =
Small fix.

= 1.5.10 =
Increased font size.

= 1.5.9 =
HTML fixes.

= 1.5.8 =
Wordpress 5.6.1 tested. Some bugs fixed.

= 1.5.7 =
Small fixes.

= 1.5.6 =
CSS bugs fixed. Optimizations for mobile devices.

= 1.5.5 =
Small fixes and tests.

= 1.5.4 =
Small HTML fixes.

= 1.5.3 =
Small fix for Internet Explorer 10.

= 1.5.2 =
Small fixes.

= 1.5.1 =
Small interface fixes.

= 1.5 =
Now all images, CSS and JavaScript are bundled with the plugin and loaded from your own server.

== Frequently Asked Questions ==

= How do I add the mortgage calculator to a page? =

In the block editor add the "Mortgage Calculator" block and set its options in the sidebar. You can also paste `[mlcalc]` into the page or post (or into a Shortcode block). To start with specific values, add attributes, for example `[mlcalc default="mortgage" price="400,000" rate="6.5" down="20"]`.

= How do I show only the mortgage calculator, or only the loan calculator? =

Use `default="mortgage_only"` or `default="loan_only"`. With `default="mortgage"` or `default="loan"` both calculators are available and the chosen one is shown first.

= Does it calculate PMI and property tax? =

Yes. The mortgage form has fields for property tax, home insurance and PMI, which you can preset with the `tax`, `insurance` and `pmi` attributes.

= Which languages and currencies are supported? =

English, German, Spanish, French, Italian, Russian, Portuguese and Albanian, set with the `language` attribute (or the widget settings). Currencies: `usd`, `eur`, `gbp`, or any currency code of your own.

= Does it send data to an external service? =

Yes. When a visitor presses Calculate, the numbers typed into the form are sent to MLCALC.com, which returns the results popup. Nothing is sent before that and no personal data is collected. See the External services section for details and links.

= Does it work offline? =

No. The results are produced by MLCALC.com, so the visitor needs an internet connection.

= May I use your widget on my commercial website =

Yes. Absolutely.
