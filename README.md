# WP Markdown Exporter — v0.0.1

Export selected posts and pages as Markdown files bundled in a ZIP archive. This release changes the export behavior to always use the user-selected source field (no automatic fallback), and exported files are always saved with a `.md` extension.

## Highlights (v0.0.1)

- **Field Selection** — Choose which field to export: `post_content_filtered` (default), `post_content`, or `meta` (searches common markdown meta keys).
- **No Fallback** — If the chosen field is empty for a post, an empty `.md` file will be created for that post.
- **Unified .md Files** — All exported files use the `.md` extension.
- **Filtered List** — The admin list shows only posts with non-empty `post_content`.

## Requirements

- WordPress 4.7 or higher
- PHP 7.0 or higher

## Installation

Install and activate the plugin as a normal WordPress plugin (Upload via **Plugins → Add New** or copy the folder to `/wp-content/plugins/`).

## Usage

1. Go to **Tools → Markdown Export** in the WordPress admin.
2. Select posts/pages to export (the list shows only posts with non-empty `post_content`).
3. Choose the export field from the dropdown (`post_content_filtered` is selected by default).
4. Optionally include post IDs in filenames.
5. Click **Export as ZIP**.

The ZIP will contain `.md` files named from the post title (optionally suffixed with the post ID). For `meta` field exports the plugin will check common meta keys for raw Markdown.

## Supported Markdown Meta Keys (when exporting `meta`)

- `editor_md`
- `editormd`
- `editor_md_post`
- `markdown`
- `content_markdown`
- `wp_editor_md`
- `wpedmd_markdown`
- `raw_markdown`
- `md_content`
- `editor_md_content`
- `editor_md_text`

## Notes

- The plugin prefers `post_content_filtered` by default because many editors store cleaned/filtered content there. Choose `meta` to export raw stored Markdown when available.
- There is intentionally no HTML fallback; export behavior is predictable and based solely on the selected field.

If you want, I can also prepare a release ZIP and update a CHANGELOG file.
