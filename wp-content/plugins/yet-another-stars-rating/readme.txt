=== Yasr - Yet Another Stars Rating ===
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AXE284FYMNWDC
Tags: rating, rate post, rate page, star rating, google rating, votes
Requires at least: 5.0
Contributors: Dudo
Tested up to: 5.9.3
Stable tag: 3.0.4
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Boost the way people interact with your site with an easy WordPress stars rating system! With schema.org rich snippets YASR will improve your SEO

== Description ==
Improving the user experience with your website is a top priority for everyone who cares about their online activity,
as it promotes familiarity and loyalty with your brand, and enhances visibility of your activity.

Yasr - Yet Another Stars Rating is a powerful way to add SEO-friendly user-generated reviews and testimonials to your
website posts, pages and CPT, without affecting its speed.

== How To use ==

== Reviewer Vote ==
With the classic editor, when you create or update a page or a post, a box (metabox) will be available in the upper right corner where you'll
be able to insert the overall rating.
With the new Guteneberg editor, just click on the "+" icon to add a block and search for Yasr Overall Rating.
You can either place the overall rating automatically at the beginning or the end of a post (look in "Settings"
-> "Yet Another Stars Rating: Settings"), or wherever you want in the page using the shortcode [yasr_overall_rating] (easily added through the visual editor).

== Visitor Votes ==
You can give your users the ability to vote, pasting the shortcode [yasr_visitor_votes] where you want the stars to appear.
If you're using the new Gutenberg editor, just click on the "+" icon to add a block and search for Yasr Visitor Votes
Again, this can be placed automatically at the beginning or the end of each post; the option is in "Settings" -> "Yet Another Stars Rating: Settings".

== Multi Set ==
Multisets give the opportunity to score different aspects for each review: for example, if you're reviewing a videogame, you can create the aspects "Graphics", "Gameplay", "Story", etc.

== Migration tools ==
You can easily migrate from *WP-PostRatings*, *kk Star Ratings*, *Rate My Post* and *Multi Rating*
A tab will appear in the settings if one of these plugin is detected.

== Supported itemtypes ==
YASR supports the following schema.org itemtypes:

BlogPosting ✝,
Book ¶,
Course,
CreativeWorkSeason,
CreativeWorkSeries,
Episode,
Event,
Game,
LocalBusiness ‡,
MediaObject,
Movie Δ,
MusicPlaylist,
MusicRecording,
Organization,
Product §,
Recipe ||,
SoftwareApplication ◊

