=== Rename Taxonomies by WebMan ===

Contributors:      webmandesign
Donate link:       https://www.webmandesign.eu/contact/#donation
Author URI:        https://www.webmandesign.eu/
Plugin URI:        https://www.webmandesign.eu/portfolio/rename-taxonomies-wordpress-plugin/
Requires at least: 6.0
Tested up to:      6.9
Requires PHP:      7.0
Stable tag:        1.3.0
License:           GPL-3.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html
Tags:              taxonomy, name, label, category, tag

Customizes text and menu labels for any registered taxonomy using a simple interface.


== Description ==

This plugin allows you to customize any taxonomy labels with simple interface, no coding required. The customized taxonomies are only renamed, no registration keys are changed, so you won't cause any taxonomy conflicts using this plugin.

= Features =

* Easily rename any taxonomy (for example, rename *"Categories"* to *"Topics"*)
* Renames custom taxonomies as well (added by 3rd party plugins)
* Simple, intuitive user interface
* No coding required
* Translation ready
* Multilingual plugins compatible

= Plugin Localization =

You can [contribute your plugin translation](https://translate.wordpress.org/projects/wp-plugins/rename-taxonomies/) directly to WordPress. Thank you!

= Additional Resources =

* [Write a review](https://wordpress.org/support/plugin/rename-taxonomies/reviews/#postform)
* [Have a question?](https://wordpress.org/support/plugin/rename-taxonomies/)
* [Get an accessibility ready WordPress theme](https://www.webmandesign.eu/project-tag/free-wordpress-theme/)
* [Follow @webmandesigneu @ X/Twitter](https://x.com/webmandesigneu/)
* [Follow @webmandesigneu @ Facebook](https://www.facebook.com/webmandesigneu/)
* [Visit WebMan Design website](https://www.webmandesign.eu)


== Installation ==

1. Unzip the plugin download file and upload `rename-taxonomies` folder into the `/wp-content/plugins/` directory.
2. Activate the plugin through the *"Plugins"* menu in WordPress.
3. Rename taxonomies under **Tools &rarr; Rename Taxonomies**.


== Frequently Asked Questions ==

= How can I rename taxonomy labels? =

Navigate to **Tools &rarr; Rename Taxonomies** in your WordPress dashboard. Then click the taxonomy you want to rename and fill the form fields displayed. That's it.

(Note that you might need to refresh the WordPress dashboard once the form is saved to preview the changes.)

= My taxonomies are renamed in admin but not in front-end of my website! =

Well, this is most likely caused by your theme (or a plugin) hard-coding the taxonomy names on the front-end.

Please contact your theme (or plugin) developer to update their code and use [WordPress taxonomy labels](https://developer.wordpress.org/reference/functions/get_taxonomy_labels/) instead of hard-coding them.

= Is this plugin translation ready? =

Yes, the interface of the plugin is translation ready and you are perfectly fine to use it on a single-language website.

For multilingual website please read below (the other FAQ question).

You can [translate the plugin](https://translate.wordpress.org/projects/wp-plugins/rename-taxonomies/) directly in WordPress repository. Thank you!

= Does the plugin work with multilingual website (and plugins)? =

If you are building a multilingual website and want to translate the customized taxonomy labels, this is possible using [**WPML**](https://wpml.org/) or [**Polylang**](https://wordpress.org/plugins/polylang/) multilingual plugins.

Please note that if you have already customized your taxonomy labels, you need to re-save your customizations to register them for translation with the multilingual plugins.

Other solution would be to [use WordPress multisite approach to build a multilingual website](https://wordpress.tv/2016/01/16/alexandre-simard-elise-desaulniers-multilingual-content-wp/).

= But there is no setting for `post_format`! How can I change that? =

The plugin disables customization for certain WordPress native taxonomies. The list consists of `nav_menu`, `link_category`, `post_format`, `wp_theme`, `wp_template_part_area`, and `wp_pattern_category`.

In case you want to edit this list, use a `rename_taxonomies_skipped_keys` filter hook via your (child) theme or plugin PHP code:

`add_filter( 'rename_taxonomies_skipped_keys', function( $taxonomy_keys ) {

    unset( $taxonomy_keys['post_format'] );

    return $taxonomy_keys;
} );`

The taxonomies that have no admin edit UI will be skipped too.


== Screenshots ==

1. Taxonomies list
2. Editing a taxonomy labels
3. Customized taxonomy admin page (note the changed labels - taxonomy titles)
4. Setting up a customized taxonomy on post edit page (note the changed labels - taxonomy titles)


== Changelog ==

Please see the [`changelog.md` file](https://github.com/webmandesign/rename-taxonomies/blob/master/changelog.md) for details.


== Upgrade Notice ==

= 1.3.0 =
Fixing bugs and improving code.
