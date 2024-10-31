=== Post and Page Specific Product Ads ===
Contributors: grrega
Donate link: https://grrega.com/projects/post-and-page-specific-product-ads
Tags: ads, advertising, advertise, woocommerce, product, e-commerce, ecommerce, post, page, category, specific
Requires at least: 4.4.0
Tested up to: 4.9.8
WC tested up to: 3.5.0
Requires PHP: 5.4
Stable tag: 1.0.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Advertise your products on your blog. This plugin allows you to select which products to show in a particular post, page or category.

== Description ==

<h4>Post and Page Specific Product Ads</h4>
<p><em>Advertise your products on your blog.<br />
</em></p>
<p>Post and Page Specific Product Ads allows you to select which products to show in a particular post, page or category.</p>
<p>Imagine you have a blog and a couple of products on sale on your website. You have an article which relates to one or more of your products. How do you show exactly that product on exactly that article page?</p>
<p>WooCommerce offers a widget that shows products but you can only filter them by featured products or on-sale products.</p>
<p><strong>That's where Post and Page Specific Product Ads comes in.</strong></p>
<p>With this plugin you can specify exactly which products to show in each post, page or category. You can also select the categories from which products will be shown.</p>
<p>Use views and clicks tracking to gather and analize statistical data.</p>
<p>You can choose to show products randomly or use views tracking to determine which product should be shown next (the one with the least views). This way you can be sure no ad you assign to the article wil be shown twice in a row.</p>
<p>For example: you can have three [papspa] shortcodes in an article and each of them will show a different product (assuming you have at least three ads assigned to the article). If the ads are retrieved randomly there is a good chance that one ad will be shown on more than one shortcode or that one ad will be shown more times than the others. But if the ads are retrieved by checking the statistics information, each shortcode will show a different ad.</p>

<h4>Features</h4>
<ul>
	<li>Set which products to show in each post/page/category</li>
	<li>Set categories from where products will be shown for each post/page/category</li>
	<li>Show random products if no products/categories are set, or don't show anything</li>
	<li>Select a product image size</li>
	<li>Show/hide product description</li>
	<li>Set product description length</li>
	<li>Show/hide product price</li>
	<li>Show/hide a "Read More" button</li>
	<li>Set text for the "Read More" button</li>
	<li>Show/hide on posts</li>
	<li>Show/hide on pages</li>
	<li>Show/hide on archive (category) page</li>
	<li>Show/hide on blog page</li>
	<li>Show/hide on search page</li>
	<li>Views and clicks tracking</li>
	<li>Quick stats column in post/page/category lists</li>
	<li>A very configurable shortcode</li>
	<li>Widget</li>
	<li>Currently works with WooCommerce</li>
	<li>Translation ready</li>
	<li>Lightweight</li>
</ul>

<h4>Settings</h4>
<p>You can find the settings page by visiting "Settings -> Post and Page Specific Product Ads"</p>

<h4>Shortcode</h4>
<p>Use the [papspa] shortcode to show ads anywhere on your site.</p>
<p>The shortcode accepts the following parameters:</p>
<ul>
	<li>show_description (true/false)</li>
	<li>show_price (true/false)</li>
	<li>show_button (true/false)</li>
	<li>show_on_post (true/false)</li>
	<li>show_on_page (true/false)</li>
	<li>show_on_blog (true/false)</li>
	<li>show_on_archive (true/false)</li>
	<li>show_on_search (true/false)</li>
	<li>button_text (string)</li>
	<li>desc_length (number of words)</li>
	<li>image_size (any WP registered image size)</li>
	<li>layout (horizontal/vertical)</li>
</ul>
<p>Using these parameters on the shortcode will override the default settings.</p>
<p>Example:</p>
<code>
	[papspa show_description="true" show_price="true" show_button="true" show_on_post="true" show_on_page="true" show_on_blog="true" show_on_archive="true" show_on_search="true" button_text="Click me!" desc_length="35" image_size="thumbnail" layout="horizontal"]
</code>

<h4>Support</h4>
<p>Contact me by <a href="https://grrega.com/contact">Grrega.com contact form</a> if you have any questions or need support.</p>
<p>You can also use the live chat on my website, find this plugins' support forum (look right) or contact me on any social website.</p>

== Installation ==

SERVER REQUIREMENTS

1. PHP version 5.4 or greater (PHP 7.2 or greater is recommended)
2. MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)


AUTOMATIC INSTALLATION

1. Log in to your WordPress dashboard, navigate to the Plugins menu and click Add New
2. In the search field type "Post and Page Specific Product Ads" and click Search Plugins
3. Install the plugin by simply clicking "Install Now"

MANUAL INSTALLATION

The manual installation method involves downloading the Post and Page Specific Product Ads plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains instructions on how to do this <a href="https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation">here</a>.

== FAQ ==

= How to override template files =

Template files are stored at the "/post-and-page-specific-product-ads/templates" folder.

1. Create a subfolder "/post-and-page-specific-product-ads" in your child themes folder.
2. Copy the template that you want to override from the plugins "/templates" folder to the new folder in your child theme
3. Edit the template

== Screenshots ==

1. Shortcode - vertical layout
2. Shortcode - horizontal layout
3. Widget in a sidebar
4. Widgets page
5. Settings page
6. Edit post screen
7. Edit page screen
8. Edit category screen
9. Posts list screen
10. Pages list screen

== Changelog ==

= 1.0.4 =
* FIX: fixed a typo
* FIX: added an extra check to prevent papspa columns showing up on non supported post types

= 1.0.3 =
* FIX: category ads not saving sometimes
* FIX: widget product selection wrong on certain pages
* FIX: completely removed the screen ID check from category list table

= 1.0.2 =
* FIX: fixed quick stats not always showing on post and page lists due to wrong screen id

= 1.0.1 =
* FIX: echo fix