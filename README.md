# WP Markdown Exporter

A WordPress plugin that exports posts and pages as Markdown files in a ZIP archive. Perfect for backing up your content or migrating Markdown-formatted posts.

## Features

- **Bulk Export** – Select multiple posts/pages and export them in one click
- **Markdown Detection** – Automatically detects raw Markdown content from common meta keys (`editor_md`, `editormd`, `markdown`, `raw_markdown`, and more)
- **HTML Fallback** – Posts without stored Markdown are exported as `.html` files using `post_content`
- **Flexible File Naming** – Optional inclusion of post IDs in filenames to prevent name collisions
- **Visual Status** – Color-coded table rows show which posts have original Markdown
- **No Dependencies** – Works with ZipArchive or falls back to PclZip automatically

## Requirements

- WordPress 4.7 or higher
- PHP 7.0 or higher

## Installation

### Method 1: Upload via WordPress Admin

1. Download the plugin ZIP file to your computer
2. In WordPress admin, go to **Plugins → Add New**
3. Click the **Upload Plugin** button at the top of the page
4. Click **Choose File**, select the `markdown-exporter.zip` file
5. Click **Install Now**
6. After installation, click **Activate Plugin**

### Method 2: Manual FTP Installation

1. Extract the `markdown-exporter` folder from the ZIP archive
2. Upload the `markdown-exporter` folder to `/wp-content/plugins/` on your WordPress server
3. Go to **Plugins** in your WordPress admin panel
4. Find **Markdown Exporter** in the plugins list and click **Activate**

## Usage

1. Navigate to **Tools → Markdown Export** in your WordPress admin panel
2. Select the posts or pages you want to export
3. Choose whether to include post IDs in filenames (recommended for avoiding duplicate names)
4. Click **Export as ZIP**

The plugin will generate a ZIP file containing:
- `.md` files for posts with stored Markdown
- `.html` files for posts without stored Markdown

## Supported Markdown Meta Keys

The plugin checks for raw Markdown content in the following post meta keys (in order):

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