✝ BlogPosting itemtype will not show stars in search result.
More info [here](https://wordpress.org/plugins/yet-another-stars-rating/faq/)

¶ Book supports the following properties
* author
* bookEdition
* BookFormat
* ISBN
* numberOfPages

‡ LocalBusiness supports the following properties
* Address
* PriceRange
* Telephone

Δ Movie supports the following properties
* actor
* director
* Duration
* dateCreated

§ Products supports the following properties
* Brand
* Sku
* Global identifiers
* Price
* Currency
* Price Valid Until
* Availability
* Url

|| Recipe supports the following properties
* cookTime
* prepTime
* description
* keywords
* nutrition
* recipeCategory
* recipeCuisine
* recipeIngredient

◊ SoftwareApplication supports the following properties
* applicationCategory
* operatingSystem
* Currency
* Price Valid Until
* Availability
* Url

== Video Tutorial ==

= Old video, but still valid. =
[youtube https://www.youtube.com/watch?v=M47xsJMQJ1E]

= New videos, talked in Italian but easily to understand (eng subs will comes) =
[Tutorial's playlist](https://www.youtube.com/playlist?list=PLFErQFOLUVMcx8Qb9--KKme3bQ_KGri71)

== Developers ==
While YASR - Yet Another Stars Rating does not require any coding, it is developer friendly!
It is the first (and for now only) rating plugin that uses REST API.
[Here](https://documenter.getpostman.com/view/12873985/TzJycbVz) you can find the documentation.
Further, it comes with a lot of hooks, you can find more info [here](https://yetanotherstarsrating.com/docs/developers/) .

== Related Link ==
* [All available shortcodes in free version](https://yetanotherstarsrating.com/yasr-shortcodes/)
* Documentation at [Yasr Official Site](https://yetanotherstarsrating.com/docs/)

Do you want more feature? [Check out Yasr Pro!](https://yetanotherstarsrating.com/#yasr-pro)

== Press ==
[Tutorial by Qode Magazine](https://qodeinteractive.com/magazine/how-to-add-post-rating-to-your-wordpress-website/)
[Tutorial by Greengeeks](https://www.greengeeks.com/tutorials/star-rating-system-yasr-wordpress/)

== Installation ==
1. Navigate to Dashboard -> Plugins -> Add New and search for YASR
2. Click on "Installa Now" and than "Activate"

== Frequently Asked Questions ==

= What is "Overall Rating"? =
It is the vote given by who writes the review: readers are able to see this vote in read-only mode. Reviewer can vote using the box on the top right in the editor screen. Remember to insert this shortcode **[yasr_overall_rating]** to make it appear where you like.

= What is "Visitor Rating"? =
It is the vote that allows your visitors to vote: just paste this shortcode **[yasr_visitor_votes]** where you want the stars to appear.

[Demo page for Overall Rating and Vistor Rating](https://yetanotherstarsrating.com/yasr-shortcodes/)

= What is "Multi Set"? =
It is the feature that makes YASR awesome. Multisets give the opportunity to score different aspects for each review: for example, if you're reviewing a videogame, you can create the aspects "Graphics", "Gameplay", "Story", etc. and give a vote for each one. To create a set, just go in "Settings" -> "Yet Another Stars Rating: Settings" and click on the "Multi Sets" tab. To insert it into a post, just paste the shortcode that YASR will create for you.

[Demo page for Multi Sets](https://yetanotherstarsrating.com/yasr-shortcodes/#yasr-multiset-shortcodes)

= What is "Ranking reviews" ? =
It is the 10 highest rated item ranking by reviewer. In order to insert it into a post or page, just paste this shortcode **[yasr_ov_ranking]**

= Wht is "Users' ranking" ? =
This is 2 charts in 1. Infact, this chart shows both the most rated posts/pages or the highest rated posts/pages.
For an item to appear in this chart, it has to be rated twice at least.
Paste this shortcode to make it appear where you want **[yasr_most_or_highest_rated_posts]**

= What is "Most active reviewers" ? =
If in your site there are more than 1 person writing reviews, this chart will show the 5 most active reviewers. Shortcode is **[yasr_top_reviewers]**

= What is "Most active users" ? =
When a visitor (logged in or not) rates a post/page, his rating is stored in the database. This chart will show the 10 most active users, displaying the login name if logged in or "Anonymous" otherwise. The shortcode : **[yasr_most_active_users]**

[Demo page for Rankings](https://yetanotherstarsrating.com/yasr-shortcodes/#yasr-rankings-shortcodes)

= Wait, wait! Do I need to keep in mind all this shortcode? =
If you're using the new Gutenberg editor, you don't need at all: just use the blocks.
If, instead, you're using the classic editor, in the visual tab just click the "Yasr Shortcode" button above the editor

= Does it work with caching plugins? =
Since version 2.3.0 YASR works with *every caching plugin available out there*.
In the settings, just select "yes" to "Load results with AJAX".
YASR has been tested with:
* Wp Super Cache
* LiteSpeed Cache
* Wp Fastest Cache
* WP-Optimize
* Cache Enabler
* Hyper Cache
* Wp Rocket

= Why I don't see stars in google? =
[Read here](https://yetanotherstarsrating.com/yasr-rich-snippets/) and find out how to set up rich snippets.
You can use the [Structured Data Testing Tool](https://search.google.com/structured-data/testing-tool/u/0/) to validate your pages.
Also [read this](https://webmasters.googleblog.com/2019/09/making-review-rich-results-more-helpful.html) google announcement.
If you set up everything fine, in 99% of cases your stars will appear in a week.
If doesn't, you should work on your seo reputation.

= Does it work with PHP 8? =
Yes, YASR is 100% fully compatible with PHP 8

== Screenshots ==
1. Example of Yasr Overall Rating and Yasr Visitor Votes shortcodes
2. Yasr Multi Set
3. User's ranking showing most rated posts
4. User's ranking showing highest rated posts
5. Ranking reviews

== Changelog ==

The full changelog can be found in the plugin's directory. Recent entries:

= 3.0.4 =
* FIX: Author multiset didn't get saved
* TWEAKED: better error handling

= 3.0.3 =
* FIX: if parameter 'show_average' is enabled on a multi set, a duplicate query was run.
* TWEAKED: loader image has height and width attributes set.
* TWEAKED: minor changes


= 3.0.2 =
* Updated Freemius SDK

= 3.0.1 =
* Minor changes and new pricing page.

= 3.0.0 =
* Fix for an authenticated XSS vulnerability if 'Load results with AJAX?' is enabled

= 2.9.9 =
* TWEAKED: removed "Pro only features" box in the editor screen

= 2.9.8 =
* TWEAKED: updated rest api
* FIX: softwareApplication itemType didn't get saved
* Fix: impossible to rate for overall rating in certain circumstances.
* Fix: minor css fixes in admin area

= 2.9.7 =
* NEW FEATURE: auto insert option has now a new feature: it is possible to place shortcodes on both above and below the content
* TWEAKED: attribute 'custom-fields' is added for all CPTS, to avoid compatibility issue in Gutenberg
* TWEAKED: nonces are checked only for logged in users.
* TWEAKED: "Stats" page is now renamed in "Manage Ratings"
* TWEAKED: below the editor is now easier to manage Multi Sets


= 2.9.6 =
* FIXED: support for wp_template and wp_template_part CPT, used by FSE
* FIXED: html tags in custom texts didn't work


= 2.9.5 =
* NEW FEATURE: In the settings, is now possible to customize the text when a rating is saved / updated
* minor fixes

= 2.9.4 =
* Minor changes

= 2.9.3 =
* TWEAKED: Added support for Catch Infinite Scroll

= 2.9.2 =
* FIXED: rich snippets error that could occur in some circumstances
* FIXED: css fix
* TWEAKED: code cleanup

= 2.9.1 =
* FIXED: if shortcode exists for a post or page, but there are no ratings, wrong rich snippets are returned

= Additional Info =
See credits.txt file
