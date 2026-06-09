<?php
/**
 * Plugin Name: Markdown Exporter
 * Plugin URI:  https://henchat.net/
 * Description: 导出所选字段为 Markdown 文件并打包为 ZIP，支持多选导出。默认导出 post_content_filtered，亦可选择 post_content 或文章 Meta。导出文件统一为 .md（无回退）。
 * Version:     0.0.1
 * Author:      CH3COOOH
 * Author URI:  https://henchat.net/
 * License:     GPLv3
 * Text Domain: wp-markdown-exporter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_Editor_MD_Markdown_Exporter {
    const MENU_SLUG = 'wp-editor-md-markdown-exporter';
    const NONCE_ACTION = 'wp_editor_md_markdown_exporter_action';
    const NONCE_FIELD = 'wp_editor_md_markdown_exporter_nonce';

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_post_wp_editor_md_markdown_export', array( $this, 'handle_export_request' ) );
    }

    public function add_admin_menu() {
        add_management_page(
            __( 'Markdown 导出', 'wp-editor-md-markdown-exporter' ),
            __( 'Markdown 导出', 'wp-editor-md-markdown-exporter' ),
            'export',
            self::MENU_SLUG,
            array( $this, 'render_admin_page' )
        );
    }

    

    public function render_admin_page() {
        $posts = get_posts( array(
            'posts_per_page' => -1,
            'post_type'      => array( 'post', 'page' ),
            'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
            'orderby'        => 'post_date',
            'order'          => 'DESC',
        ) );

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Markdown Exporter', 'wp-editor-md-markdown-exporter' ); ?></h1>
            <p><?php esc_html_e( '从 WordPress 中选择文章，将所选字段的内容打包为 ZIP 下载。列表仅显示 post_content 非空的文章。', 'wp-editor-md-markdown-exporter' ); ?></p>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD ); ?>
                <input type="hidden" name="action" value="wp_editor_md_markdown_export">

                <p>
                    <button type="button" class="button" id="select-all-posts"><?php esc_html_e( '全选', 'wp-editor-md-markdown-exporter' ); ?></button>
                    <button type="button" class="button" id="deselect-all-posts"><?php esc_html_e( '取消全选', 'wp-editor-md-markdown-exporter' ); ?></button>
                    <button type="submit" class="button button-primary"><?php esc_html_e( '导出为 ZIP', 'wp-editor-md-markdown-exporter' ); ?></button>
                </p>
                <p>
                    <label>
                        <input type="checkbox" name="include_id" value="1" checked>
                        <?php esc_html_e( '导出文件名包含文章 ID（建议用于避免重名）', 'wp-editor-md-markdown-exporter' ); ?>
                    </label>
                </p>

                <p>
                    <label for="export_field">
                        <?php esc_html_e( '选择导出字段（无回退）：', 'wp-editor-md-markdown-exporter' ); ?>
                    </label>
                    <select name="export_field" id="export_field">
                        <option value="post_content_filtered" selected><?php esc_html_e( 'post_content_filtered（默认）', 'wp-editor-md-markdown-exporter' ); ?></option>
                        <option value="post_content"><?php esc_html_e( 'post_content', 'wp-editor-md-markdown-exporter' ); ?></option>
                        <option value="meta"><?php esc_html_e( '文章 Meta', 'wp-editor-md-markdown-exporter' ); ?></option>
                    </select>
                    <br>
                    <em><?php esc_html_e( '选择要导出的字段；若所选字段为空，将导出空文件。导出文件统一为 .md。', 'wp-editor-md-markdown-exporter' ); ?></em>
                </p>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column column-cb"></th>
                            <th scope="col"><?php esc_html_e( '文章标题', 'wp-editor-md-markdown-exporter' ); ?></th>
                            <th scope="col"><?php esc_html_e( '类型', 'wp-editor-md-markdown-exporter' ); ?></th>
                            <th scope="col"><?php esc_html_e( '状态', 'wp-editor-md-markdown-exporter' ); ?></th>
                            <th scope="col"><?php esc_html_e( '发布日期', 'wp-editor-md-markdown-exporter' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 仅显示 post_content 非空的文章
                        $posts = array_filter( $posts, function( $p ) {
                            return '' !== trim( $p->post_content );
                        } );

                        if ( empty( $posts ) ) : ?>
                            <tr>
                                <td colspan="5"><?php esc_html_e( '没有找到文章。', 'wp-editor-md-markdown-exporter' ); ?></td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $posts as $post ) : ?>
                                <tr>
                                    <th scope="row" class="check-column">
                                        <label>
                                            <input type="checkbox" name="post_ids[]" value="<?php echo esc_attr( $post->ID ); ?>">
                                        </label>
                                    </th>
                                    <td><?php echo esc_html( $post->post_title ? $post->post_title : sprintf( '#%d', $post->ID ) ); ?></td>
                                    <td><?php echo esc_html( ucfirst( $post->post_type ) ); ?></td>
                                    <td><?php echo esc_html( ucfirst( $post->post_status ) ); ?></td>
                                    <td><?php echo esc_html( get_date_from_gmt( $post->post_date_gmt, 'Y-m-d H:i' ) ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            </form>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('select-all-posts').addEventListener('click', function () {
                document.querySelectorAll('input[name="post_ids[]"]').forEach(function (checkbox) {
                    checkbox.checked = true;
                });
            });
            document.getElementById('deselect-all-posts').addEventListener('click', function () {
                document.querySelectorAll('input[name="post_ids[]"]').forEach(function (checkbox) {
                    checkbox.checked = false;
                });
            });
        });
        </script>
        <?php
    }

    public function handle_export_request() {
        if ( ! current_user_can( 'export' ) ) {
            wp_die( esc_html__( '权限不足。', 'wp-editor-md-markdown-exporter' ) );
        }

        if ( ! isset( $_POST[ self::NONCE_FIELD ] ) || ! wp_verify_nonce( wp_unslash( $_POST[ self::NONCE_FIELD ] ), self::NONCE_ACTION ) ) {
            wp_die( esc_html__( '安全验证失败。', 'wp-editor-md-markdown-exporter' ) );
        }

        if ( empty( $_POST['post_ids'] ) || ! is_array( $_POST['post_ids'] ) ) {
            wp_redirect( admin_url( 'tools.php?page=' . self::MENU_SLUG ) );
            exit;
        }

        $include_id = isset( $_POST['include_id'] ) ? true : false;
        $post_ids = array_map( 'absint', wp_unslash( $_POST['post_ids'] ) );
        $allowed = array( 'post_content_filtered', 'post_content', 'meta' );
        $export_field = 'post_content_filtered';
        if ( isset( $_POST['export_field'] ) ) {
            $val = sanitize_text_field( wp_unslash( $_POST['export_field'] ) );
            if ( in_array( $val, $allowed, true ) ) {
                $export_field = $val;
            }
        }
        $posts = get_posts( array(
            'post_type'      => array( 'post', 'page' ),
            'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
            'posts_per_page' => -1,
            'post__in'       => $post_ids,
            'orderby'        => 'post__in',
        ) );

        if ( empty( $posts ) ) {
            wp_die( esc_html__( '未找到任何文章。', 'wp-editor-md-markdown-exporter' ) );
        }

        $zip_name = 'markdown-export-' . date( 'Y-m-d-H-i-s' ) . '.zip';
        $tmp_file = wp_tempnam( $zip_name );

        if ( ! $tmp_file ) {
            wp_die( esc_html__( '无法创建临时文件。', 'wp-editor-md-markdown-exporter' ) );
        }

        $zip_created = $this->create_zip( $posts, $tmp_file, $include_id, $export_field );

        if ( ! $zip_created ) {
            wp_die( esc_html__( '无法创建 ZIP 文件。', 'wp-editor-md-markdown-exporter' ) );
        }

        if ( ! file_exists( $tmp_file ) ) {
            wp_die( esc_html__( 'ZIP 文件已丢失。', 'wp-editor-md-markdown-exporter' ) );
        }

        header( 'Content-Type: application/zip' );
        header( 'Content-Disposition: attachment; filename="' . basename( $zip_name ) . '"' );
        header( 'Content-Length: ' . filesize( $tmp_file ) );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        readfile( $tmp_file );
        unlink( $tmp_file );
        exit;
    }

    protected function create_zip( $posts, $tmp_file, $include_id, $export_field = 'post_content_filtered' ) {
        if ( class_exists( 'ZipArchive' ) ) {
            $zip = new ZipArchive();
            if ( true !== $zip->open( $tmp_file, ZipArchive::CREATE ) ) {
                return false;
            }

            $used_file_names = array();
            foreach ( $posts as $post ) {
                $content = $this->get_markdown_content( $post, $export_field );
                $file_name = $this->make_post_filename( $post, $include_id );
                $file_name = $this->make_unique_filename( $file_name, $used_file_names );
                $zip->addFromString( $file_name, $content );
            }

            $zip->close();
            return true;
        }

        if ( ! class_exists( 'PclZip' ) ) {
            require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
        }

        $used_file_names = array();
        $entries = array();
        foreach ( $posts as $post ) {
            $file_name = $this->make_post_filename( $post, $include_id );
            $file_name = $this->make_unique_filename( $file_name, $used_file_names );
            $entries[] = array(
                PCLZIP_ATT_FILE_NAME => $tmp_file,
                PCLZIP_ATT_FILE_CONTENT => $this->get_markdown_content( $post, $export_field ),
                PCLZIP_ATT_FILE_NEW_SHORT_NAME => $file_name,
            );
        }

        $archive = new PclZip( $tmp_file );
        return ( $archive->create( $entries ) !== 0 );
    }

    

    protected function get_markdown_content( $post, $export_field = 'post_content_filtered' ) {
        switch ( $export_field ) {
            case 'meta':
                return $this->get_markdown_from_meta( $post->ID );
            case 'post_content':
                return isset( $post->post_content ) ? $post->post_content : '';
            case 'post_content_filtered':
            default:
                return isset( $post->post_content_filtered ) ? $post->post_content_filtered : '';
        }
    }

    protected function get_markdown_from_meta( $post_id ) {
        $keys = array(
            'editor_md',
            'editormd',
            'editor_md_post',
            'markdown',
            'content_markdown',
            'wp_editor_md',
            'wpedmd_markdown',
            'raw_markdown',
            'md_content',
            'editor_md_content',
            'editor_md_text',
        );

        foreach ( $keys as $key ) {
            $value = get_post_meta( $post_id, $key, true );
            if ( '' !== trim( $value ) ) {
                return $value;
            }
        }

        return '';
    }

    protected function make_post_filename( $post, $include_id ) {
        $name = $post->post_title ? $post->post_title : 'post-' . $post->ID;
        $slug = sanitize_file_name( $name );
        if ( '' === $slug ) {
            $slug = 'post-' . $post->ID;
        }

        $extension = 'md';
        if ( $include_id ) {
            return sprintf( '%s-%d.%s', $slug, $post->ID, $extension );
        }

        return sprintf( '%s.%s', $slug, $extension );
    }

    protected function make_unique_filename( $file_name, &$used_file_names ) {
        if ( ! isset( $used_file_names[ $file_name ] ) ) {
            $used_file_names[ $file_name ] = 1;
            return $file_name;
        }

        $count = $used_file_names[ $file_name ];
        $parts = pathinfo( $file_name );
        do {
            $count++;
            $new_name = sprintf( '%s-%d.%s', $parts['filename'], $count, $parts['extension'] );
        } while ( isset( $used_file_names[ $new_name ] ) );

        $used_file_names[ $file_name ] = $count;
        $used_file_names[ $new_name ] = 1;

        return $new_name;
    }
}

new WP_Editor_MD_Markdown_Exporter();
