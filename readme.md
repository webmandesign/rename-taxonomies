# Rename Taxonomies by WebMan

**Customize the text labels or menu names for any registered taxonomy using a simple interface.**


## Description

This plugin allows you to customize any taxonomy labels with simple interface and no coding required!

The customized taxonomies are renamed only, no registration keys or IDs are changed, so you don't loose any registered taxonomy by renaming it with this plugin.

### Features

* Easily rename any taxonomy (rename *"Categories"* to *"Topics"*, for example)
* Renames custom taxonomies as well
* Simple, intuitive user interface
* No coding required
* Translation ready
* WPML multilingual plugin compatible

### Plugin Localization

Translate the plugin at its [WordPress.org repository](https://wordpress.org/plugins/rename-taxonomies/): click the "Translate this plugin" button under the "Translations" section in sidebar.

### Additional Resources

* [Write a review](https://wordpress.org/support/view/plugin-reviews/rename-taxonomies/#postform)
* [Have a question?](https://wordpress.org/support/plugin/rename-taxonomies/)
* [Grab a free theme](https://profiles.wordpress.org/webmandesign/#content-themes)
* [Follow @webmandesigneu](https://twitter.com/webmandesigneu/)
* [Visit WebMan Design](http://www.webmandesign.eu)


## Installation

1. Unzip the plugin download file and upload `rename-taxonomies` folder into the `/wp-content/plugins/` directory.
2. Activate the plugin through the *"Plugins"* menu in WordPress.
3. Rename taxonomies under **Tools &raquo; Rename Taxonomies**.


## Frequently Asked Questions

### How can I rename taxonomy labels?

Navigate to **Tools &raquo; Rename Taxonomies** in your WordPress dashboard. Then click the taxonomy you want to rename and fill the form displayed. That's it. Note that you might need to refresh the WordPress dashboard once the form is saved to preview the changes.

### My taxonomies are renamed in admin but not in front-end of my website!

Well, this is most likely caused by your theme (or a plugin) hard-coding the taxonomy name. Please contact your theme (or a plugin) developer to update their code and use WordPress taxonomy labels instead of hard-coding it.

### Is this plugin translation ready?

Yes, the interface of the plugin is translation ready and you are perfectly fine to use it on single-language website. For multilingual website please read below. Translate the plugin by clicking the **"Translate this plugin"** button under the "Translations" section in the sidebar of the plugin WordPress repository page.

### Does the plugin work with multilingual website and WPML plugin?

If you are building a multilingual website and want to translate the customized taxonomy labels, this is possible using [**WPML**](https://wpml.org/) or [**Polylang**](https://wordpress.org/plugins/polylang/) multilingual plugin. Please note that if you already have customized your taxonomies labels, you need to resave those to register them for translation with the above multilingual plugins. Other solution would be to [use WordPress multisite approach to build a multilingual website](https://wordpress.tv/2016/01/16/alexandre-simard-elise-desaulniers-multilingual-content-wp/).

### But there is no setting for `post_format`! How can I change that?

Well, certain WordPress native taxonomies are not available for customization by default. The list consists of `link_category`, `nav_menu` and `post_format`. In case you wanted to edit this list, use a `rename_taxonomies_skipped_keys` filter hook. The custom taxonomies that have no (empty) taxonomy name will be skipped too.
